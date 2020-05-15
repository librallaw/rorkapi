<?php

namespace App\Http\Controllers;

use App\Playlist;
use App\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function createVideo(Request $request){
        $request->validate([
           'video_title' => 'required',
           'video_category' => 'required',
           'video_file' => 'required'
        ]);

        $videoName = time(). '.' . $request->video_file->getClientOriginalExtension();

        $bannerName = time(). '.' . $request->video_banner->getClientOriginalExtension();

        $s3 = new \S3('AKIAYIMTQ7ZNUX4GSC57','kNc/d572ntscpDWcwamoTdA8nfqKiZymzBZ6RbgT' );

        if ($s3->putObjectFile($request->file('video_banner')->path(), "vcp-blw", "timeline/cei/products/images/" . $bannerName,
            \S3::ACL_PUBLIC_READ)) {
            $videoBanner = "http://vcp-blw.s3.amazonaws.com/timeline/cei/products/images/".$bannerName;
        }

        if ($s3->putObjectFile($request->file('video_file')->path(), "vcp-blw", "timeline/cei/products/images/" . $videoName,
            \S3::ACL_PUBLIC_READ)) {
            $videoFile = "http://vcp-blw.s3.amazonaws.com/timeline/cei/products/images/".$videoName;
        }

        $createVideo = new Video();
        $createVideo->video_title = $request->video_title;
        $createVideo->video_category = $request->video_category;
        $createVideo->video_banner = $videoBanner;
        $createVideo->video_file = $videoFile;
        $createVideo->save();

        return response()->json([
            'status' => true,
            'message' => "Video created successfully",
            'data' => $createVideo,
        ]);
    }

    public function showVideo($video_id){
        $video = Video::find($video_id);

        return response()->json([
            'status' => true,
            'data' => $video,
        ]);
    }

    public function updateVideo(Request $request, $id){
        $this->validate($request, [
            'video_title'    => 'required',
            'video_category' => 'required',
            'video_file'     => 'required'
        ]);

        $video = Video::where('id', $id)->first();
        $video->video_title = $request->video_title;
        $video->video_category = $request->video_category;
        $video->video_banner = $request->video_banner;
        $video->video_file = $request->video_file;
        $video->save();



//        dd($video);
        $currentVideo = $video->video_file;

        if ($request->video_file != $currentVideo){

            $videoName = time(). '.' . $request->video_file->getClientOriginalExtension();

            $s3 = new \S3('AKIAYIMTQ7ZNUX4GSC57','kNc/d572ntscpDWcwamoTdA8nfqKiZymzBZ6RbgT' );

            if ($s3->putObjectFile($request->file('video_file')->path(), "vcp-blw", "timeline/cei/products/images/" . $videoName,
                \S3::ACL_PUBLIC_READ)) {
                $videoFile = "http://vcp-blw.s3.amazonaws.com/timeline/cei/products/images/".$videoName;
            }

            $request->merge(['video_file' => $videoName]);

            $Video = "http://vcp-blw.s3.amazonaws.com/timeline/cei/products/images/".$currentVideo;

            if (file_exists($Video)){
                @unlink($Video);
            }
        }

        $video->update($request->all());

        return response()->json([
            'status' => true,
            'message' => "Video updated successfully",
            'data' => $video,
        ]);

    }


    public function allVideos(){
        $videos = Video::latest()->get();

        if (count($videos) > 0){
            return response()->json([
                'status' => true,
                'data' => $videos,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No videos found',
            ]);
        }


    }


    public function deleteVideo($id){
        $video = Video::find($id);

        if($video){
            $video->delete();

            return response()->json([
                'status' => true,
                'message' => 'Video deleted successfully',
            ]);

        } else {

            return response()->json([
                'status' => false,
                'message' => 'Not found',
            ]);
        }
    }
}
