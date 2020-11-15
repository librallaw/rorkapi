<?php

namespace App\Http\Controllers\USER;

use App\Http\Controllers\Controller;
use App\ScheduleDate;
use App\Station;
use App\StationSchedule;
use App\User;
use Illuminate\Http\Request;
use Validator;
class StationController extends Controller
{
    //

    public function stationProfile(Request $request){

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);


        if ($validator->fails()) {

            return response()->json([
                'status'=>false,
                'message' => 'All fields are required',
                'errors' =>$validator->errors()->all() ,
            ], 401);

        }




        $station = Station::where('unique_id', $request->id)->first();

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
                "numOfComments"=> $station->numOfComments,
                "isLoginRequired"=> $isLoginRequired,
                "chatRoom"=> $station->chatRoom,
                "unique_id"=> $station->unique_id,
                "created_at"=> $station->created_at->diffForHumans(),
            );

            return response()->json([
                'status' => true,
                'data'  => $profile
            ],200);

        } else {
            return response()->json([
                'status' => false,
                'message' => "Station not found",
            ],404);
        }
    }


    public function stationSchedule(Request $request){

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);


        if ($validator->fails()) {

            return response()->json([
                'status'=>false,
                'message' => 'All fields are required',
                'errors' =>$validator->errors()->all() ,
            ], 401);

        }

        $station = User::where('unique_id', $request->id)->first();

        if ($station){
            $today = time();
            $schedules = ScheduleDate::latest()->get();

            $schedules_mother = array();

            foreach ($schedules as $schedule){

                $schedules_mother[] =  array(
                    "scheduleDate"=> date('D, M d',$schedule->date),
                    "schedules" =>  StationSchedule::where('schedule_date', $schedule->date)->latest()->get(),

                );

            }

//            if ((int)$schedule->date >= (int)$today){

                return response()->json([
                    'status' => true,
                    'data'  => $schedules_mother
                ],200);

//            }else {
//                return response()->json([
//                    'status' => false,
//                    'message' => "No upcoming schedule has been created",
//                ],404);
//            }





        }
    }


    public function LoadAllStations(){

        $stations = Station::where('active',1)->latest()->get();


        if (count($stations) > 0){

            if(isset($_GET['per_page'])){

                $stations = Station::where('active',1)->orderBy("id","asc")->paginate($_GET['per_page']);

            }else{
                $stations = Station::where('active',1)->orderBy("id","asc")->get();
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


    public function LoadAllUsers(){

        $users = User::where('active',1)->latest()->get();


        if (count($users) > 0){

            if(isset($_GET['per_page'])){

                $users = User::where('active',1)->orderBy("id","asc")->paginate($_GET['per_page']);

            }else{
                $users = User::where('active',1)->orderBy("id","asc")->get();
            }


            $data_arr = array();


            foreach ($users as $user){

                $data_arr[] =  array(
                    "name"=> $user->name,
                    "email"=> $user->email,
                    "country"=> $user->country,
                    "phone_number"=> $user->phone_number,
                    "created_at"=> $user->created_at->diffForHumans(),
                );

            }

            return response()->json([
                'status' => true,
                'data' => $data_arr,
            ],200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No User found',
            ],404);
        }

    }



}
