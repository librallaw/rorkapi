<?php

namespace App\Http\Controllers\SUPER;

use App\Announcement;
use App\Http\Controllers\Controller;
use App\Libraries\Messenger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
    public function createAnnouncement(Request $request, Messenger $messenger){
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'button_text' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
               'status' => false,
               'message' => 'All fields are required',
                'errors' => $validator->errors()->all(),
            ], 401);
        }

        $unique_id = $messenger->randomId('6','unique_id','videos');

        $createAnnouncement = new Announcement();
        $createAnnouncement->unique_id = $unique_id;
        $createAnnouncement->title = $request->title;
        $createAnnouncement->description = $request->description;
        $createAnnouncement->button_text = $request->button_text;
        $createAnnouncement->save();

        return response()->json([
            'status' => true,
            'message' =>  "Announcement created successfully",
            'data' => [

                "title"=> $createAnnouncement->title,
                "description"=> $createAnnouncement->description,
                "button_text"=> $createAnnouncement->button_text,
                "created_at"=> $createAnnouncement->created_at->diffForHumans(),

            ],
        ]);

    }


    public function allAnnouncement(){
        $announcements = Announcement::latest()->get();

        if (count($announcements) > 0){

            $data_arr = array();

            foreach ($announcements as $announcement){

                $data_arr[] = array(
                  'title'       => $announcement->title,
                  'description' => $announcement->description,
                  'button_text' => $announcement->button_text,
                  'created_at'  => $announcement->created_at->diffForHumans()
                );
            }

            return response()->json([
                'status' => true,
                'data'    => $data_arr
            ], 200);

        } else {
            return response()->json([
                'status' => false,
                'message' => 'No Announcement found',
            ]);
        }
    }


    public function removeAnnouncement(Request $request){
        $validator = Validator::make($request->all(), [
            'announcement_id' => 'required',
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'All fields are required',
                'errors' => $validator->errors()->all(),
            ], 401);
        }

        $announcement = Announcement::where('unique_id',$request->announcement_id)->first();

        if($announcement){

            $announcement->delete();

            return response()->json([
                'status' => true,
                'message' => 'Announcement removed successfully',
            ],200);


        } else {

            return response()->json([
                'status' => false,
                'message' => 'Not found',
            ],404);
        }
    }
}
