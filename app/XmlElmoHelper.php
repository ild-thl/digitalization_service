<?php

namespace App;

use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;

use Selective\XmlDSig\DigestAlgorithmType;
use Selective\XmlDSig\XmlSigner;
use Selective\XmlDSig\XmlSignatureValidator;

class XmlElmoHelper {

    /**
     * Tags that should be ignored in the XML loops - typically HTML tags
     */
    private static $ignoreTags = [
        'html',
        'body',
        'p',
        'ol',
        'ul',
        'li',
        'i',
        'b',
        'u'
    ];

    /**
     * Returns all semantic errors of a XML String
     * 
     * @param xmlstr XML as a String
     * 
     * @return Array Errors-Array with a HTML Error description
     */
    public static function getXmlErrors(String $xmlstr) 
    {
        libxml_use_internal_errors(true);

        $doc = simplexml_load_string($xmlstr);
        $xml = explode("\n", $xmlstr);

        $errors_array = array();

        if($doc === false) {
            $errors = libxml_get_errors();

            foreach ($errors as $error) {
                $errors_array[] = self::returnXmlError($error, $xml);
            }

            libxml_clear_errors();
        }
        
        return $errors_array;
    }

    /**
     * Returns all schematic errors of a XML file
     * 
     * @param xmlpath Path of the XML file to check
     * @param elmoSchemaPath Path of elmo.xsd Schmema
     * 
     * @return Array Errors-Array with a HTML Error description
     */
    public static function getXmlSchemaErrors(String $xmlpath, String $elmoSchemaPath) 
    {
        $errors_array = array();

        $xmldoc = new \DOMDocument();
        if(Storage::disk('local')->exists($xmlpath)) {
            $xmlstr = Storage::disk('local')->get($xmlpath);
            $xmldoc->loadXML($xmlstr);

            $xml = explode("\n", $xmlstr);

            // Enable user error handling
            libxml_use_internal_errors(true);;

            if (!$xmldoc->schemaValidate($elmoSchemaPath)) {
                $errors = libxml_get_errors();

                foreach ($errors as $error) {
                    $errors_array[] = self::returnXmlError($error, $xml);
                }

                libxml_clear_errors();
            }
        } else {
            $errors_array[] = "<b>Fatal Error:</b><br>File does not exist: " . $xmlpath;
        }
        return $errors_array;
    }

    /**
     * Returns a HTML description of a XML Error
     * 
     * @param error XML-Error Object returned by libxml
     * @param xml Array of the XML with each new line is a new array element
     * 
     * @return String HTML Error String
     */
    protected static function returnXmlError($error, $xml)
    {
        $line  = "<code>" . htmlspecialchars($xml[$error->line - 1]) . "</code><br>";
        $line .= str_repeat('-', $error->column) . "^<br>";

        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $line .= "<b>Warning $error->code:</b> ";
                break;
            case LIBXML_ERR_ERROR:
                $line .= "<b>Error $error->code:</b> ";
                break;
            case LIBXML_ERR_FATAL:
                $line .= "<b>Fatal Error $error->code:</b> ";
                break;
        }

        $line .= trim($error->message) .
                "<br>  Line: $error->line" .
                "<br>  Column: $error->column";

        if ($error->file && strpos($error->file, ".xml") !== false) {
            $line .= "<br>  File: $error->file";
        }

