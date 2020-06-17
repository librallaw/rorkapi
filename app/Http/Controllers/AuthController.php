<?php

namespace App\Http\Controllers;

use App\Libraries\Messenger;
use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //

    public function login(Request $request,Messenger $messenger){




        $validator = Validator::make($request->all(), [
            'email'     => 'required|string',
            'password'  => 'required|string',
        ]);



        if ($validator->fails()) {

            $errors =$validator->errors()->all();

            return response()->json([
                'status'=> false,
                'message' => 'Some error(s) occurred',
                'errors'=> $errors

            ]);

        }


        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){

            $user = $request->user();


            if(Auth::user()->active == 1){
                $tokenResult = $user->createToken('Personal Access Token');
                $token = $tokenResult->token;

                if ($request->remember_me)
                    $token->expires_at = Carbon::now()->addWeeks(1);
                $token->save();




                return response()->json([
                    'status' => true,
                    'message' => "success",
                    'data' =>
                        [
                            'access_token' => "Bearer ".$tokenResult->accessToken,
                            'token_type' => 'Bearer',
                            "user"=> [
                                "first_name"=> Auth::user()->first_name,
                                "last_name"=>  Auth::user()->last_name,
                                "email"=> Auth::user()->email,
                                "unique_id"=> Auth::user()->unique_id,
                            ]
                        ],
                    'expires_at' => Carbon::parse(
                        $tokenResult->token->expires_at
                    )->toDateTimeString()
                ]);
            }else{
                return response()->json([
                    'status'=> false,
                    'type'=> 'danger',
                    'message' => 'Account not active, please contact the Administrators',

                ]);

            }


        }
        else{
            return response()->json([
                'status'=> false,
                'type'=> 'danger',
                'message' => 'Authentication Failed',

            ]);
        }
    }
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */


    public function register(Request $request,  Messenger $messenger)
    {

        $validator = Validator::make($request->all(), [
            'first_name'     => 'required',
            'last_name'     => 'required',
            'email'         => 'required|email|unique:users',
            'password'      => 'required',
        ]);


        if ($validator->fails()) {

            return response()->json([
                'status'=>false,
                'message' => 'Sorry your registration could not be completed',
                'errors' =>$validator->errors()->all() ,
            ], 401);

        }

        //disable mass assignment protection.
        //please only use this if you know what u are doing, it is dangerous

        User::unguard();

        $unique_id = $messenger->randomId('4','unique_id','users');

        $user = new User();
        $user->first_name    = $request->first_name;
        $user->last_name    = $request->last_name;
        $user->email        = $request->email;
        $user->password     = bcrypt($request->password);
        $user->unique_id    = $unique_id;

        $user->save();

        User::reguard();

        $tokenResult = $user->createToken('Personal Access Token');

        return response()->json([
            'status'        => true,
            'message'       => 'Registration Successfully!',
            'data' =>
                [
                    'access_token' => "Bearer ".$tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    "user"=> [
                        "first_name"=> $user->first_name,
                        "last_name"=>  $user->last_name,
                        "email"=> $user->email,
                        "unique_id"=> $user->unique_id,
                    ],
                ],



        ], 200);
    }



    public function login2(Request $request,Messenger $messenger){




        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'country'     => 'required',
            'email'         => 'required',
        ]);



        if ($validator->fails()) {

            $errors =$validator->errors()->all();

            return response()->json([
                'status'=> false,
                'message' => 'Some error(s) occurred',
                'errors'=> $errors

            ]);

        }


        if(!empty($user = User::where('email', request('email') )->where('country' , request('country')) ->first())){

            Auth::login($user);

            $user = $request->user();


            if(Auth::user()->active == 1){
                $tokenResult = $user->createToken('Personal Access Token');
                $token = $tokenResult->token;

                if ($request->remember_me)
                    $token->expires_at = Carbon::now()->addWeeks(1);
                $token->save();


                return response()->json([
                    'status' => true,
                    'message' => "success",
                    'data' =>
                        [
                            'access_token' => "Bearer ".$tokenResult->accessToken,
                            'token_type' => 'Bearer',
                            "user"=> [
                                "name"=> Auth::user()->name,
                                "email"=> Auth::user()->email,
                                "country"=>  Auth::user()->country,
                                "unique_id"=> Auth::user()->unique_id,
                            ]
                        ],
                    'expires_at' => Carbon::parse(
                        $tokenResult->token->expires_at
                    )->toDateTimeString()
                ]);
            }else{
                return response()->json([
                    'status'=> false,
                    'type'=> 'danger',
                    'message' => 'Account not active, please contact the Administrators',

                ]);

            }


        }
        else{
            return response()->json([
                'status'=> false,
                'type'=> 'danger',
                'message' => 'Authentication Failed',

            ]);
        }
    }



    public function register2(Request $request,  Messenger $messenger)
    {

        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'country'     => 'required',
            'email'         => 'required|email|unique:users',
        ]);


        if ($validator->fails()) {

            return response()->json([
                'status'=>false,
                'message' => 'Sorry your registration could not be completed',
                'errors' =>$validator->errors()->all() ,
            ], 401);

        }

        //disable mass assignment protection.
        //please only use this if you know what u are doing, it is dangerous

        User::unguard();

        $unique_id = $messenger->randomId('4','unique_id','users');

        $user = new User();
        $user->name    = $request->name;
        $user->email        = $request->email;
        $user->country     = $request->country;
        $user->unique_id    = $unique_id;

        $user->save();

        User::reguard();

        $tokenResult = $user->createToken('Personal Access Token');

        return response()->json([
            'status'        => true,
            'message'       => 'Registration Successfully!',
            'data' =>
                [
                    'access_token' => "Bearer ".$tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    "user"=> [
                        "name"=> $user->name,
                        "email"=>  $user->email,
                        "country"=> $user->country,
                        "unique_id"=> $user->unique_id,
                    ],
                ],



        ], 200);
    }


}
