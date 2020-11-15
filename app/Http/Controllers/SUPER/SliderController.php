<?php

namespace App\Http\Controllers\SUPER;

use App\Http\Controllers\Controller;
use App\Libraries\Messenger;
use App\Slider;
use Validator;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    //
    public function uploadBanner(Request $request,Messenger $messenger){


        //file [image,video]

        //file_type [external,video,catchup]

        //uri[link,id,

        $validator = Validator::make($request->all(), [
            'file' => 'required',
            'uri_type' => 'required',
            'uri' => 'required',
        ]);


        if ($validator->fails()) {

            return response()->json([
                'status'=>false,
                'message' => 'All fields are required',
                'errors' =>$validator->errors()->all() ,
            ], 401);

        }

        $bannerName = time(). '.' . $request->file->getClientOriginalExtension();


        //dd("I got here");

        $s3 = new \S3(env('AWS_ACCESS_KEY_ID'),env('AWS_SECRET_ACCESS_KEY') );

        if ($s3->putObjectFile($request->file('file')->path(), "Testvoda", "livetvapp/slider/images/".$bannerName,
            \S3::ACL_PUBLIC_READ)) {

            $videoBanner = "http://s3.amazonaws.com/Testvoda/livetvapp/slider/images/".$bannerName;

            $createVideo = new Slider();
            $createVideo->file_type = "image";
            $createVideo->uri_type = $request->uri_type;
            $createVideo->file = $videoBanner;
            $createVideo->uri = $request->uri;
            $createVideo->status = 1;
            $createVideo->save();

            return response()->json([
                'status' => true,
                'message' =>  "Banner Successfully created ",
                'data' => [

                    "file_type"=> $createVideo->file_type,
                    "uri"=> $createVideo->uri,
                    "uri_type"=> $createVideo->uri_type,
                    "file"=> $createVideo->file,
                    "created_at"=> $createVideo->created_at->diffForHumans(),

                ],
            ]);

        }else{

            return response()->json([
                'status' => false,
                'message' =>  "An error occurred while uploading your banner ",
            ],401);

        }

    }




    public function uploadVideo(Request $request,Messenger $messenger){


        //file [image,video]

        //file_type [external,video,catchup]

        //uri[link,id,

        $validator = Validator::make($request->all(), [
            'file' => 'required',
        ]);


        if ($validator->fails()) {

            return response()->json([
                'status'=>false,
                'message' => 'All fields are required',
                'errors' =>$validator->errors()->all() ,
            ], 401);

        }

        $bannerName = time(). '.' . $request->file->getClientOriginalExtension();


        //dd("I got here");

        $s3 = new \S3(env('AWS_ACCESS_KEY_ID'),env('AWS_SECRET_ACCESS_KEY') );

        if ($s3->putObjectFile($request->file('file')->path(), "Testvoda", "livetvapp/slider/video/".$bannerName,
            \S3::ACL_PUBLIC_READ)) {
            $videoBanner = "http://s3.amazonaws.com/Testvoda/livetvapp/slider/video/".$bannerName;

            $createVideo = new Slider();
            $createVideo->file_type = "video";
            $createVideo->file = $videoBanner;
            $createVideo->status = 1;
            $createVideo->save();

            return response()->json([
                'status' => true,
                'message' =>  "Video Slider Successfully created ",
                'data' => [

                    "file_type"=> $createVideo->file_type,
                    "file"=> $createVideo->file,
                    "created_at"=> $createVideo->created_at->diffForHumans(),

                ],
            ]);

        }else{
            return response()->json([
                'status' => false,
                'message' =>  "An error occurred while uploading your video ",
            ],401);

        }

    }


    public function allSliders(){

        $banners = Slider::where('status',1)->latest()->get();

        if (count($banners) > 0){

            if(isset($_GET['per_page'])){
                $all_banners = Slider::orderBy("id","desc")->paginate($_GET['per_page']);
            }else{
                $all_banners = Slider::orderBy("id","desc")->paginate(10);
            }

            $data_arr = array();


            foreach ($all_banners as $banner){

                    $data_arr[] =  array(
                        "contentType"=> $banner->file_type,
                        "bannerType"=> $banner->uri_type,
                        "contentID"=> $banner->uri,
                        "file"=> $banner->file,
                    );


            }


            return response()->json([
                'status' => true,
                'data' =>$data_arr,
            ],200);

        } else {
            return response()->json([
                'status' => false,
                'message' => 'No banner found',
            ],404);
        }

    }
}