        return $line;
    }

    /**
     * Returns all XML tags as custom XmlElement, recursive function
     * 
     * @param xml SimpleXmlElement of the current root node
     * @param parent Name of the parent tag
     * 
     * @return Array of XmlElement-Objects
     */
    public static function getAllXmlTags(SimpleXMLElement $xml, $parent = "")
    {
        $array = [];

        foreach($xml as $child) {

            if (!empty($child) && !in_array($child->getName(), self::$ignoreTags)) {
               
                if(self::hasOnlyHtmlTags($child)) {
                    $array[] = new XmlElement($parent, $child->getName(), $child->asXML());
                }

                if(sizeof($child->children()) > 0) {
                    $array = array_merge($array, self::getAllXmlTags($child->children(), $child->getName()));
                }
            }
        }
        
        return $array;
    }

    /**
     * Returns all XML tags with a level limitation, recursive function
     * 
     * @param xml SimpleXmlElement of the current root node
     * @param parent Name of the parent tag
     * @param maxLevel max depth level
     * @param level current level
     * 
     * @return Array ofXmlElement-Objects
     */
    public static function getAllXmlTagsByLevel(SimpleXMLElement $xml, $parent = "", $maxLevel, $level = 0)
    {
        $l = $level;
        $array = [];
        if($level == 0) {
            $array[] = new XmlElement('', $xml->getName(), "");
        }

        foreach($xml as $child) {

            if (!in_array($child->getName(), self::$ignoreTags)) {

                $array[] = new XmlElement($parent, $child->getName(), "");

                if(sizeof($child->children()) > 0) {
                    if($level < $maxLevel) {
                        $array = array_merge($array, self::getAllXmlTagsByLevel($child->children(), $child->getName(), $maxLevel, ++$l));
                    }
                }
            }
        }
        
        return $array;
    }

    /**
     * Returns an Array with the content of the XML tag search_tag with the parent tag search_parent_tag. Recursive function
     * 
     * @param xml SimpleXmlElement XML Object
     * @param parent_tag Parent tag of current XML node
     * @param search_parent_tag Tag name of parent tag to search for
     * @param search_tag Tag name of tag to search for
     * @param take Force to add XML node to return list 
     *                      (used to get to the content of a node with only html tags in it without having the root node name in the result)
     * 
     * @return Array Results
     */
    public static function getContentByTag(SimpleXmlElement $xml, String $parent_tag = "", String $search_parent_tag, String $search_tag, $take = false) {

        $ret = array();

        foreach($xml as $child) {
            
            if (!empty($child) && !in_array($child->getName(), self::$ignoreTags)) {

                if(strcmp($search_parent_tag, $parent_tag) === 0 && strcmp($search_tag, $child->getName()) === 0) {

                    if($child->count() > 0 && self::hasOnlyHtmlTags($child)) {
                        $ret = array_merge($ret, self::getContentByTag($child, $parent_tag, $search_parent_tag, $search_tag, true));
                    } elseif($child->count() == 0) {
                        $ret[] = $child;
                    }
                }                

                if(sizeof($child->children()) > 0) {
                    $ret = array_merge($ret, self::getContentByTag($child->children(), $child->getName(), $search_parent_tag, $search_tag));
                }
            } elseif($take == true) {
                $ret[] = $child->asXml();
            }
        }

        return $ret;
    }

    /**
     * Beautifies and returns an XML String
     * 
     * @param xmlstr XML as a string
     * 
     * @return String Beautified XML
     */
    public static function beautifyXml($xmlstr) {

        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = false;
        $domxml->formatOutput = true;
        
        $domxml->loadXML($xmlstr);
        return $domxml->saveXml();

    }

    /**
     * Filters a given XmlElements by unique tag names
     * 
     * @param xmlTags Array of XmlElement-Objects
     * 
     * @return Array Array of XmlElement-Objects with unique Tag names
     */
    public static function uniqueXmlTags(Array $xmlTags = array()) {
        $ret = $xmlTags;
        $delete = array();

        for($i = 0; $i < sizeof($ret)-1; $i++) {
            if(isset($ret[$i]) && isset($ret[$i+1]) && 
                $ret[$i]->getParentTag() == $ret[$i+1]->getParentTag() && 
                $ret[$i]->getTag() == $ret[$i+1]->getTag()) {
                    $delete[] = $i;
                }
        }

        foreach($delete as $deleteId) {
            if(isset($ret[$deleteId])) {
                unset($ret[$deleteId]);
            }
        }

        return $ret;
    }

    /**
     * Determines if a Xml Node hast only HTML Tags in it, recursive function
     * 
     * @param xml SimpleXmlElement of the current root node
     * 
     * @return bool 
     */
    protected static function hasOnlyHtmlTags(SimpleXMLElement $xml) 
    {        
        foreach($xml as $child) {
            if(!in_array($child->getName(), self::$ignoreTags)) {
                return false;
            }
            
            if(sizeof($child->children()) > 0) {
                return self::hasOnlyHtmlTags($child->children());
            }
        }
        
        return true;
    }

    /**
     * Counts the number of tags in a XML String, keeps key<->value assignment
     * 
     * @param module_tags Array of Tags as Strings
     * @param xml_string XML String to search in
     * 
     * @return Array with Number of occurance in xml_string
     */
    public static function countModule(Array $module_tags, String $xml_string) {
        $ret = array();

        foreach($module_tags as $module_tag_id => $module_tag) {
            $ret[$module_tag_id] = substr_count(strtolower($xml_string), "<" . strtolower($module_tag->getTag()) . ">");
        }

        return $ret;
    }

    /**
     * Splits a SimpleXmlElement Node by a tag name
     * 
     * @param xml SimpleXmlElement Root Node
     * @param selectedTag String of Tag to split at
     * @param returnChildren If children of tag that matches selectedTag should be returned directly
     * 
     * @return Array of SimpleXmlElement Nodes
     */
    public static function splitXml(SimpleXmlElement $xml, $selectedTag, $returnChildren = false) {

        $ret = array();
        foreach($xml as $child) {
            if(strcmp($child->getName(), $selectedTag) === 0) {
                if($returnChildren) {
                    return $child->children();
                } else {
                    $ret[] = $child;
                }
            }
        }

        return $ret;
    }

    /**
     * Beautifies a XML file
     * 
     * @param filename Path of file to beautify
     */
    public static function beautifyXmlFile($filename) {
        if(Storage::disk('local')->exists($filename)) {
            $xml_unbeautified = Storage::disk('local')->get($filename);

            Storage::disk('local')->put($filename, self::beautifyXml($xml_unbeautified));
        }
    }

    /**
     * Beautifies a XML String and saves the beautified string into a file
     * 
     * @param filename Path of file to save
     * @param content Unbeautified XML String
     */
    public static function saveXmlFile($filename, $content) {
        Storage::disk('local')->put($filename, self::beautifyXml($content));
    }

     /**
     * EMREX Sign function - Signes a XML file and saves the signed XML into a new file
     * 
     * from github oncampus/lift
     * 
     * @param filename_unsigned Path of unsigned XML file
     * @param filename_signed Path of signed XML file
     * @param keyfile Path to .pfx Certificate file
     * @param password Password of private key file
     */
    public static function signEmrexDocument($filename_unsigned, $filename_signed, $keyfile, $password) {
        $filepath_unsigned  = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . $filename_unsigned;
        $filepath_signed  = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix()  .$filename_signed;
    
        $xmlSigner = new XmlSigner();
    
        $xmlSigner->loadPfxFile($keyfile, $password);
        $xmlSigner->signXmlFile($filepath_unsigned, $filepath_signed, 'sha512');
    }
    
    /**
     * EMREX Signature Validation function
     * 
     * from github oncampus/lift
     * 
     * @param filename_signed Path of signed XML file
     * @param public_keyfile Path to public key file
     * 
     * @return bool if signature is valid
     */
    public static function validateEmrexSignature($filename_signed, $public_keyfile) {
    
        $filepath_signed  = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . $filename_signed;

        $signatureValidator = new XmlSignatureValidator();
        $signatureValidator->loadPublicKeyFile($public_keyfile);

        $isValid = $signatureValidator->verifyXmlFile($filepath_signed);

        return $isValid;
    }
} 