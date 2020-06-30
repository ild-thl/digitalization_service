<?php

namespace App\Http\Controllers;

use App\ElmoElement;
use App\ElmoKey;
use App\KeyAssignment;
use App\XmlElement;
use App\XmlElmoHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use App\SimpleXmlElementExtended;
use SimpleXMLElement;

class TransformController extends Controller
{

    private $elmoElements = array();

    /**
     * Returns view for transformation start
     *
     * @return \Illuminate\Http\Response
     */
    public function transformStart(Request $request)
    {
        return view('transform/start', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Returns view for transformation assignment
     * 
     * @param Request 
     * 
     * @return \Illuminate\Http\Response
     */
    public function transformAssign(Request $request) 
    {
        $validatedData = $request->validate([
            'issuerTitle' => 'required|min:1|string|max:191',
            'issuerIdentifier' => 'required|string|min:1|max:191',
            'issuerUrl' => 'required|url|min:1|max:191',
            'xmlFile' => 'required_if:xmlText,""|file|mimetypes:text/html,text/plain,text/xml,aplication/xml,xml',
            'xmlText' => 'required_if:xmlFile.*,null'
        ]);

        $user = Auth::user();

        // save issuer title
        if (strcmp($user->issuer_title, $request->issuerTitle) !== 0) {
            $user->issuer_title = $request->issuerTitle;
            $user->save();
        }

        // save issuer identifier
        if (strcmp($user->issuer_identifier, $request->issuerIdentifier) !== 0) {
            $user->issuer_identifier = $request->issuerIdentifier;
            $user->save();
        }

        // save issuer url
        if (strcmp($user->issuer_url, $request->issuerUrl) !== 0) {
            $user->issuer_url = $request->issuerUrl;
            $user->save();
        }

        $xml_string = "";
        $xml_errors = array();
        $elmo_keys = array();
        $xml_tags = array();
        $xml = null;
        $xml_uploaded_filename = "";
        $module_tags = array();
        $module_count = array();
        $modulepart_tags = array();

        if($request->hasFile('xmlFile') && $request->file('xmlFile')->isValid()) {
            $xml_string = file_get_contents($request->file('xmlFile')->path());
        } elseif(strlen(trim($request->xmlText)) > 0) {
            $xml_string = $request->xmlText;
        }

        $xml_errors = XmlElmoHelper::getXmlErrors($xml_string);

        if(sizeof($xml_errors) == 0) {
            $elmo_keys = ElmoKey::orderBy('title')->get();
            $xml = new SimpleXMLElement($xml_string);

            $xml_tags = XmlElmoHelper::getAllXmlTags($xml, $xml->getName());
            usort($xml_tags, function($a, $b)
            {
                $strcmpParentTags = strcmp(strtolower($a->getParentTag()), strtolower($b->getParentTag()));
                if($strcmpParentTags === 0) {
                    return strcmp(strtolower($a->getTag()), strtolower($b->getTag()));
                }
                return $strcmpParentTags;
            });

            $module_tags = XmlElmoHelper::getAllXmlTagsByLevel($xml, $xml->getName(), 1);
            usort($module_tags, function($a, $b)
            {
                return strcmp(strtolower($a->getTag()), strtolower($b->getTag()));
            });

            $modulepart_tags = XmlElmoHelper::getAllXmlTagsByLevel($xml, $xml->getName(), 2, 1);
            usort($modulepart_tags, function($a, $b)
            {
                return strcmp(strtolower($a->getTag()), strtolower($b->getTag()));
            });

            $xml_tags = XmlElmoHelper::uniqueXmlTags($xml_tags);
            $module_tags = XmlElmoHelper::uniqueXmlTags($module_tags);
            $modulepart_tags = XmlElmoHelper::uniqueXmlTags($modulepart_tags);

            $module_count = XmlElmoHelper::countModule($module_tags, $xml_string);

            $xml_uploaded_filename = '/tmp/xml/uploaded'. uniqid('xmlfile_').'.xml';
            
            Storage::disk('local')->put($xml_uploaded_filename, $xml_string);
        }
        
        return view('transform/assign', [
            'xml_errors' => $xml_errors,
            'elmo_keys' => $elmo_keys,
            'xml_tags' => $xml_tags,
            'xml_uploaded_filename' => $xml_uploaded_filename,
            'module_tags' => $module_tags,
            'module_count' => $module_count,
            'modulepart_tags' => $modulepart_tags,
        ]);
    }

    /**
     * Returns view for transformation creation
     * 
     * @param Request
     * 
     * @return \Illuminate\Http\Response
     */
    public function transformCreate(Request $request) 
    {
        $elmoSchemaPath = base_path() . "/app/schema/elmo.xsd";

        $validatedData = $request->validate([
            'xml_uploaded_filename' => 'required|string|min:1',
            'selectModuleTag' => 'required|string|min:1',
            'selectModulePartTag' => 'required|string|min:1',
            'parentTag.*' => 'required|string|min:1',
            'tag.*' => 'required|string|min:1',
            'assignElmoKey.*' => 'required|int'
        ]);

        if(!Storage::disk('local')->exists($request->xml_uploaded_filename)) {
            return redirect()->route('transform');
        }

        Cookie::queue("selectedModuleTag", $request->selectModuleTag);
        Cookie::queue("selectedModulePartTag", $request->selectModulePartTag);

        $xml_string = Storage::disk('local')->get($request->xml_uploaded_filename);
        Storage::disk('local')->delete($request->xml_uploaded_filename);
        $xml = new SimpleXMLElement($xml_string);

        $transformArray = $this->getTransformArray($request->parentTag, $request->tag, $request->assignElmoKey);

        $splitted = XmlElmoHelper::splitXml($xml, $request->selectModuleTag);

        $private_keyfile = base_path() . env('EMREX_PRIVATE_KEY');
        $public_keyfile = base_path() . env('EMREX_PUBLIC_KEY');
        $sign_password = env('EMREX_PASSWORD');

        $i = 0;
        foreach($splitted as $modulXml) {
            $filename = uniqid('elmofile_');
            $this->elmoElements[$i] = new ElmoElement("tmp/elmo/unsigned/unsigned_" . $filename . ".xml", "tmp/elmo/signed/" . $filename . ".xml");

            $this->createModuleXml($i, $modulXml, $transformArray, $request->selectModulePartTag);
            XmlElmoHelper::saveXmlFile($this->elmoElements[$i]->getFilenameUnsigned(), $this->elmoElements[$i]->xml->asXML());

            $this->elmoElements[$i]->setXmlErrors(XmlElmoHelper::getXmlErrors($this->elmoElements[$i]->xml->asXml()));
            if($this->elmoElements[$i]->isXmlValid()) {

                    XmlElmoHelper::signEmrexDocument($this->elmoElements[$i]->getFilenameUnsigned(), $this->elmoElements[$i]->getFilenameSigned(), $private_keyfile, $sign_password);
                    $this->elmoElements[$i]->setSignatureValid(XmlElmoHelper::validateEmrexSignature($this->elmoElements[$i]->getFilenameSigned(), $public_keyfile));

                    $this->elmoElements[$i]->setXmlSchemaErrors(XmlElmoHelper::getXmlSchemaErrors($this->elmoElements[$i]->getFilenameSigned(), $elmoSchemaPath));
            }
            $i++;
        }

        $zip_filename = __('digiserv.transform') . "_" . date("Y-m-d-H-i-s") . ".zip";
        $zip_path = "elmo/zip/" . $zip_filename;
        $zip_storage_path = storage_path("app/public/" . $zip_path);

        $xmlError = false;
        foreach($this->elmoElements as $elmoElement) {
            if(!$elmoElement->isXmlValid()) {
                $xmlError = true;
                break;
            }
        }

        $signatureError = false;
        foreach($this->elmoElements as $elmoElement) {
            if(!$elmoElement->isSignatureValid()) {
                $signatureError = true;
                break;
            }
        }

        $xmlSchemaError = false;
        foreach($this->elmoElements as $elmoElement) {
            if(!$elmoElement->isXmlSchemaValid()) {
                $xmlSchemaError = true;
                break;
            }
        }

        if(sizeof($this->elmoElements) > 0 && !$xmlError && !$signatureError && !$xmlSchemaError) {
            $zip = new \ZipArchive();
            
            Storage::disk('local')->makeDirectory("public/elmo/zip/", 0755, true);

            if($zip->open($zip_storage_path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
                foreach($this->elmoElements as $elmoElement) {
                    if(file_exists(storage_path("app/" . $elmoElement->getFilenameSigned()))) {
                        $zip->addFile(storage_path("app/" . $elmoElement->getFilenameSigned()), "" . $elmoElement->getBasenameSigned());
                    }
                }
                if(!$zip->close()) {
                    $zip_filename = "";
                    $zip_path = "";
                }
            } else {
                $zip_filename = "";
                $zip_path = "";
            }            
        }

        foreach($this->elmoElements as $elmoElement) {
            Storage::disk('local')->delete($elmoElement->getFilenameUnsigned());
            Storage::disk('local')->delete($elmoElement->getFilenameSigned());
        }

        return view('transform/create', [
            "elmoElements" => $this->elmoElements,
            "zip_filename" => $zip_filename,
            "zip_path" => $zip_path,
            "xmlError" => $xmlError,
            "signatureError" => $signatureError,
            "xmlSchemaError" => $xmlSchemaError,
        ]);
    }

    /**
     * Returns file to download and deletes this file after download is finished
     * 
     * @param Request path Path of File to download
     * 
     * @return \Illuminate\Http\Response
     */
    public function transformDownload(Request $request) {
        $validatedData = $request->validate([
            'path' => 'required|string|min:1'
        ]);

        if(Storage::disk('local')->exists("public/" . $request->path)) {
            return response()->download(storage_path("app/public/" . $request->path))->deleteFileAfterSend();
        } else {
            return response('File not found', 404);
        }
    }

    /**
     * Creates the ELMO XML for a single Module
     * 
     * @param xmlSep Index of current Module in the elmoElements Array
     * @param moduleXML SimpleXmlElement of the uploaded XML file splitted by modules
     * @param transformArray Array of XmlElement Objects with information on which XML tag with a given parent tag name should be matched to which ELMO key
     * @param modulePartTag Name of XML tag that contains the courses of a module
     */
    protected function createModuleXml(int $xmlSeq, SimpleXmlElement $moduleXML, Array $transformArray, String $modulePartTag) {

        $user = Auth::user();

        $course_ects = false;

        $this->elmoElements[$xmlSeq]->xml = new SimpleXmlElementExtended("<?xml version=\"1.0\" encoding=\"utf-8\"?>
                                                        <elmo xmlns=\"https://github.com/emrex-eu/elmo-schemas/tree/v1\" xmlns:xml=\"http://www.w3.org/XML/1998/namespace\" 
                                                        xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" 
                                                        xmlns:er=\"https://github.com/ilydlci/elmo-schemas\" xsi:schemaLocation=\"https://github.com/emrex-eu/elmo-schemas/tree/v1 
                                                        https://raw.githubusercontent.com/emrex-eu/elmo-schemas/v1/schema.xsd https://github.com/ilydlci/elmo-schemas 
                                                        https://raw.githubusercontent.com/ilydlci/elmo-schemas/v1/elmoRecog.xsd\"></elmo>");

        // elmo -> generatedDate
        $this->elmoElements[$xmlSeq]->xml->addChild("generatedDate", date('Y-m-d\TH:i:sP'));
        // END elmo -> generatedDate

        // elmo -> learner
        $learner = $this->elmoElements[$xmlSeq]->xml->addChild("learner");
        $learner->addChild("givenNames", "");
        $learner->addChild("familyName", "");
        // END elmo -> learner

        // elmo -> report
        $report = $this->elmoElements[$xmlSeq]->xml->addChild("report");

        // elmo -> report -> issuer
        $issuer = $report->addChild("issuer");

        $issueridentifier = $issuer->addChild("identifier", $user->issuer_identifier);
        $issueridentifier->addAttribute("type","erasmus");

        $issuertitle = $issuer->addChild("title", $user->issuer_title);
        $issuertitle->addAttribute("xml:lang", "de", "http://www.w3.org/XML/1998/namespace");

        $issuer->addChild("url", $user->issuer_url);
        // END elmo -> report -> issuer

        // elmo -> report -> learningOpportunitySpecification
        $learningOpportunitySpecification = $report->addChild("learningOpportunitySpecification");

        // Module ID
        $los_identifier_content = $this->getContentByElmoKey("Modul_Identifier", $transformArray, $moduleXML);
        if(trim($los_identifier_content) != "") {
            $los_identifier = $learningOpportunitySpecification->addChild("identifier", $los_identifier_content);
            $los_identifier->addAttribute("type", "local");
        }

        // Module Title
        $los_title_de_content = $this->getContentByElmoKey("Modulname_Deutsch", $transformArray, $moduleXML);
        $los_title_en_content = $this->getContentByElmoKey("Modulname_Englisch", $transformArray, $moduleXML);
        if(trim($los_title_de_content) != "" && trim($los_title_en_content) != "") {
            if(trim($los_title_de_content) != "") {
                $los_title_de = $learningOpportunitySpecification->addChild("title", $los_title_de_content);
                $los_title_de->addAttribute("xml:lang", "de", "http://www.w3.org/XML/1998/namespace");
            }
            
            if(trim($los_title_en_content) != "") {
                $los_title_en = $learningOpportunitySpecification->addChild("title", $los_title_en_content);
                $los_title_en->addAttribute("xml:lang", "en", "http://www.w3.org/XML/1998/namespace");
            }
        } else {
            $learningOpportunitySpecification->addChild("title", "");
        }

        // Module Type
        $learningOpportunitySpecification->addChild("type", "Module");

        // Module subjectArea
        $los_subjectArea_content = $this->getContentByElmoKey("Modul_subjectArea", $transformArray, $moduleXML);
        if(trim($los_subjectArea_content) != "") {
            $learningOpportunitySpecification->addChild("subjectArea", $los_subjectArea_content);
        }

        // Module iscedCode
        $los_iscedCode_content = $this->getContentByElmoKey("Modul_iscedCode", $transformArray, $moduleXML);
        if(trim($los_iscedCode_content) != "") {
            $learningOpportunitySpecification->addChild("iscedCode", $los_iscedCode_content);
        }

        // Module URL
        $los_url_content = $this->getContentByElmoKey("Modul_Url", $transformArray, $moduleXML);
        if(trim($los_url_content) != "") {
            $learningOpportunitySpecification->addChild("url", $los_url_content);
        }

        // Module Description
        $los_desc_de_content = $this->getContentByElmoKey("Modul_Beschreibung_Deutsch", $transformArray, $moduleXML);
        $los_desc_en_content = $this->getContentByElmoKey("Modul_Beschreibung_Englisch", $transformArray, $moduleXML);
        $los_desc_html_de_content = $this->getContentByElmoKey("Modul_Beschreibung_HTML_Deutsch", $transformArray, $moduleXML);
        $los_desc_html_en_content = $this->getContentByElmoKey("Modul_Beschreibung_HTML_Englisch", $transformArray, $moduleXML);

        if(trim($los_desc_de_content) != "" || trim($los_desc_en_content) != "" || trim($los_desc_html_de_content) != "" || trim($los_desc_html_en_content) != "") {

            if(trim($los_desc_de_content) == "" && trim($los_desc_html_de_content) != "") 
            {
                $los_desc_de = $learningOpportunitySpecification->addChild("description", strip_tags($los_desc_html_de_content));
                $los_desc_de->addAttribute("xml:lang", "de", "http://www.w3.org/XML/1998/namespace");

                $los_desc_html_de = $learningOpportunitySpecification->addChild("descriptionHtml");
                $los_desc_html_de->addCData($los_desc_html_de_content);
                $los_desc_html_de->addAttribute("xml:lang", "de", "http://www.w3.org/XML/1998/namespace");

            } 
            elseif(trim($los_desc_de_content) != "" && trim($los_desc_html_de_content) == "") 
            {
                $los_desc_de = $learningOpportunitySpecification->addChild("description", $los_desc_de_content);
                $los_desc_de->addAttribute("xml:lang", "de", "http://www.w3.org/XML/1998/namespace");
            } 
            elseif(trim($los_desc_de_content) != "" && trim($los_desc_html_de_content) != "") 
            {
                $los_desc_de = $learningOpportunitySpecification->addChild("description", $los_desc_de_content);
                $los_desc_de->addAttribute("xml:lang", "de", "http://www.w3.org/XML/1998/namespace");

                $los_desc_html_de = $learningOpportunitySpecification->addChild("descriptionHtml");
                $los_desc_html_de->addCData($los_desc_html_de_content);
                $los_desc_html_de->addAttribute("xml:lang", "de", "http://www.w3.org/XML/1998/namespace");

            }

            if(trim($los_desc_en_content) == "" && trim($los_desc_html_en_content) != "") 
            {
                $los_desc_en = $learningOpportunitySpecification->addChild("description", strip_tags($los_desc_html_en_content));
                $los_desc_en->addAttribute("xml:lang", "en", "http://www.w3.org/XML/1998/namespace");

                $los_desc_html_en = $learningOpportunitySpecification->addChild("descriptionHtml");
                $los_Desc_html_en->addCData($los_desc_html_en_content);
                $los_desc_html_en->addAttribute("xml:lang", "en", "http://www.w3.org/XML/1998/namespace");

            } 
            elseif(trim($los_desc_en_content) != "" && trim($los_desc_html_en_content) == "") 
            {
                $los_desc_en = $learningOpportunitySpecification->addChild("description", $los_desc_en_content);
                $los_desc_en->addAttribute("xml:lang", "en", "http://www.w3.org/XML/1998/namespace");
            } 
            elseif(trim($los_desc_en_content) != "" && trim($los_desc_html_en_content) != "") 
            {
                $los_desc_en = $learningOpportunitySpecification->addChild("description", $los_desc_en_content);
                $los_desc_en->addAttribute("xml:lang", "en", "http://www.w3.org/XML/1998/namespace");

                $los_desc_html_en = $learningOpportunitySpecification->addChild("descriptionHtml");
                $los_desc_html_en->addCData($los_desc_html_en_content);
                $los_desc_html_en->addAttribute("xml:lang", "en", "http://www.w3.org/XML/1998/namespace");

            }
        }
        // END elmo -> report -> learningOpportunitySpecification

        $specifies = $learningOpportunitySpecification->addChild("specifies");
        $learningOpportunityInstance = $specifies->addChild("learningOpportunityInstance");
        // elmo -> report -> learningOpportunitySpecification -> specifies -> learningOpportunityInstance

        // Module Language (if mix of languages then the element should not be present - see schema.xsd documentation)
        $los_language_content = $this->getContentByElmoKey("Modul_Sprache", $transformArray, $moduleXML);
        if(trim($los_language_content) != "" && strcmp(strtolower($los_language_content), "deutsch") === 0 || strcmp(strtolower($los_language_content), "german") === 0) {
            $learningOpportunityInstance->addChild("languageOfInstruction", "de");
        }
        if(trim($los_language_content) != "" && strcmp(strtolower($los_language_content), "englisch") === 0 || strcmp(strtolower($los_language_content), "english") === 0) {
            $learningOpportunityInstance->addChild("languageOfInstruction", "en");
        }
        // END elmo -> report -> learningOpportunitySpecification -> specifies -> learningOpportunityInstance

        $courseSplitted = XmlElmoHelper::splitXml($moduleXML, $modulePartTag, true);

        // elmo -> report -> learningOpportunitySpecification -> hasPart ->learningOpportunitySpecification for each Course
        foreach($courseSplitted as $courseXml) {
            $hasPart = $learningOpportunitySpecification->addChild("hasPart");
            $c_los = $hasPart->addChild("learningOpportunitySpecification");

            // Course ID
            $c_los_identifier_content = $this->getContentByElmoKey("Kurs_Identifier", $transformArray, $courseXml);
            if(trim($c_los_identifier_content) != "") {
                $c_los_identifier = $c_los->addChild("identifier", $c_los_identifier_content);
                $c_los_identifier->addAttribute("type", "local");
            }
            
            // Course Title
            $c_title_de_content = $this->getContentByElmoKey("Kursname_Deutsch", $transformArray, $courseXml);
            $c_title_en_content = $this->getContentByElmoKey("Kursname_Englisch", $transformArray, $courseXml);
            if(trim($c_title_de_content) != "" && trim($c_title_en_content) != "") {
                if(trim($c_title_de_content) != "") {
                    $c_los_title_de = $c_los->addChild("title", $c_title_de_content);
                    $c_los_title_de->addAttribute("xml:lang", "de", "http://www.w3.org/XML/1998/namespace");
                }
                
                if(trim($c_title_en_content) != "") {
                    $c_los_title_en = $c_los->addChild("title", $c_title_en_content);
                    $c_los_title_en->addAttribute("xml:lang", "en", "http://www.w3.org/XML/1998/namespace");
                }
            } else {
                $c_los->addChild("title", "");
            }

            // Course Type
            $c_los->addChild("type", "Course");

            // Course subjectArea
            $c_los_subjectArea_content = $this->getContentByElmoKey("Kurs_subjectArea", $transformArray, $courseXml);
            if(trim($c_los_subjectArea_content) != "") {
                $c_los->addChild("subjectArea", $c_los_subjectArea_content);
            }

            // Course iscedCode
            $c_los_iscedCode_content = $this->getContentByElmoKey("Kurs_iscedCode", $transformArray, $courseXml);
            if(trim($c_los_iscedCode_content) != "") {
                $c_los->addChild("iscedCode", $c_los_iscedCode_content);
            }

            // Course URL
            $c_los_url_content = $this->getContentByElmoKey("Kurs_Url", $transformArray, $courseXml);
            if(trim($c_los_url_content) != "") {
                $c_los->addChild("url", $c_los_url_content);
            }

            // Course Description
            $c_los_desc_de_content = $this->getContentByElmoKey("Kurs_Beschreibung_Deutsch", $transformArray, $courseXml);
            $c_los_desc_en_content = $this->getContentByElmoKey("Kurs_Beschreibung_Englisch", $transformArray, $courseXml);
            $c_los_desc_html_de_content = $this->getContentByElmoKey("Kurs_Beschreibung_HTML_Deutsch", $transformArray, $courseXml);
            $c_los_desc_html_en_content = $this->getContentByElmoKey("Kurs_Beschreibung_HTML_Englisch", $transformArray, $courseXml);

            if(trim($c_los_desc_de_content) != "" || trim($c_los_desc_en_content) != "" || trim($c_los_desc_html_de_content) != "" || trim($c_los_desc_html_en_content) != "") {

                if(trim($c_los_desc_de_content) == "" && trim($c_los_desc_html_de_content) != "") 
                {
                    $c_los_desc_de = $c_los->addChild("description", strip_tags($c_los_desc_html_de_content));
                    $c_los_desc_de->addAttribute("xml:lang", "de", "http://www.w3.org/XML/1998/namespace");

                    $c_los_desc_html_de = $c_los->addChild("descriptionHtml", $c_los_desc_html_de_content);
                    $c_los_desc_html_de->addAttribute("xml:lang", "de", "http://www.w3.org/XML/1998/namespace");
                } 
                elseif(trim($c_los_desc_de_content) != "" && trim($c_los_desc_html_de_content) == "") 
                {
                    $c_los_desc_de = $c_los->addChild("description", $c_los_desc_de_content);
                    $c_los_desc_de->addAttribute("xml:lang", "de", "http://www.w3.org/XML/1998/namespace");
                } 
                elseif(trim($c_los_desc_de_content) != "" && trim($c_los_desc_html_de_content) != "") 
                {
                    $c_los_desc_de = $c_los->addChild("description", $c_los_desc_de_content);
                    $c_los_desc_de->addAttribute("xml:lang", "de", "http://www.w3.org/XML/1998/namespace");

                    $c_los_desc_html_de = $c_los->addChild("descriptionHtml", $c_los_desc_html_de_content);
                    $c_los_desc_html_de->addAttribute("xml:lang", "de", "http://www.w3.org/XML/1998/namespace");
                }

                if(trim($c_los_desc_en_content) == "" && trim($c_los_desc_html_en_content) != "") 
                {
                    $c_los_desc_en = $c_los->addChild("description", strip_tags($c_los_desc_html_en_content));
                    $c_los_desc_en->addAttribute("xml:lang", "en", "http://www.w3.org/XML/1998/namespace");
                    
                    $c_los_desc_html_en = $c_los->addChild("descriptionHtml", $c_los_desc_html_en_content);
                    $c_los_desc_html_en->addAttribute("xml:lang", "en", "http://www.w3.org/XML/1998/namespace");
                } 
                elseif(trim($c_los_desc_en_content) != "" && trim($c_los_desc_html_en_content) == "") 
                {
                    $c_los_desc_en = $c_los->addChild("description", $c_los_desc_en_content);
                    $c_los_desc_en->addAttribute("xml:lang", "en", "http://www.w3.org/XML/1998/namespace");
                } 
                elseif(trim($c_los_desc_en_content) != "" && trim($c_los_desc_html_en_content) != "") 
                {
                    $c_los_desc_en = $c_los->addChild("description", $c_los_desc_en_content);
                    $c_los_desc_en->addAttribute("xml:lang", "en", "http://www.w3.org/XML/1998/namespace");
                    
                    $c_los_desc_html_en = $c_los->addChild("descriptionHtml", $c_los_desc_html_en_content);
                    $c_los_desc_html_en->addAttribute("xml:lang", "en", "http://www.w3.org/XML/1998/namespace");
                }
            }

            $c_specifies = $c_los->addChild("specifies");
            $c_loi = $c_specifies->addChild("learningOpportunityInstance");
            // elmo -> report -> learningOpportunitySpecification -> hasPart ->learningOpportunitySpecification -> specifies -> learningOpportunityInstance
            
            // Course Niveau and ECTS Points (Level-Tag is deprecated) - Niveau comes from Module
            $c_loi_ects_points = $this->getContentByElmoKey("Kurs_ECTS_Punkte", $transformArray, $courseXml);
            $los_niveau = $this->getContentByElmoKey("Modul_Niveau", $transformArray, $moduleXML);
            if(trim($c_loi_ects_points) != "" && is_numeric($c_loi_ects_points) && trim($los_niveau) != "" && (strcmp($los_niveau, "Master") === 0 || strcmp($los_niveau, "Bachelor") === 0)) {
                $c_los_credit = $c_loi->addChild("credit");
                $c_los_credit->addChild("scheme", "ects");
                $c_los_credit->addChild("level", $los_niveau);
                $c_los_credit->addChild("value", $c_loi_ects_points);

                $course_ects = true;
            }

            // Course Language (if mix of languages then the element should not be present - see schema.xsd documentation)
            $c_loi_language_content = $this->getContentByElmoKey("Kurs_Sprache", $transformArray, $courseXml);
            if(trim($c_loi_language_content) != "" && strcmp(strtolower($c_loi_language_content), "deutsch") === 0 || strcmp(strtolower($c_loi_language_content), "german") === 0) {
                $c_loi->addChild("languageOfInstruction", "de");
            }
            if(trim($c_loi_language_content) != "" && strcmp(strtolower($c_loi_language_content), "englisch") === 0 || strcmp(strtolower($c_loi_language_content), "english") === 0) {
                $c_loi->addChild("languageOfInstruction", "en");
            }

            // Course engagement hours
            $c_loi_engagement_hours_content = $this->getContentByElmoKey("Kurs_Arbeitsaufwand_Stunden", $transformArray, $courseXml);
            if(trim($c_loi_engagement_hours_content) != "" && is_numeric($c_loi_engagement_hours_content)) {
                $c_loi->addChild("engagementHours", $c_loi_engagement_hours_content);
            }
            // END elmo -> report -> learningOpportunitySpecification -> hasPart ->learningOpportunitySpecification -> specifies -> learningOpportunityInstance
        }
        // END elmo -> report -> learningOpportunitySpecification -> hasPart ->learningOpportunitySpecification

        // Only if Course does not have credit points (ects points are only valid in the child element, otherwise the points would be given twice)
        if(!$course_ects) {
            // Module Niveau and ECTS Points (Level-Tag is deprecated)
            $los_ects_points = $this->getContentByElmoKey("Modul_ECTS_Punkte", $transformArray, $moduleXML);
            $los_niveau = $this->getContentByElmoKey("Modul_Niveau", $transformArray, $moduleXML);
            if(trim($los_ects_points) != "" && is_numeric($los_ects_points) && trim($los_niveau) != "" && (strcmp($los_niveau, "Master") === 0 || strcmp($los_niveau, "Bachelor") === 0)) {
                $los_credit = $learningOpportunityInstance->addChild("credit");
                $los_credit->addChild("scheme", "ects");
                $los_credit->addChild("level", $los_niveau);
                $los_credit->addChild("value", $los_ects_points);
            }
        }

        $report->addChild("issueDate", date('Y-m-d\TH:i:sP'));
        // END elmo -> report
    }

    /**
     * Matches the parent tag names, tag names and ELMO keys into a single XmlElement Object
     * 
     * @param parentTags Array of parent tag names
     * @param tags Array of tag names
     * @param assignElmoKeys Array of ElmoKey IDs
     * 
     * @return Array of XmlElement Objects
     */
    protected function getTransformArray(Array $parentTags = array(), Array $tags = array(), Array $assignElmoKeys = array()) {
        $ret = array();

        if(sizeof($parentTags) == sizeof($tags) && sizeof($parentTags) == sizeof($assignElmoKeys)) {
            foreach(array_keys($parentTags) as $i) {
                if($assignElmoKeys[$i] >= 0) {
                    $ret[] = new XmlElement($parentTags[$i], $tags[$i], "", $assignElmoKeys[$i], ElmoKey::find($assignElmoKeys[$i])->title);

                    if(KeyAssignment::where('parent', $parentTags[$i])->where('tag', $tags[$i])->count() == 1) {
                        
                        $keyAssignment = KeyAssignment::where('parent', $parentTags[$i])->where('tag', $tags[$i])->first();
                        $keyAssignment->updated_at = now();
                        $keyAssignment->save();


                    } else {
                        $keyAssignment = new KeyAssignment;
                        $keyAssignment->parent = $parentTags[$i];
                        $keyAssignment->tag = $tags[$i];
                        $keyAssignment->elmo_key_id = $assignElmoKeys[$i];

                        $keyAssignment->save();
                    }
                }
            }
        }

        return $ret;
    }

    /**
     * Returns the content of a specific ELMO key with the assignments in the transform Array of a XML document.
     * If there is more than one result, the results are stitched together by a white space
     * 
     * @param elmokey ELMO key to search for
     * @param transformArray Array with XmlElement Objects and the assignments between ELMO key and parent tags/tags
     * @param xml SimpleXmlElement to search in
     * 
     * @return String content
     */
    protected function getContentByElmoKey(String $elmokey, Array $transformArray, SimpleXmlElement $xml) {
        
        $content = array();
        $found = array();
        
        foreach($transformArray as $element) {
            if(strcmp($element->getElmoKeyTitle(), $elmokey) === 0) {
                $found[] = $element;
            }
        }

        foreach($found as $f) {
            $content = array_merge($content, XmlElmoHelper::getContentByTag($xml, $xml->getName(), $f->getParentTag(), $f->getTag()));
        }
        
        return implode(" ", $content);
    }
}
