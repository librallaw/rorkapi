<?php

namespace App\Http\Controllers\SUPER;

use App\Banner;
use App\Http\Controllers\Controller;
use App\Libraries\Messenger;
use Illuminate\Http\Request;
use Validator;

class BannerController extends Controller
{
    //

    public function uploadBanner(Request $request,Messenger $messenger){

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'link' => 'required',
            'file' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status'=>false,
                'message' => 'All fields are required',
                'errors' =>$validator->errors()->all() ,
            ], 401);

        }

        $bannerName = time(). '.' . $request->file->getClientOriginalExtension();

        $s3 = new \S3(env('AWS_ACCESS_KEY_ID'),env('AWS_SECRET_ACCESS_KEY') );

        if ($s3->putObjectFile($request->file('file')->path(), "vcp-blw", "timeline/cei/products/images/".$bannerName,
            \S3::ACL_PUBLIC_READ)) {
            $videoBanner = "http://vcp-blw.s3.amazonaws.com/timeline/cei/products/images/".$bannerName;

            $createVideo = new Banner();
            $createVideo->title = $request->title;
            $createVideo->description = $request->description;
            $createVideo->file = $videoBanner;
            $createVideo->link = $request->link;
            $createVideo->status = 1;
            $createVideo->save();

            return response()->json([
                'status' => true,
                'message' =>  "Banner Successfully created ",
                'data' => [

                    "title"=> $createVideo->title,
                    "description"=> $createVideo->description,
                    "file"=> $createVideo->file,
                    "link"=> $createVideo->link,
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




    public function allBanners(){

        $banners = Banner::where('status',1)->latest()->get();

        if (count($banners) > 0){

            if(isset($_GET['per_page'])){
                $all_banners = Banner::orderBy("id","desc")->paginate($_GET['per_page']);
            }else{
                $all_banners = Banner::orderBy("id","desc")->paginate(10);
            }

            $data_arr = array();


            foreach ($all_banners as $banner){

                $data_arr[] =  array(
                    "title"=> $banner->title,
                    "description"=> $banner->description,
                    "file"=> $banner->file,
                    "link"=> $banner->link,
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
