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

                $data_arr[] =  array(
                    "name"=> $station->name,
                    "description"=> $station->description,
                    "live_url"=> $station->live_url,
                    "email"=> $station->email,
                    "unique_id"=> $station->unique_id,
                    "created_at"=> $station->created_at->diffForHumans(),
                    "updated_at"=> $station->updated_at->diffForHumans(),
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
            'live_url'         => 'required',
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

                $station->name = $request->name;
                $station->description = $request->description;
                $station->email = $request->email;
                $station->live_url = $request->live_url;
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

            $profile[] =  array(
                "name"=> $station->name,
                "description"=> $station->description,
                "live_url"=> $station->live_url,
                "email"=> $station->email,
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
            $newSchedule->start_time = $request->start_time;
            $newSchedule->end_time = $request->end_time;
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
