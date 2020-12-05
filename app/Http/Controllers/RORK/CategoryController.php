<?php

namespace App\Http\Controllers\RORK;

use App\Category;
use App\Http\Controllers\Controller;
use App\Product_category;
use App\Video;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //

    public function CategoryVid(Request $request){



        if(isset($_GET['per_page'])){
            $categories = Category::paginate($_GET['per_page']);
        }else{
            $categories = Category::take(3)->get();
        }

        $finalData = array();




        if (count($categories) > 0){



            foreach ($categories as $category){

                $data_arr = array();

                $videos = Video::where('category_id',$category->unique_id)->where('status',1)->take(3)->get();

                foreach ($videos as $video){


                    $data_arr[] =  array(
                        "title"=> $video->title,
                        "banner"=> $video->banner,
                        "file"=> $video->file,
                        "video_id"=> $video->unique_id,
                        "category"=> $video->category->name,
                        "category_id"=> $video->category_id,
                        "owner_id"=> $video->station->unique_id,
                        "owner_name"=> $video->station->name,
                        "created_at"=> $video->created_at->diffForHumans(),
                    );

                }

                $finalData[] = array("cat_id" => $category->unique_id,"title"=>$category->name,'videos'=>$data_arr);


            }


            return response()->json([
                'status' => true,
                'data' =>$finalData,
            ],200);

        }



    }


    public function SingleCategoryVid(Request $request,$cat_id){


                $videos = Video::where('category_id',$cat_id)->get();
                $category = Category::where('unique_id',$cat_id)->first();

                foreach ($videos as $video){


                    $data_arr[] =  array(
                        "title"=> $video->title,
                        "banner"=> $video->banner,
                        "file"=> $video->file,
                        "video_id"=> $video->unique_id,
                        "category"=> $video->category->name,
                        "category_id"=> $video->category_id,
                        "owner_id"=> $video->station->unique_id,
                        "owner_name"=> $video->station->name,
                        "created_at"=> $video->created_at->diffForHumans(),
                    );

                }



            return response()->json([
                'status' => true,
                'category_name' => $category -> name,
                'data' => $data_arr,
            ],200);

        }


    public function viewProductCategories(){

        if(isset($_GET['per_page'])){
            $categories = Product_category::paginate($_GET['per_page']);
        }else{
            $categories = Product_category::paginate(10);
        }


        if (count($categories) > 0){


            $data_arr = array();

            foreach ($categories as $category){

                $data_arr[] = array(
                    "name"=> $category->name,
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


}


