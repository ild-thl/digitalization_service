<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleXMLElement;

class TransformController extends Controller
{

    private $ignoreTags = [
        'html',
        'p'
    ];

    /**
     * TODO
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
     * TODO
     *
     * @return \Illuminate\Http\Response
     */
    public function transformUpload(Request $request)
    {
        $validated = $request->validate([
            'issuer_title' => 'string'
        ]);

        $user = Auth::user();

        // save issuer title
        if (!empty($validated['issuer_title']) && $user->issuer_title !== $validated['issuer_title']) {
            $user->issuer_title = $validated['issuer_title'];
            $user->save();
        }

        return view('transform/upload', [
            'user' => $user
        ]);
    }

    /**
     * TODO
     *
     * @return \Illuminate\Http\Response
     */
    public function transformAssignment(Request $request)
    {
        $validated = $request->validate([
            'xml' => 'string'
        ]);

        $xmlString = $validated['xml'];
        $xml = new SimpleXMLElement($xmlString);

        $array = $this->xml2Array($xml);

        // TODO store xml in session for later

        return view('transform/assignment', [
            'user' => Auth::user(),
            'xmlAsArray' => $array
        ]);
    }

    /**
     * Undocumented function
     *
     * @param SimpleXMLElement $xml
     * @return void
     */
    function xml2Array(SimpleXMLElement $xml) : Array
    {
        $array = [];

        foreach ($xml as $k => $v) {
            // Ausschließen, dass es sich um HTML Inhalte handelt.
            if (is_string($k) && !in_array($k, $this->ignoreTags)) {
                // wenn diese if weg gelassen wird, gibt es im array auch die keys mit leeren values.
                if (!empty($v)) {
                    // simpleXMLElement hat Inhalt, Anzahl der children checken
                    if (!empty($v->children())) {
                        $subArray = $this->xml2Array($v);
                        $array = array_merge($array, $subArray);
                    } else {
                        // es gibt keine weiteren children
                        $array[$k] = $v->__toString();
                    }
                }
            // ignored tag contents als XML übernehmen.
            } else if(is_string($k) && in_array($k, $this->ignoreTags)) {
                $array[$k] = $v->asXML();
            }
        }

        return $array;
    }
}
