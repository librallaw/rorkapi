<?php

namespace App\Http\Controllers\RORK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //

    public function userDetails()
    {
        $data = array(
            "first_name" => Auth::user()->first_name,
            "last_name" => Auth::user()->last_name,
            "email" => Auth::user()->email,
            "first_name" => Auth::user()->coins,
        );


        return response()->json([
            'status' =>true,
            'data' => $data
        ]);

    }
}
