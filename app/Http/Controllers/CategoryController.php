<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\FixitTrait;
use App\Models\Category;
use App\Models\Contractor;

class CategoryController extends Controller
{
    use FixitTrait;

    public function getAllCategories()
    {
        $categories = Category::all();
        return $this->SuccessResponse($categories,'All categories',200);
    }



    public function addCategory(Request $request)
    {
        $validation=Validator::make($request->all(),[
            'category_name' => 'required|string|max:255',
            'category_image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        //تأكد من ان الصورة تم تحميلها
        if($request->hasFile('category_image'))
        {
            //تخزين الصورة
            $imagePath = $request->file('category_image')->store('categories', 'public');

        // إضافة الفئة إلى قاعدة البيانات
        $category=Category::create([
            'category_name' => $request->category_name,
            'image' => $imagePath

        ]);

            return $this->SuccessResponse($category,'Category added successfully',201);
        }

        return $this->SuccessResponse('Image upload failed', 400);
    }



    public function editCategoryName(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'category_id' => 'required',
            'new_category_name' => 'required|string|max:255'
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        $category = Category::where('id',$request->category_id)->first();

        if(!$category)
        {
            return $this->ErrorResponse('category not found',404);
        }

        $category->category_name=$request->new_category_name;
        $category->save();

        return $this->SuccessResponse($category,'category name updated successfully',200);
    }



    public function getAllContractorsInOneCategory(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'category_id'=>'required|exists:categories,id'
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        $allContractors=Contractor::where('category_id',$request->category_id)->get();

        if($allContractors->isEmpty())
        {
            return $this->ErrorResponse('No contractors found in this category',404);
        }

        return $this->SuccessResponse($allContractors,'All contractors in this category',200);
    }



    public function searchCategory(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'search' =>'required|string'
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        // البحث عن الفئات
        $searchResult = Category::where('category_name','like','%'.$request->search.'%')->get();

        if($searchResult->isEmpty())
        {
            return $this->ErrorResponse('No categories found matching your search',404);
        }

        return $this->SuccessResponse($searchResult,'categories found',200);
    }
}
