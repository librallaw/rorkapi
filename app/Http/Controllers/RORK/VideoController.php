<?php

namespace App\Http\Controllers\RORK;

use App\Http\Controllers\Controller;
use App\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    //

    public function showVideo(Request $request, $video_id){


        $video = Video::where("unique_id",$video_id)->first();

        if($video){

            if($video->status !== 1){

                return response()->json([
                    'status' => false,
                    'message' => 'This Video has either been deleted or not available for viewing now',
                ],400);
            }
        }else{

            return response()->json([
                'status' => false,
                'message' => "Video not found",
            ],404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                "title"=> $video->title,
                "banner"=> $video->banner,
                "file"=> $video->file,
                "video_id"=> $video->unique_id,
                "category"=> $video->category->name,
                "category_id"=> $video->category_id,
                "owner_id"=> $video->owner->unique_id,
                "owner_name"=> $video->owner->full_name(),
                "created_at"=> $video->created_at->diffForHumans(),
            ],
        ]);
    }





    public function otherVideos(Request $request, $video_id){


        $videos = Video::where("category_id",$video_id)->where("status",1)->take(10)->get();

        if($videos){


            $data_arr =  array();

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
            ]);


        }else{

            return response()->json([
                'status' => false,
                'message' => "Video not found",
            ],404);
        }

    }
}
