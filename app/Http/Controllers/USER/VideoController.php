<?php

namespace App\Http\Controllers\USER;

use App\Libraries\Messenger;
use App\Playlist;
use App\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class VideoController extends Controller
{


    public function showVideo(Request $request){

        $validator = Validator::make($request->all(), [
            'video_id' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status'=>false,
                'message' => 'All fields are required',
                'errors' =>$validator->errors()->all() ,
            ], 401);

        }

        $video = Video::where("unique_id",$request->video_id)->first();

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




    public function allVideos(){

        $videos = Video::where('status',1)->latest()->get();

        if (count($videos) > 0){

            if(isset($_GET['per_page'])){
                $videos = Video::where('status',1)->orderBy("id","desc")->paginate($_GET['per_page']);
            }else{
                $videos = Video::where('status',1)->orderBy("id","desc")->paginate(10);
            }

            $data_arr = array();


            foreach ($videos as $video){

                $data_arr[] =  array(
                    "title"=> $video->title,
                    "banner"=> $video->banner,
                    "file"=> $video->file,
                    "video_id"=> $video->unique_id,
                    //"category"=> $video->category->name,
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









    public function stationVideos(Request $request){

        $validator = Validator::make($request->all(), [
            'station_id' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status'=>false,
                'message' => 'All fields are required',
                'errors' =>$validator->errors()->all() ,
            ], 401);

        }

        $stationVideos = Video::where('owner_id', \auth()->user()->unique_id)->get();

//        return $stationVideos;
//        exit();

        if (count($stationVideos) > 0){

            if(isset($_GET['per_page'])){
                $stationVideos = Video::where('owner_id', \auth()->user()->unique_id)->orderBy("id","desc")->paginate($_GET['per_page']);
            }else{
                $stationVideos = Video::where('owner_id', \auth()->user()->unique_id)->orderBy("id","desc")->paginate(10);
            }

            $data_arr = array();

            foreach ($stationVideos as $stationVideo){

                $data_arr[] =  array(
                    "title"=> $stationVideo->title,
                    "banner"=> $stationVideo->banner,
                    "file"=> $stationVideo->file,
                    "video_id"=> $stationVideo->unique_id,
                    //"category"=> $stationVideo->category->name,
                    "category_id"=> $stationVideo->category_id,
                    "owner_id"=> $stationVideo->owner->unique_id,
                    "owner_name"=> $stationVideo->owner->full_name(),
                    "created_at"=> $stationVideo->created_at->diffForHumans(),
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
