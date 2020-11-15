<?php

namespace App\Http\Controllers\USER;

use App\Http\Controllers\Controller;
use App\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    //


    public function allBanners(){

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
