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

        $videoName = $unique_id. '.' . $request->file->getClientOriginalExtension();
        $bannerName = $unique_id. '.' . $request->banner->getClientOriginalExtension();



        $s3 = new \S3(env('AWS_ACCESS_KEY_ID'),env('AWS_SECRET_ACCESS_KEY') );

        if ($s3->putObjectFile($request->file('banner')->path(), "vcp-blw", "timeline/cei/products/images/".$bannerName,
            \S3::ACL_PUBLIC_READ)) {
            $videoBanner = "http://vcp-blw.s3.amazonaws.com/timeline/cei/products/images/".$bannerName;
        }

        if ($s3->putObjectFile($request->file('file')->path(), "vcp-blw", "timeline/cei/products/images/".$videoName,
            \S3::ACL_PUBLIC_READ)) {
            $videoFile = "http://vcp-blw.s3.amazonaws.com/timeline/cei/products/images/".$videoName;
        }

        $createVideo = new Video();
        $createVideo->title = $request->title;
        $createVideo->category_id = $request->category_id;
        $createVideo->banner = $videoBanner;
        $createVideo->file = $videoFile;
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

        $s3 = new \S3(env('AWS_ACCESS_KEY_ID'),env('AWS_SECRET_ACCESS_KEY') );

        $video = Video::where('unique_id', $request->video_id)->first();

        if ($video) {

            //check if the user sent a new banner
            if(!empty($request->file('banner'))){
                $bannerName = $video->unique_id. '.' . $request->banner->getClientOriginalExtension();

                if ($s3->putObjectFile($request->file('banner')->path(), "vcp-blw", "timeline/cei/products/images/" . $bannerName,
                    \S3::ACL_PUBLIC_READ)) {
                    $video->banner = "http://vcp-blw.s3.amazonaws.com/timeline/cei/products/images/".$bannerName;
                }
            }

            //check if the user sent a new video file
            if(!empty($request->file('file'))){
                $videoName = $video->unique_id. '.' . $request->file->getClientOriginalExtension();

                if ($s3->putObjectFile($request->file('file')->path(), "vcp-blw", "timeline/cei/products/images/" . $videoName,
                    \S3::ACL_PUBLIC_READ)) {
                    $video->file = "http://vcp-blw.s3.amazonaws.com/timeline/cei/products/images/".$videoName;
                }

            }


            $video->title = $request->title;
            $video->category_id = $request->category;
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
}
