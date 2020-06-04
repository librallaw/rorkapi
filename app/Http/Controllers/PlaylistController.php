<?php

namespace App\Http\Controllers;

use App\Libraries\Messenger;
use App\Playlist;
use App\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class PlaylistController extends Controller
{
    public function createPlaylist(Request $request,Messenger $messenger)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'category_id'  => 'required',
        ]);


        if ($validator->fails()) {

            return response()->json([
                'status'=>false,
                'message' => 'All fields are required',
                'errors' =>$validator->errors()->all() ,
            ], 401);

        }



        $newPlaylist = new Playlist();
        $newPlaylist->title = $request->title;
        $newPlaylist->category_id = $request->category_id;
        $newPlaylist->owner_id = Auth::user()->unique_id;
        $newPlaylist->unique_id ="PL-".$messenger->randomId('4','unique_code','playlists');

        $newPlaylist->save();

        return response()->json([
            'status' => true,
            'message' => "Playlist created successfully",
            'data' => [
                "title"=> $newPlaylist->title,
                "category_id"=> $newPlaylist->category_id,
                "category_name"=> $newPlaylist->category->name,
                "owner_id"=> $newPlaylist->owner_id,
                "owner_name"=> $newPlaylist->owner->full_name(),
                "unique_id"=>$newPlaylist->unique_id ,
                "created_at"=>$newPlaylist->created_at->diffForHumans(),

            ],
        ]);
    }

    public function addToPlaylist(Request $request){


        $validator = Validator::make($request->all(), [
            'video_id' => 'required',
            'playlist_id' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status'=>false,
                'message' => 'All fields are required',
                'errors' =>$validator->errors()->all() ,
            ], 401);

        }


        $playlist = Playlist::where('unique_id', $request->playlist_id)->first();

        if(!empty($playlist)){

            $videolist = $playlist->videos;

            $confirm_video  = Video::where("unique_id",$request->video_id)->where("status",1)->first();

            if(!empty($confirm_video)){

                if ($videolist == null || empty($videolist)){

                    //check video to be sure that the video exists

                        //convert playlist videos to an array
                        $playlist_videos = explode('-', $videolist);

                        //add new video to playlist videos
                        $playlist_videos[] = $request->video_id;

                        //convert play list video back to string
                        $playlist_videos_string = implode('-', $playlist_videos);

                        $playlist -> videos = $playlist_videos_string;
                        $playlist ->save();


                    return response()->json([
                        'status' => true,
                        'message' => "video added successfully",

                        'data' =>  [
                            "title"         => $playlist->title,
                            "category_id"   => $playlist->category_id,
                            "category_name" => $playlist->category->name,
                            "videos_count" => count(explode('-',$playlist->videos)),
                            "owner_id"      => $playlist->owner_id,
                            "owner_name"    => $playlist->owner->full_name(),
                            "unique_id"     => $playlist->unique_id ,
                            "created_at"    => $playlist->created_at->diffForHumans(),
                        ],
                    ]);



                } else {

                    $convertToArray = explode('-', $videolist);
                    $convertToArray[] = $request->video_id;
                    $addedtolist = implode('-', $convertToArray);
                    $playlist->videos = $addedtolist;
                    $playlist->save();

                    return response()->json([
                        'status' => true,
                        'message' => "video added successfully",
                        'data' => [
                            "title"         => $playlist->title,
                            "category_id"   => $playlist->category_id,
                            "category_name" => $playlist->category->name,
                            "videos_count" => count(explode('-',$playlist->videos)),
                            "owner_id"      => $playlist->owner_id,
                            "owner_name"    => $playlist->owner->full_name(),
                            "unique_id"     => $playlist->unique_id ,
                            "created_at"    => $playlist->created_at->diffForHumans(),
                        ],
                    ]);
                }

            }else{
                return response()->json([
                    'status' => false,
                    'message' => "The video you selected is not available for viewing",

                ],400);
            }

        }else{
            return response()->json([
                'status' => false,
                'message' => "Plsylist not found",

            ],404);
        }


    }


    public function viewAllPlaylist(){

        if(isset($_GET['per_page'])){

            $playlists = Playlist::latest()->paginate($_GET['per_page']);

        }else{
            $playlists = Playlist::latest()->paginate(10);
        }

       // dd($playlists);
        $data_arr = array();

        foreach ($playlists as $playlist){

            $data_arr[] = array(
                "title"=> $playlist->title,
                "videos_count"=> count(explode('-',$playlist->videos)),
                "category_name"=> $playlist->category->name,
                "category_id"=> $playlist->category->unique_id,
                "owner_id"      => $playlist->owner_id,
                "owner_name"    => $playlist->owner->full_name(),
                "created_at"    => $playlist->created_at->diffForHumans(),
                );
        }

        if (count($playlists) > 0){
            return response()->json([
                'status' => true,
                'data' => $data_arr,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No playlist found',
            ]);
        }
    }



    public function playlistVideos(Request $request){

        $validator = Validator::make($request->all(), [
            'playlist_id' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status'=>false,
                'message' => 'All fields are required',
                'errors' =>$validator->errors()->all() ,
            ], 401);

        }

        $playlists = Playlist::where('unique_id',$request->playlist_id)->first();

        $playlists_videos = explode($playlists->videos);

        // dd($playlists);
        $data_arr = array();

        foreach ($playlists_videos as $video){

            $video_single = Video::where("unique_id",$video)->first();

            if(!empty($video_single)){
                if($video_single -> status == 1){

                    $data_arr[] = array(
                        "video_title"=> $video->title,
                        "category_name"=> $video->category->name,
                        "category_id"=> $video->category->unique_id,
                        "owner_id"      => $video->owner_id,
                        "owner_name"    => $video->owner->full_name(),
                        "created_at"    => $video->created_at->diffForHumans(),
                    );
                }
            }


        }

        if (count($playlists) > 0){
            return response()->json([
                'status' => true,
                'data' => $data_arr,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No playlist found',
            ]);
        }
    }



}
