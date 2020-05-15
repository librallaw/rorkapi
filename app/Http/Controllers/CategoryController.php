<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function createCategory(Request $request){
        $request->validate([
           'name' => 'required',
        ]);

        $newCategory = new Category();
        $newCategory->name = $request->name;
        $newCategory->unique_id = time();
        $newCategory->save();

        return response()->json([
            'status' => true,
            'message' => "Category created successfully",
            'data' => $newCategory,
        ]);
    }


    public function viewCategories(){
        $categories = Category::all();

        if (count($categories) > 0){
            return response()->json([
                'status' => true,
                'data' => $categories,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No category found',
            ]);
        }
    }
}
