<?php

namespace App\Http\Controllers\RORK;

use App\Http\Controllers\Controller;
use App\Product;
use App\Purchase;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    //

    public function purchaseProduct($product_u)
    {
        //check if user already bought that product

        $check_user_product = Purchase::where("product_id",$product_u)->where("user_id",Auth::user()->unique_id)->first();

        if(!empty($check_user_product)){

            return response()->json([
                'status' => false,
                'message' => "You have already bought this product",
            ]);
        }

        $product = Product::where("unique_id",$product_u) ->first();

        if(empty($product)){
            return response()->json([
                'status' => false,
                'message' => "Product not found",
            ]);
        }


        $require_coins  =  $product -> price;
        $available_coins = Auth::user()->coins;


        if($require_coins > $available_coins){
            return response()->json([
                'status' => false,
                'message' => "Sorry you need an additional ".($require_coins - $available_coins)." coin(s) to purchase this product",
            ]);
        }else{



            $update = User::where("unique_id",Auth::user()->unique_id) -> first();
            $update -> coins = ($update -> coins) - ($product -> price);
            $update -> save();


            $new_purchase =  new Purchase();

            $new_purchase -> product_id = $product_u;
            $new_purchase -> user_id = Auth::user()->unique_id;
            $new_purchase -> amount = $product -> price;

            $new_purchase -> save();




            return response()->json([
                'status' =>true,
                'message' => "Product successfully added to your Library",
            ]);
        }

        //check if user has enough coin to purchase product


        //Add product to user Account
    }
}
