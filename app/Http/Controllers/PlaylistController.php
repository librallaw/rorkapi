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

    public function addToPlaylist(Request $request, $id){

        $request->validate([
           'videos' => 'required',
        ]);

        $playlist = Playlist::where('id', $id)->first();
        $videolist = $playlist->videos;
        if ($videolist == null){

            $convertToArray = explode(' ', $videolist);
            $convertToArray[] = $request->videos;
            $addedtolist = implode(' ', $convertToArray);
            $playlist->videos = $addedtolist;
            $playlist->save();

        } else {

            $convertToArray = explode('-', $videolist);
            $convertToArray[] = $request->videos;
            $addedtolist = implode('-', $convertToArray);
            $playlist->videos = $addedtolist;
            $playlist->save();
        }


        return response()->json([
            'status' => true,
            'message' => "video added successfully",
            'data' => $playlist,
        ]);

    }


    public function viewAllPlaylist(){
        $playlists = Playlist::latest()->get();

        if (count($playlists) > 0){
            return response()->json([
                'status' => true,
                'data' => $playlists,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No playlist found',
            ]);
        }
    }
}
