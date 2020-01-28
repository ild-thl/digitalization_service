<?php

namespace App\Http\Controllers;

use App\ElmoKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ElmoKeyController extends Controller
{
    public function showElmoKeys(Request $request)
    {
        $keys = ElmoKey::all();

        return view('elmo_keys/overview', [
            'keys' => $keys
        ]);
    }
}
