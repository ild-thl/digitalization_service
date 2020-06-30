<?php

namespace App\Http\Controllers;

use App\KeyAssignment;
use App\ElmoKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KeyAssignmentController extends Controller
{
    public function showKeyAssignments(Request $request)
    {
        $keys = ElmoKey::all();

        return view('key_assignments/overview', [
            'keys' => $keys,
            'edit' => env('DIGISERV_EDIT', false),
        ]);
    }

    public function deleteKeyAssignment(Request $request)
    {
        $json = new \stdClass();

        if(!env('DIGISERV_EDIT', false)) {

            $validator = Validator::make($request->all(), [
                'keyassignmentid' => 'required|integer'
            ]);

            if(!$validator->fails()) {

                $keyassignmentid = $request->keyassignmentid;

                $keyassignment = KeyAssignment::find($keyassignmentid);

                if($keyassignment !== null) {

                    if($keyassignment->delete()) {
                        $json->status = "success";
                    } else {
                        $json->status = "error";
                        $json->errorMessage = __('digiserv.key_assignments_err_del');
                    }

                } else {
                    $json->status = "error";
                    $json->errorMessage = __('digiserv.key_assignments_err_not_found');
                }

            } else {
                $json->status = "error";
                $json->errorMessage = __('digiserv.key_assignments_err_nan');
            }
        } else {
            $json->status = "error";
            $json->errorMessage = __('digiserv.edit_mode_disabled');
        }

        return response()->json($json);
    }
}
