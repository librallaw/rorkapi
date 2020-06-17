<?php

namespace App\Http\Controllers\USER;

use App\Banner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class BannerController extends Controller
{
    //

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
