<?php

namespace App\Http\Controllers\RORK;

use App\Http\Controllers\Controller;
use App\Product;
use App\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReadController extends Controller
{
    //

    public function readBook($product_id)
    {
        $puchases = Purchase::where("user_id",Auth::user()->unique_id)->where("product_id",$product_id) -> first();


        if(!empty($puchases)){

            $product = Product::where("unique_id",$product_id)->first();



            $data_arr = array(
                "title"=> $product->title,
                "category_id"=> $product->category_id,
                "description"=> $product->description,
                "price"=> $product->price,
                "unique_id"=> $product->unique_id,
                "image" => $product ->image,
                "file" => $product ->file,
            );

            return response()->json([
                'status' => true,
                'data' => $data_arr,
            ],200);

        }else{

            return response()->json([
                'status' => false,
                'message' => "No product found",
            ],200);

        }
    }
}
