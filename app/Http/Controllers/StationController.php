<?php

namespace App\Http\Controllers;

use App\Libraries\Messenger;
use App\ScheduleDate;
use App\Station;
use App\StationSchedule;
use App\User;
use App\Video;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StationController extends Controller
{
    public function LoadAllStations(){

        $stations = User::where('active',1)->where('level',2)->latest()->get();


        if (count($stations) > 0){

            if(isset($_GET['per_page'])){

                $stations = User::orderBy("id","desc")->where('level',2)->paginate($_GET['per_page']);

            }else{
                $stations = User::orderBy("id","desc")->where('level',2)->paginate(10);
            }


            $data_arr = array();


            foreach ($stations as $station){

                $listPhoneNumbers = $station->phone_number;
                $phoneNumbers = explode(',', $listPhoneNumbers);

                $isDvrEnabled =  ($station->is_dvr_enabled == 1) ? true : false;
                $isLiveChatEnabled =  ($station->is_live_chat_enabled == 1) ? true : false;
                $isLoginRequired =  ($station->is_login_required == 1) ? true : false;

                $data_arr[] =  array(
                    "stationName"=> $station->name,
                    "description"=> $station->description,
                    "url"=> $station->url,
                    "email"=> $station->email,
                    "thumbnail"=> $station->thumbnail,
                    "paypal_id"=> $station->paypal_id,
                    "phoneNumbers"=> $phoneNumbers,
                    "id"=> $station->unique_id,
                    "donateurl"=> $station->donate_url,
                    "weburl"=> $station->web_url,
                    "scheduleurl"=> $station->schedule_url,
                    "kingspaycode"=> $station->kings_pay_code,
                    "isDvrEnabled"=> $isDvrEnabled,
                    "isLiveChatEnabled"=> $isLiveChatEnabled,
                    "numOfComments"=> $station->numOfComments,
                    "isLoginRequired"=> $isLoginRequired,
                    "chatRoom"=> $station->chatRoom,
                    "created_at"=> $station->created_at->diffForHumans(),
                );

            }

            return response()->json([
                'status' => true,
                'data' => $data_arr,
            ],200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No station found',
            ],404);
        }

    }


    public function updateStationProfile(Request $request){

        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'description'     => 'required',
            'email'         => 'required|email',
            'url'         => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status'=>false,
                'message' => 'All fields are required',
                'errors' =>$validator->errors()->all() ,
            ], 401);

        }

        $station = Station::where('unique_id', Auth::user()->unique_id)->first();

        if ($station) {
            if($station->unique_id == Auth::user()->unique_id) {

                $listPhoneNumbers = $station->phone_number;
                $phoneNumbers = explode(',',$listPhoneNumbers);
                foreach ($phoneNumbers as $phoneNumber){
                    if ($request->phone_number != $phoneNumber){
                        $phoneNumbers = explode(',', $listPhoneNumbers);
                        $phoneNumbers[] = $request->phone_number;
                        $phone_numbers_string = implode(',', $phoneNumbers);
                    } else {

                        $phoneNumbers = explode(',', $listPhoneNumbers);
                        $phone_numbers_string = implode(',', $phoneNumbers);
                    }
                }


                $station->name = $request->name;
                $station->description = $request->description;
                $station->email = $request->email;
                $station->url = $request->url;
                $station->thumbnail = $request->thumbnail;
                $station->paypal_id = $request->paypal_id;
                $station->phone_number = $phone_numbers_string;
                $station->donate_url = $request->donate_url;
                $station->web_url = $request->web_url;
                $station->schedule_url = $request->schedule_url;
                $station->kings_pay_code = $request->kings_pay_code;
                $station->is_dvr_enabled = $request->is_dvr_enabled;
                $station->is_live_chat_enabled = $request->is_live_chat_enabled;
                $station->is_login_required = $request->is_login_required;
                $station->chat_room = $request->chat_room;
                $station->save();

                return response()->json([
                    'status' => true,
                    'data'  => $station,
                    'message' => "Station updated successfully",
                ]);
            }else {
                return response()->json([
                    'status' => false,
                    'message' => 'Sorry you can only update your own account',
                ],401);
            }

        } else {
            return response()->json([
                'status' => false,
                'message' => "Station Not Found",
            ]);
        }


    }


    public function stationProfile(){

        $station = Station::where('unique_id', Auth::user()->unique_id)->first();

        if ($station) {

            $listPhoneNumbers = $station->phone_number;
            $phoneNumbers = explode(',', $listPhoneNumbers);

            $isDvrEnabled =  ($station->is_dvr_enabled == 1) ? true : false;
            $isLiveChatEnabled =  ($station->is_live_chat_enabled == 1) ? true : false;
            $isLoginRequired =  ($station->is_login_required == 1) ? true : false;

            $profile[] =  array(
                "stationName"=> $station->name,
                "description"=> $station->description,
                "url"=> $station->url,
                "email"=> $station->email,
                "thumbnail"=> $station->thumbnail,
                "paypal_id"=> $station->paypal_id,
                "phoneNumbers"=> $phoneNumbers,
                "donateurl"=> $station->donate_url,
                "weburl"=> $station->web_url,
                "scheduleurl"=> $station->schedule_url,
                "kingspaycode"=> $station->kings_pay_code,
                "isDvrEnabled"=> $isDvrEnabled,
                "isLiveChatEnabled"=> $isLiveChatEnabled,
                "numOfComments"=> $station->num_of_comments,
                "isLoginRequired"=> $isLoginRequired,
                "chatRoom"=> $station->chat_room,
                "unique_id"=> $station->unique_id,
                "created_at"=> $station->created_at->diffForHumans(),
            );

            return response()->json([
                'status' => true,
                'data'  => $profile
            ]);

        } else {
            return response()->json([
                'status' => false,
                'message' => "Please sign up for an account",
            ]);
        }
    }


    public function stationSchedule(Request $request){
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'start_time'     => 'required',
            'end_time'         => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status'=>false,
                'message' => 'All fields are required',
                'errors' => $validator->errors()->all(),
            ], 401);

        }

        $station  = User::where('unique_id', \auth()->user()->unique_id)->first();

        if ($station){
            $newSchedule = new StationSchedule();
            $newSchedule->title = $request->title;
            $newSchedule->station_id = \auth()->user()->unique_id;
            $newSchedule->schedule_date = \auth()->user()->unique_id;
            $newSchedule->start_time = strtotime($request->start_time);
            $newSchedule->end_time = strtotime($request->end_time);
            $newSchedule->save();

            return response()->json([
                'status' => true,
                'data'  => $newSchedule
            ]);

        } else {
            return response()->json([
                'status' => false,
                'message'  => 'Sorry you can only create schedules for your own station',
            ]);
        }


    }


    public function createScheduleDate(Request $request, Messenger $messenger){
        $validator = Validator::make($request->all(), [
            'date'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'=>false,
                'message' => 'All fields are required',
                'errors' => $validator->errors()->all(),
            ], 401);
        }

        $unique_id = $messenger->randomId('6','unique_id','schedule_dates');
        $station  = User::where('unique_id', \auth()->user()->unique_id)->first();

        if ($station){
            $scheduleDate = new ScheduleDate();
            $scheduleDate->date = strtotime($request->date);
            $scheduleDate->unique_id = $unique_id;
            $scheduleDate->station_id = \auth()->user()->unique_id;
            $scheduleDate->save();

            return response()->json([
                'status' => true,
                'message' => 'Schedule date was created successfuly',
                'data'  => $scheduleDate
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message'  => 'Sorry you can only create schedule date for your own station',
            ]);
        }

    }


    public function createSchedule(Request $request){
        $validator = Validator::make($request->all(), [
            'date_id'     => 'required',
            'title'     => 'required',
            'start_time'     => 'required',
            'end_time'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'All fields are required',
                'errors'  => $validator->errors()->all(),
            ], 401);
        }

        $station  = User::where('unique_id', \auth()->user()->unique_id)->first();

        $scheduleDate = ScheduleDate::where('unique_id', $request->date_id)->where('station_id', \auth()->user()->unique_id)->first();

        if ($station){
            if ($scheduleDate){
                $newSchedule = new StationSchedule();
                $newSchedule->title = $request->title;
                $newSchedule->station_id = \auth()->user()->unique_id;
                $newSchedule->schedule_date = $scheduleDate->date;//date('D, M d',$scheduleDate->date);
                $newSchedule->start_time = strtotime($request->start_time);
                $newSchedule->end_time = strtotime($request->end_time);
                $newSchedule->save();

                return response()->json([
                    'status' => true,
                    'data'  => $newSchedule
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message'  => 'Please pick a Schedule date or the schedule date is incorrect'
                ]);
            }


        } else {
            return response()->json([
                'status' => false,
                'message'  => 'Sorry you can only create schedules for your own station',
            ]);
        }
    }


    public function stationLiveTV(Request $request){

        $validator = Validator::make($request->all(), [
            'station_id' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status'=>false,
                'message' => 'All fields are required',
                'errors' =>$validator->errors()->all() ,
            ], 401);

        }

        $station = User::where('unique_id',$request->station_id)->first();

        if($station){

            if($station->active  == 1){


                return response()->json([
                    'status' => true,
                    'data' => $station->live_url,
                ],200);
            }else{

                return response()->json([
                    'status' => false,
                    'message' => 'This Station is not Active',
                ],400);

            }

        } else {

            return response()->json([
                'status' => false,
                'message' => 'Not found',
            ],404);
        }
    }


    public function statistics(){

        $users          = User::where('level', 1)->get();
        $recent_users   = User::where('level', 1)->limit(5)->get();
        $stations       = User::where('level', 2)->get();
        $videos         = Video::latest()->get();
        $recent_videos  = Video::latest()->limit(10)->get();
        $recent_stations  = Station::latest()->limit(5)->get();

        $response = array('status' => true, 'data' => array(

            'users' => $users,
            'stations' => $stations,
            'videos' => $videos,
            'recent_users' => $recent_users,
            'recent_stations' => $recent_stations,
            'recent_videos' => $recent_videos,

        ));

        return response($response, 200);
    }


    public function stationstatistics(Request $request){

        $validator = Validator::make($request->all(), [
            'station_id' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status'=>false,
                'message' => 'All fields are required',
                'errors' =>$validator->errors()->all() ,
            ], 401);

        }



        $stationVideos      = Video::where('owner_id', \auth()->user()->unique_id)->latest()->limit(5)->get();

        if (count($stationVideos) > 0){

            $data_arr = array();

            foreach ($stationVideos as $stationVideo){

                $data_arr[] =  array(
                    "title"=> $stationVideo->title,
                    "banner"=> $stationVideo->banner,
                    "file"=> $stationVideo->file,
                    "video_id"=> $stationVideo->unique_id,
                    //"category"=> $stationVideo->category->name,
                    "category_id"=> $stationVideo->category_id,
                    "owner_id"=> $stationVideo->owner->unique_id,
                    "owner_name"=> $stationVideo->owner->full_name(),
                    "created_at"=> $stationVideo->created_at->diffForHumans(),
                );


            }

            $response = array('status' => true, 'data' => array(
                'recent_videos' => $data_arr
            ));

            return response($response, 200);



        } else {
            return response()->json([
                'status' => false,
                'message' => 'No videos found',
            ],404);
        }

    }



    public function allScheduleDates(Request $request){

        $stationScheduleDates  = ScheduleDate::where('station_id', \auth()->user()->unique_id)->latest()->get();

        if (count($stationScheduleDates) > 0){

            $data_arr = array();

            foreach ($stationScheduleDates as $stationScheduleDate){

                $data_arr[] =  array(
                    "date_id"=> $stationScheduleDate->unique_id,
                    "Schedule_date"=> date('D, M d',$stationScheduleDate->date),
                );

            }


            return response()->json([
                'status' => true,
                'data' => $data_arr,
            ],200);



        } else {
            return response()->json([
                'status' => false,
                'message' => 'No schedule date has been created',
            ],404);
        }

    }



}
