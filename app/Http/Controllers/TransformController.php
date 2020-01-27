<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleXMLElement;

class TransformController extends Controller
{

    private $maxRecursionLevel = 2;

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
        if( ! empty($validated['issuer_title']) && $user->issuer_title !== $validated['issuer_title']) {
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

        $currentRecursionLevel = 0;

        $validated = $request->validate([
            'xml' => 'string'
        ]);

        $html = '';
        $array = [];

        $xmlString = $validated['xml'];
        $xml = new SimpleXMLElement($xmlString);

        $array = $this->rrr($xml);


        /* foreach($xml as $key => $val) {

            if($currentRecursionLevel > $this->maxRecursionLevel) {
                $html .= 'reched recursion level';
                continue;
            }

            $currentRecursionLevel++;

            $html .=  $key.' --> ';

            if(is_object($val)) {
                foreach($val as $tmpKey => $tmpVal) {
                    $html .=  $tmpKey .' ==> '.$tmpVal;
                    $html .=  '
                    ';

                    $array[$tmpKey] = $tmpVal;
                }
            } else {
                $html .=  $val .'
                ';

                $array[$key] = $val;
            }



            $currentRecursionLevel--;

        }
 */
        dd($array);

        // TODO store xml in session for later

        return view('transform/assignment', [
            'user' => Auth::user(),
            'html' => $html
        ]);
    }

    function rrr($var) {

        $arr = [];



        foreach($var as $key => $val) {
            if(is_object($val)) {
                $tmpArr = $this->rrr($val);

                var_dump(is_array($tmpArr));

                $arr = array_merge($arr, $tmpArr);
            } else {
                $arr[$key] = $val;
            }
        }

        echo '<br><hr><br>Input: <br>';
        echo get_class($var).'<br>';
        dump($var);
        echo 'Output: <br>';
        dump($arr);

        return $arr;

    }

}
