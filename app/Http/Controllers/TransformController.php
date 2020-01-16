<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransformController extends Controller
{
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
        $validated = $request->validate([
            'xml' => 'string'
        ]);

        dd($validated);

        return view('transform/assignment', [
            'user' => Auth::user()
        ]);
    }

}
