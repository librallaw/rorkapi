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

//Route::get('test',function (){
//
//    $stored_videos = "1-20-3";
//
//    $converted_String = explode("-",$stored_videos);
//    $converted_String[] = "30";
//
//    $revered_string = implode('-',$converted_String);
//
//    dd($revered_string);
//
//
//});

Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');
//Route::group(['middleware' => 'auth:api'], function(){
//    Route::post('details', 'API\UserController@details');
//});

Route::group(['middleware' => 'auth:api'], function(){

    Route::get('view/videos', 'VideoController@allVideos')->name('all_videos');

    Route::get('view/playlists', 'PlaylistController@viewAllPlaylist')->name('all_playlist');

    Route::get('view/categories', 'CategoryController@viewCategories')->name('all_categories');

    Route::get('show/video/{video_id}', 'VideoController@showVideo')->name('show_video');

    Route::post('addTo/playlist/{id}', 'PlaylistController@addToPlaylist')->name('addToPlaylist');

    Route::post('create/video', 'VideoController@createVideo')->name('create_videos');

    Route::post('create/playlist', 'PlaylistController@createPlaylist')->name('create_playlist');

    Route::post('create/category', 'CategoryController@createCategory')->name('create_category');

    Route::patch('update/video/{id}', 'VideoController@updateVideo')->name('update_video');

    Route::get('delete/video/{id}', 'VideoController@deleteVideo')->name('delete_video');


});