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




Route::get('/home/video/all',       'USER\CategoryController@HomecategoryVideo')     ->name('HomecategoryVideo');
Route::get('/rork/video/details/{video_id}',  'VideoController@showVideo');



Route::group(['middleware' => 'auth:api'], function(){


    Route::get('station/schedules/dates',    'StationController@allScheduleDates')      ->name('allScheduleDates');
    Route::post('station/profile',           'USER\StationController@stationProfile')   ->name('station_profile_user');
    Route::post('station/schedules',         'USER\StationController@stationSchedule')  ->name('station_schedule_user');
    Route::post('category/video',            'USER\CategoryController@categoryVideo')   ->name('categoryVideo');
    Route::get('view/stations',              'USER\StationController@LoadAllStations')  ->name('all_stations');
    Route::get('view/users',              'USER\StationController@LoadAllUsers')  ->name('all_users');

    Route::get("/banner/all",                'USER\BannerController@allBanners')        ->name("allBanners");
    Route::get("/slider/all",                'USER\SliderController@allSliders')        ->name("allSliders");


    Route::get('category/all',              'USER\CategoryController@viewCategories')  ->name('viewCategories');


    Route::get('statistics',                'StationController@statistics')            ->name('statistics');

    Route::get('station/live-url',          'StationController@stationLiveTV')         ->name('live_url');
    Route::get('station/profile',           'StationController@stationProfile')        ->name('station_profile');

    Route::post('station/statistics',       'StationController@stationstatistics')     ->name('stationstatistics');
    Route::post('update/station/profile', 'StationController@updateStationProfile')  ->name('update_station_profile');

    Route::post('schedule/create/', 'STATION\ScheduleController@createSchedule')  ->name('createSchedule');


    Route::post('schedule/view', 'USER\ScheduleController@displaySchedule')  ->name('displaySchedule');

    Route::get('view/playlists',  'PlaylistController@viewAllPlaylist') ->name('all_playlist');
    Route::get('view/categories', 'CategoryController@viewCategories')  ->name('all_categories');

    Route::post('playlist/create',    'PlaylistController@createPlaylist') ->name('create_playlist');
    Route::post('playlist/video/add', 'PlaylistController@addToPlaylist')  ->name('addToPlaylist');

    Route::post('create/category', 'CategoryController@createCategory') ->name('create_category');
    Route::post('update/category', 'CategoryController@updateCategory') ->name('updateCategory');

    Route::get('video/all',       'VideoController@allVideos')     ->name('all_videos');
    Route::get('video/station/all',       'VideoController@stationVideos')     ->name('stationVideos');
    Route::post('video/details',  'VideoController@showVideo')     ->name('show_video');
    Route::post('video/upload',   'VideoController@uploadVideo')   ->name('uploadVideo');
    Route::post('video/update',   'VideoController@updateVideo')   ->name('update_video');
    Route::post('video/delete',   'VideoController@deleteVideo')   ->name('delete_video');
    Route::post('video/activate', 'VideoController@activateVideo') ->name('activateVideo');

    Route::group(['prefix'=>'super'],function (){

        Route::post("/slider/upload/banner",'SUPER\SliderController@uploadBanner')->name("sliderUploadBanner");
        Route::post("/slider/upload/video",'SUPER\SliderController@uploadVideo')->name("sliderUploadVideo");
        Route::get("/slider/all",'SUPER\SliderController@allSliders')->name("sliderAllSliders");

        Route::post("/banner/upload",'SUPER\BannerController@uploadBanner')->name("uploadBanner");
        Route::post("/banner/update",'SUPER\BannerController@updateBanner')->name("updateBanner");
        Route::post("/banner/delete",'SUPER\BannerController@deleteBanner')->name("deleteBanner");

        Route::post("/video/upload",'SUPER\VideoController@uploadVideo')->name("uploadVideo");
        Route::post("/video/update",'SUPER\VideoController@updateVideo')->name("updateVideo");
        Route::post('video/activate', 'SUPER\VideoController@activateVideoSuper') ->name('activateVideoSuper');
        Route::post('video/deactivate', 'SUPER\VideoController@deactivateVideoSuper') ->name('deactivateVideoSuper');

        Route::post("/featured-video/create",'SUPER\VideoController@createFeaturedVideos')->name("createFeaturedVideos");
        Route::get("/featured-videos/all",'SUPER\VideoController@allFeaturedVideos')->name("allFeaturedVideos");
        Route::post("/featured-videos/remove",'SUPER\VideoController@removeFeaturedVideo')->name("removeFeaturedVideo");

        Route::post("/search/videos",'SUPER\VideoController@searchVideos')->name("searchVideos");

        Route::post("/create/announcement",'SUPER\AnnouncementController@createAnnouncement')->name("createAnnouncement");
        Route::get("/announcement/all",'SUPER\AnnouncementController@allAnnouncement')->name("allAnnouncement");
        Route::post("/announcement/remove",'SUPER\AnnouncementController@removeAnnouncement')->name("removeAnnouncement");
    });



});
