<?php

namespace App\Http\Controllers;

use App\ElmoKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ElmoKeyController extends Controller
{
    public function showElmoKeys(Request $request)
    {
        $keys = ElmoKey::all();

        return view('elmo_keys/overview', [
            'keys' => $keys,
            'edit' => env('DIGISERV_EDIT', false),
        ]);
    }

    public function addElmoKey(Request $request)
    {
        $json = new \stdClass();

        if(!env('DIGISERV_EDIT', false)) {

            $validator = Validator::make($request->all(), [
                'newElmoKeyTitle' => 'required|min:1|max:191'
            ]);

            if(!$validator->fails()) {

                $newElmoKeyTitle = $request->newElmoKeyTitle;

                if(ElmoKey::where('title', '=', $newElmoKeyTitle)->count() == 0) {

                    $elmoKey = new ElmoKey;
                    $elmoKey->title = $newElmoKeyTitle;
                    $elmoKey->created_at = now();
                    $elmoKey->updated_at = now();

                    if($elmoKey->save()) {
                        $json->status = "success";
                    } else {
                        $json->status = "error";
                        $json->errorMessage = __('digiserv.elmo_keys_err_db');
                    }

                } else {
                    $json->status = "error";
                    $json->errorMessage = __('digiserv.elmo_keys_err_duplicate');
                }

            } else {
                $json->status = "error";
                $json->errorMessage = __('digiserv.elmo_keys_err_invalid');
            }
        } else {
            $json->status = "error";
            $json->errorMessage = __('digiserv.edit_mode_disabled');
        }
        
        return response()->json($json);
    }

    public function deleteElmoKey(Request $request)
    {
        $json = new \stdClass();

        if(!env('DIGISERV_EDIT', false)) {

            $validator = Validator::make($request->all(), [
                'elmokeyid' => 'required|integer'
            ]);

            if(!$validator->fails()) {

                $elmokeyid = $request->elmokeyid;

                $elmokey = ElmoKey::find($elmokeyid);

                if($elmokey !== null) {

                    if($elmokey->keyAssignment->count() > 0) {
                        if(!$elmokey->keyAssignment()->delete()) {
                            $json->status = "error";
                            $json->errorMessage = __('digiserv.elmo_keys_err_del_ka');
                        }
                    }

                    if(!property_exists($json, 'status')) {
                        if($elmokey->delete()) {
                            $json->status = "success";
                        } else {
                            $json->status = "error";
                            $json->errorMessage = __('digiserv.elmo_keys_err_del_ek');
                        }
                    }

                } else {
                    $json->status = "error";
                    $json->errorMessage = __('digiserv.elmo_keys_err_not_found');
                }

            } else {
                $json->status = "error";
                $json->errorMessage = __('digiserv.elmo_keys_err_nan');
            }
        } else {
            $json->status = "error";
            $json->errorMessage = __('digiserv.edit_mode_disabled');
        }

        return response()->json($json);
    }
}
