<?php

namespace App\Http\Controllers\USER;

use App\Category;
use App\Http\Controllers\Controller;
use App\Video;
use Illuminate\Http\Request;
use Validator;

class CategoryController extends Controller
{
    //
    public function categoryVideo(Request $request){

        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);


        if ($validator->fails()) {

            return response()->json([
                'status'=>false,
                'message' => 'All fields are required',
                'errors' =>$validator->errors()->all() ,
            ], 401);

        }



        $videos = Video::where('category_id',$request->category_id)->where('status',1)->latest()->get();

        if (count($videos) > 0){

            if(isset($_GET['per_page'])){
                $videos = Video::where('category_id',$request->category_id)->orderBy("id","desc")->paginate($_GET['per_page']);
            }else{
                $videos = Video::where('category_id',$request->category_id)->orderBy("id","desc")->paginate(10);
            }

            $data_arr = array();


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
                'data' =>$data_arr,
            ],200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No videos found',
            ],404);
        }

    }


    public function viewCategories(){

        if(isset($_GET['per_page'])){
            $categories = Category::paginate($_GET['per_page']);
        }else{
            $categories = Category::paginate(10);
        }


        if (count($categories) > 0){


            $data_arr = array();

            foreach ($categories as $category){

                $data_arr[] = array(
                    "name"=> $category->name,
                    "category_id"=> $category->unique_id
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


    public function HomeCategoryVideo(Request $request){



        if(isset($_GET['per_page'])){
            $categories = Category::paginate($_GET['per_page']);
        }else{
            $categories = Category::take(5)->get();
        }

        $finalData = array();




        if (count($categories) > 0){



            foreach ($categories as $category){

                $data_arr = array();

                $videos = Video::where('category_id',$category->unique_id)->where('status',1)->take(5)->get();

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

                $finalData[] = array("title"=>$category->name,'videos'=>$data_arr);


            }


            return response()->json([
                'status' => true,
                'data' =>$finalData,
            ],200);

        }
        


    }
}
