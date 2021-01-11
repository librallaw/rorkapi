<?php

namespace App\Http\Controllers\RORK;

use App\Http\Controllers\Controller;
use App\Product;
use App\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    //

    public function viewProducts(){

        if(isset($_GET['per_page'])){
            $products = Product::paginate($_GET['per_page']);
        }else{
            $products = Product::paginate(10);
        }


        if (count($products) > 0){


            $data_arr = array();

            foreach ($products as $category){


                $data_arr[] = array(
                    "title"=> $category->title,
                    "category_id"=> $category->category_id,
                    "description"=> $category->description,
                    "price"=> $category->price,
                    "image"=> $category->image,
                    "unique_id"=> $category->unique_id
                );
            }



            return response()->json([
                'status' => true,
                'data' => $data_arr,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No category found',
            ]);
        }
    }



    public function viewFeaturedProducts(){


        $products = Product::where('featured',1)->get();


        if (count($products) > 0){


            $data_arr = array();

            foreach ($products as $category){


                $data_arr[] = array(
                    "title"=> $category->title,
                    "category_id"=> $category->category_id,
                    "description"=> $category->description,
                    "price"=> $category->price,
                    "image"=> $category->image,
                    "unique_id"=> $category->unique_id
                );
            }



            return response()->json([
                'status' => true,
                'data' => $data_arr,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No category found',
            ]);
        }
    }


    public function productDetails($unique_id)
    {



        $product = Product::where('unique_id',$unique_id)->first();

        $data_arr[] = array(
            "title"=> $product->title,
            "category_id"=> $product->category_id,
            "description"=> $product->description,
            "price"=> $product->price,
            "unique_id"=> $product->unique_id,
            "image" => $product -> image

        );



        return response()->json([
            'status' => true,
            'data' => $data_arr,
        ],200);


    }

    public function productRelation($unique_id)
    {



        $product_s = Product::where('unique_id',$unique_id)->first();

        $products = Product::where('category_id',$product_s->category_id)->where('unique_id','!=',$unique_id)->get();



        foreach ($products as $product){

            $data_arr[] = array(
                "title"=> $product->title,
                "category_id"=> $product->category_id,
                "description"=> $product->description,
                "price"=> $product->price,
                "unique_id"=> $product->unique_id,
                "image" => $product -> image

            );
        }


        return response()->json([
            'status' => true,
            'data' => $data_arr,
        ],200);


    }


    public function UserProducts()
    {
        $purchases = Purchase::where("user_id", Auth::user()->unique_id) -> get();

        if(count ($purchases) > 0 ){

            foreach ($purchases as $product){

                $data_arr[] = array(
                    "title"=> $product->product->title,
                    "category_id"=> $product->product->category_id,
                    "description"=> $product->product->description,
                    "price"=> $product->product->price,
                    "unique_id"=> $product->product->unique_id,
                    "image" => $product ->product->image

                );
            }

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


    public function SingleCategoryProduct(Request $request,$cat_id){

        // dd($videos);

        $products = Product::where('category_id',$cat_id)->get();



        foreach ($products as $product){

            $data_arr[] = array(
                "title"=> $product->title,
                "category_id"=> $product->category_id,
                "description"=> $product->description,
                "price"=> $product->price,
                "unique_id"=> $product->unique_id,
                "image" => $product -> image

            );
        }



        return response()->json([
            'status' => true,
            'data' => $data_arr,
        ],200);

    }



}
