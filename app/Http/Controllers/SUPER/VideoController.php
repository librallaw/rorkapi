<?php

namespace App\Http\Controllers\SUPER;

use App\FeaturedVideo;
use App\Http\Controllers\Controller;
use App\Libraries\Messenger;
use App\Playlist;
use App\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Searchable\Search;
use Validator;

class VideoController extends Controller
{
    public function uploadVideo(Request $request,Messenger $messenger){

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'category_id' => 'required',
            'station_id' => 'required',
            'file' => 'required',
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
        $createVideo->owner_id = $request->station_id;
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
                "station_id"=> $createVideo->owner_id,
                "banner"=> $createVideo->banner,
                "file"=> $createVideo->file,
                "video_id"=> $createVideo->unique_id,
                "created_at"=> $createVideo->created_at->diffForHumans(),

            ],
        ]);
    }



    public function updateVideo(Request $request){

        $this->validate($request, [
            'title' => 'required',
            'category_id' => 'required',
            'station_id' => 'required',
            'file' => 'required'
        ]);

        $video = Video::where('unique_id', $request->video_id)->first();

        if ($video) {

            $video->title = $request->title;
            $video->owner_id = $request->station_id;
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


    public function activateVideoSuper(Request $request){

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


        } else {

            return response()->json([
                'status' => false,
                'message' => 'Not found',
            ],404);
        }
    }



    public function deactivateVideoSuper(Request $request){

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

            if($video->status  != 1){
                return response()->json([
                    'status' => false,
                    'message' => 'This Video has already been Deactivated',
                ],400);
            }else{
                $video->status =  0;
                $video-> save();

                return response()->json([
                    'status' => true,
                    'message' => 'Video Deactivated successfully',
                ],200);
            }


        } else {

            return response()->json([
                'status' => false,
                'message' => 'Not found',
            ],404);
        }
    }






    public function createFeaturedVideos(Request $request){
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

        $featuredVideo = new FeaturedVideo();
        $featuredVideo->video_id = $request->video_id;
        $featuredVideo->status = 1;
        $featuredVideo->save();

        return response()->json([
            'status' => true,
            'message' =>  "Featured Video created successfully",
            'data' => [
                "featuredVideos"=> $featuredVideo->video_id,
                "created_at"=> $featuredVideo->created_at->diffForHumans(),
            ],
        ]);

    }


    public function allFeaturedVideos(){

        $featuredVideos = FeaturedVideo::where('status', 1)->latest()->get();

        if (count($featuredVideos) > 0){

            if(isset($_GET['per_page'])){
                $all_featuredVideos = FeaturedVideo::where('status', 1)->orderBy("id","desc")->paginate($_GET['per_page']);
            }else{
                $all_featuredVideos = FeaturedVideo::where('status', 1)->orderBy("id","desc")->paginate(10);
            }

            $data_arr = array();


            foreach ($all_featuredVideos as $featuredVideo){

                $data_arr[] =  array(
                    "title"=> $featuredVideo->video->title,
                    "category_id"=> $featuredVideo->video->category_id,
                    "category_name"=> $featuredVideo->video->category->name,
                    "station_id"=> $featuredVideo->video->owner_id,
                    "banner"=> $featuredVideo->video->banner,
                    "file"=> $featuredVideo->video->file,
                    "video_id"=> $featuredVideo->video->unique_id,
                    "created_at"=> $featuredVideo->video->created_at->diffForHumans(),
                );

            }


            return response()->json([
                'status' => true,
                'data' =>$data_arr,
            ],200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No Featured video found',
            ],404);
        }

    }


    public function removeFeaturedVideo(Request $request){
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

        $featuredVideo = FeaturedVideo::where('video_id',$request->video_id)->first();

        if($featuredVideo){

            if($featuredVideo->status == 1){

                $featuredVideo->status =  0;
                $featuredVideo-> save();

                return response()->json([
                    'status' => true,
                    'message' => 'Featured Video has been removed successfully',
                ],200);

            }else{

                return response()->json([
                    'status' => false,
                    'message' => 'Featured video already been removed',
                ],401);
            }


        } else {

            return response()->json([
                'status' => false,
                'message' => 'Not found',
            ],404);
        }
    }


    public function searchVideos(Request $request){

        $validator = Validator::make($request->all(), [
            'search' => 'required',
        ]);

        $searchResults = Video::where( 'title', 'LIKE', '%' . $request->search . '%' )->get();

        if (count($searchResults) > 0){

            $data_arr = array();

            foreach ($searchResults as $searchResult){

                $data_arr[] =  array(
                    "title"=> $searchResult->title,
                    "category_id"=> $searchResult->category_id,
                    "category_name"=> $searchResult->category->name,
                    "station_id"=> $searchResult->owner_id,
                    "banner"=> $searchResult->banner,
                    "file"=> $searchResult->file,
                    "video_id"=> $searchResult->unique_id,
                    "created_at"=> $searchResult->created_at->diffForHumans(),
                );

            }

            return response()->json([
                'status' => true,
                'data' =>$data_arr,
            ],200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No video found',
            ],404);
        }
    }


}
