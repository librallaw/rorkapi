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



Route::post('login', 'AuthController@login');
Route::post('register', 'AuthController@register');


Route::group(['middleware' => 'auth:api'], function(){

    Route::get('view/stations',    'StationController@LoadAllStations') ->name('all_stations');
    Route::get('station/live-url',    'StationController@stationLiveTV') ->name('live_url');
    Route::get('station/profile',    'StationController@stationProfile') ->name('station_profile');
    Route::post('update/station/profile', 'StationController@updateStationProfile')  ->name('update_station_profile');
    Route::post('create/station/schedule', 'StationController@stationSchedule')  ->name('create_stationSchedule');

    Route::get('view/playlists',  'PlaylistController@viewAllPlaylist') ->name('all_playlist');
    Route::get('view/categories', 'CategoryController@viewCategories')  ->name('all_categories');

    Route::post('playlist/create',    'PlaylistController@createPlaylist') ->name('create_playlist');
    Route::post('playlist/video/add', 'PlaylistController@addToPlaylist')  ->name('addToPlaylist');

    Route::post('create/category', 'CategoryController@createCategory') ->name('create_category');

    Route::get('video/all',       'VideoController@allVideos')     ->name('all_videos');
    Route::post('video/details',  'VideoController@showVideo')     ->name('show_video');
    Route::post('video/upload',   'VideoController@uploadVideo')   ->name('uploadVideo');
    Route::post('video/update',   'VideoController@updateVideo')   ->name('update_video');
    Route::post('video/delete',   'VideoController@deleteVideo')   ->name('delete_video');
    Route::post('video/activate', 'VideoController@activateVideo') ->name('activateVideo');


});