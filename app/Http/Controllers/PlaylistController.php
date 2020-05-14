<?php

namespace App\Http\Controllers;

use App\Playlist;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    public function createPlaylist(Request $request)
    {
        $request->validate([
           'playlist_title' => 'required',
           'category_name'  => 'required',
           'banner'         => 'required'
        ]);

        $newPlaylist = new Playlist();
        $newPlaylist->playlist_title = $request->playlist_title;
        $newPlaylist->category_name = $request->category_name;
        $newPlaylist->videos = $request->videos;
        $newPlaylist->banner = $request->banner;
        $newPlaylist->save();

        return response()->json([
            'status' => true,
            'message' => "Playlist created successfully",
            'data' => $newPlaylist,
        ]);
    }

    public function addToPlaylist(Request $request){
        $request->validate([
           'playlist_id' => 'required'
        ]);
        $playlist = Playlist::where('id', $request->playlist_id)->first();
        $videolist = $playlist->videos;
        $convertToArray = explode("-",$videolist);
        $convertToArray[] = '20';
        $addtolist = implode('-', $convertToArray);

//        return $addtolist;

        return response()->json([
            'status' => true,
            'message' => "video added successfully",
            'data' => $addtolist,
        ]);


    }
}
