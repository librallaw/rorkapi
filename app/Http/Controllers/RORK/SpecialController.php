<?php

namespace App\Http\Controllers\RORK;

use App\Http\Controllers\Controller;
use App\Special;
use Illuminate\Http\Request;

class SpecialController extends Controller
{
    //

    public function returnSpecialVideo()
    {
        $product = Special::first();



        if($product->status){

            $data_arr[] = array(
                "link"=> $product->video,
                "created" => $product->created_at ->diffForHumans()
            );

            return response()->json([
                'status' => true,
                'data' => $data_arr,
            ],200);

        }else{
            return response()->json([
                'status' => false,
                'message' => "No Special Video at this time",
            ],404);
        }


    }
}
