<?php

namespace App\Http\Controllers\USER;

use App\Http\Controllers\Controller;
use App\Video;
use Illuminate\Http\Request;
use Validator;

class CategoryController extends Controller
{
    //
    public function categoryVideo(Request $request){

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);


        if ($validator->fails()) {

            return response()->json([
                'status'=>false,
                'message' => 'All fields are required',
                'errors' =>$validator->errors()->all() ,
            ], 401);

        }



        $videos = Video::where('category_id',$request->id)->where('status',1)->latest()->get();

        if (count($videos) > 0){

            if(isset($_GET['per_page'])){
                $videos = Video::orderBy("id","desc")->paginate($_GET['per_page']);
            }else{
                $videos = Video::orderBy("id","desc")->paginate(10);
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
                    "owner_id"=> $video->owner->unique_id,
                    "owner_name"=> $video->owner->full_name(),
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
}
