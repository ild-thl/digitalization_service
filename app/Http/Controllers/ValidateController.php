<?php

namespace App\Http\Controllers;

use App\XmlElmoHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ValidateController extends Controller
{

    /**
     * Returns view for validation process
     * 
     * @return \Illuminate\Http\Response
     */
    public function showValidateForm(Request $request) {
        return view('validate/start');
    }

    /**
     * Returns view for validation result
     * 
     * @param Request xml file to check
     * 
     * @return \Illuminate\Http\Response
     */
    public function showValidateResult(Request $request) {

        $elmoSchemaPath = base_path() . "/app/schema/elmo.xsd";
        $public_keyfile = base_path() . env('EMREX_PUBLIC_KEY');

        $validatedData = $request->validate([
            'elmoFile' => 'required|file|mimetypes:text/html,text/plain,text/xml,aplication/xml,xml'
        ]);

        $xmlstr = file_get_contents($request->file('elmoFile')->path());
        $filepath = "tmp/elmo/validate/" . uniqid("validate_") . ".xml";
        Storage::disk('local')->put($filepath, $xmlstr);

        $xmlErrors = XmlElmoHelper::getXmlErrors($xmlstr);
        $xmlSchemaErrors = array();
        $signatureValid = false;

        if(sizeof($xmlErrors) == 0) {
            $xmlSchemaErrors = XmlElmoHelper::getXmlSchemaErrors($filepath, $elmoSchemaPath);

            if(sizeof($xmlSchemaErrors) == 0) {
                $signatureValid = xmlElmoHelper::validateEmrexSignature($filepath, $public_keyfile);
            }
        }

        Storage::disk('local')->delete($filepath);
        
        return view("validate/result", [
            "xmlErrors" => $xmlErrors,
            "xmlSchemaErrors" => $xmlSchemaErrors,
            "signatureValid" => $signatureValid,
        ]);
    }
}
