<?php

namespace App\Http\Controllers;

use App\Libraries\Messenger;
use App\Playlist;
use App\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class VideoController extends Controller
{
    public function uploadVideo(Request $request,Messenger $messenger){

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'category_id' => 'required',
            'file' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status'=>false,
                'message' => 'All fields are required',
                'errors' =>$validator->errors()->all() ,
            ], 401);

        }



        $unique_id = $messenger->randomId('6','unique_id','videos');

        $createVideo = new Video();
        $createVideo->title = $request->title;
        $createVideo->category_id = $request->category_id;
        $createVideo->banner = $request->banner;
        $createVideo->file = $request->file;
        $createVideo->unique_id = $unique_id;
        $createVideo->owner_id = Auth::user()->unique_id;
        $createVideo->save();

        return response()->json([
            'status' => true,
            'message' =>  "Video created successfully",
            'data' => [

                "title"=> $createVideo->title,
                "category_id"=> $createVideo->category_id,
                "banner"=> $createVideo->banner,
                "file"=> $createVideo->file,
                "video_id"=> $createVideo->unique_id,
                "created_at"=> $createVideo->created_at->diffForHumans(),

            ],
        ]);
    }

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

    public function updateVideo(Request $request){

        $this->validate($request, [
            'title'    => 'required',
            'category' => 'required',
            'video_id' => 'required',
        ]);

        $video = Video::where('unique_id', $request->video_id)->first();

        if ($video) {

            $video->title = $request->title;
            $video->category_id = $request->category;
            $video->banner = $request->banner;
            $video->file = $request->file;
            $video->save();

            return response()->json([
                'status' => true,
                'message' => "Video updated successfully",
            ]);


        } else {
            return response()->json([
                'status' => false,
                'message' => "Video Not Found",
            ]);
        }


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

              //  dd($video->station);

                $data_arr[] =  array(
                    "title"=> $video->title,
                    "banner"=> $video->banner,
                    "file"=> $video->file,
                    "video_id"=> $video->unique_id,
                    "status"=> $video->status,
                    "category_name"=> $video->category->name,
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


    public function deleteVideo(Request $request){

        $validator = Validator::make($request->all(), [
            'video_id' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status'=>false,
                'message' => 'All fields are required',
                'errors' =>$validator->errors()->all() ,
            ], 401);

        }

        $video = Video::where('unique_id',$request->video_id)->first();

        if($video){

            if($video->owner_id == Auth::user()->unique_id){
                $video->status =  9;
                $video-> save();

                return response()->json([
                    'status' => true,
                    'message' => 'Video deleted successfully',
                ],200);

            }else{

                return response()->json([
                    'status' => false,
                    'message' => 'You can only make changes on your own videos',
                ],401);
            }



        } else {

            return response()->json([
                'status' => false,
                'message' => 'Video Not found',
            ],404);
        }
    }




    public function activateVideo(Request $request){

        $validator = Validator::make($request->all(), [
            'video_id' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status'=>false,
                'message' => 'All fields are required',
                'errors' =>$validator->errors()->all() ,
            ], 401);

        }

        $video = Video::where('unique_id',$request->video_id)->first();

        if($video){

            if($video->owner_id == Auth::user()->unique_id){

                if($video->status  != 0){
                    return response()->json([
                        'status' => false,
                        'message' => 'This Video has already been Activated',
                    ],400);
                }else{
                    $video->status =  1;
                    $video-> save();

                    return response()->json([
                        'status' => true,
                        'message' => 'Video Activated successfully',
                    ],200);
                }


            }else{

                return response()->json([
                    'status' => false,
                    'message' => 'You can only make changes on your own videos',
                ],401);
            }



        } else {

            return response()->json([
                'status' => false,
                'message' => 'Not found',
            ],404);
        }
    }

    public function stationVideos(){



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
                    "category_id"=> $stationVideo->category_id,
                    "owner_id"=> $stationVideo->owner->unique_id,
                    "status"=> $stationVideo->status,
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
