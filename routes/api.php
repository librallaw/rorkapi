<?php

use App\Http\Controllers\VideoController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('test',function (){

    $stored_videos = "1-20-3";

    $converted_String = explode("-",$stored_videos);
    $converted_String[] = "30";

    $revered_string = implode('-',$converted_String);

    dd($revered_string);


});

Route::get('view/videos', 'VideoController@allVideos')->name('all_videos');

Route::get('show/video/{video_id}', 'VideoController@showVideo')->name('show_video');

Route::post('addTo/playlist', 'PlaylistController@addToPlaylist')->name('addToPlaylist');

Route::post('create/video', 'VideoController@createVideo')->name('create_videos');

Route::post('create/playlist', 'PlaylistController@createPlaylist')->name('create_playlist');

Route::put('update/video/{video_id}', 'VideoController@updateVideo')->name('update_videos');
