<?php

namespace App\Http\Controllers;

use App\StationSchedule;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StationController extends Controller
{
    public function LoadAllStations(){

        $stations = User::where('active',1)->latest()->get();


        if (count($stations) > 0){

            if(isset($_GET['per_page'])){

                $stations = User::orderBy("id","desc")->paginate($_GET['per_page']);

            }else{
                $stations = User::orderBy("id","desc")->paginate(10);
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
                    "numOfComments"=> $station->num_of_comments,
                    "isLoginRequired"=> $isLoginRequired,
                    "chatRoom"=> $station->chat_room,
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

        $station = User::where('unique_id', Auth::user()->unique_id)->first();

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

        $station = User::where('unique_id', Auth::user()->unique_id)->first();

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
}
