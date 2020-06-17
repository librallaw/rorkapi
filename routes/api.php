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


Route::post('/user/login', 'AuthController@login2');
Route::post('/user/register', 'AuthController@register2');


Route::group(['middleware' => 'auth:api'], function(){


    Route::get('station/schedules/dates',    'StationController@allScheduleDates')      ->name('allScheduleDates');
    Route::post('station/profile',           'USER\StationController@stationProfile')   ->name('station_profile_user');
    Route::post('station/schedules',         'USER\StationController@stationSchedule')  ->name('station_schedule_user');
    Route::post('category/video',            'USER\CategoryController@categoryVideo')   ->name('categoryVideo');
    Route::get('view/stations',              'USER\StationController@LoadAllStations')  ->name('all_stations');

    Route::get("/banner/all",                'USER\BannerController@allBanners')        ->name("allBanners");


    Route::get('statistics',    'StationController@statistics') ->name('statistics');

    Route::get('station/live-url',    'StationController@stationLiveTV') ->name('live_url');
    Route::get('station/profile',    'StationController@stationProfile') ->name('station_profile');

    Route::post('station/statistics',       'StationController@stationstatistics')     ->name('stationstatistics');
    Route::post('update/station/profile', 'StationController@updateStationProfile')  ->name('update_station_profile');
    Route::post('create/schedule/date', 'StationController@createScheduleDate')  ->name('createScheduleDate');
    Route::post('create/schedule', 'StationController@createSchedule')  ->name('createSchedule');

    Route::get('view/playlists',  'PlaylistController@viewAllPlaylist') ->name('all_playlist');
    Route::get('view/categories', 'CategoryController@viewCategories')  ->name('all_categories');

    Route::post('playlist/create',    'PlaylistController@createPlaylist') ->name('create_playlist');
    Route::post('playlist/video/add', 'PlaylistController@addToPlaylist')  ->name('addToPlaylist');

    Route::post('create/category', 'CategoryController@createCategory') ->name('create_category');

    Route::get('video/all',       'VideoController@allVideos')     ->name('all_videos');
    Route::post('video/station/all',       'VideoController@stationVideos')     ->name('stationVideos');
    Route::post('video/details',  'VideoController@showVideo')     ->name('show_video');
    Route::post('video/upload',   'VideoController@uploadVideo')   ->name('uploadVideo');
    Route::post('video/update',   'VideoController@updateVideo')   ->name('update_video');
    Route::post('video/delete',   'VideoController@deleteVideo')   ->name('delete_video');
    Route::post('video/activate', 'VideoController@activateVideo') ->name('activateVideo');

    Route::group(['prefix'=>'super'],function (){

        Route::post("/banner/upload",'SUPER\BannerController@uploadBanner')->name("uploadBanner");
    });



});