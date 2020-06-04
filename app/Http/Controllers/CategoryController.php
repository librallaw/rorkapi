<?php

namespace App\Http\Controllers;

use App\Category;
use App\Libraries\Messenger;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function createCategory(Request $request,Messenger $messenger){
        $request->validate([
           'name' => 'required|unique:categories',
        ]);

        $newCategory = new Category();
        $newCategory->name = $request->name;
        $newCategory->unique_id = "CAT-".$messenger-> randomId('4','unique_id','categories');
        $newCategory->save();

        return response()->json([
            'status' => true,
            'message' => "Category created successfully",
            'data' => [
                "name"=> $newCategory->name,
                "unique_id"=> $newCategory->unique_id,
            ],
        ]);
    }


    public function viewCategories(){

        if(isset($_GET['per_page'])){
            $categories = Category::paginate($_GET['per_page']);
        }else{
            $categories = Category::paginate(10);
        }


        if (count($categories) > 0){


            $data_arr = array();

            foreach ($categories as $category){

                $data_arr[] = array(
                    "name"=> $category->name,
                    "unique_id"=> $category->unique_id
                );
            }



            return response()->json([
                'status' => true,
                'data' => $data_arr,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No category found',
            ]);
        }
    }
}
