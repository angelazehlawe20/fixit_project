<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\FixitTrait;
use App\Models\Category;
use App\Models\Contractor;
use App\Models\CategoryImage;
use App\Models\Image;

class CategoryController extends Controller
{
    use FixitTrait;

    //عرض الخدمات
    public function getAllCategories()
    {
        $categories = Category::all();
        return $this->SuccessResponse($categories,'All categories',200);
    }


    // اضافة خدمة
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


    // تعديل اسم او صورة خدمة
    public function editCategoryNameOrImage(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'category_id' => 'required',
            'new_category_name' => 'nullable|string|max:255',
            'new_category_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // التحقق من الصورة
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        $category = Category::find($request->category_id);

        if(!$category)
        {
            return $this->ErrorResponse('category not found',404);
        }

        // تحديث اسم الخدمة إذا تم توفير اسم جديد
        if ($request->has('new_category_name')) {
            $category->category_name = $request->new_category_name;
            $category->save();
        }

        // تحديث صورة الخدمة إذا تم توفير صورة جديدة
        if ($request->hasFile('new_category_image')) {
        // حذف الصورة القديمة إذا كانت موجودة
        $oldImage = CategoryImage::where('category_id', $category->id)->first();
        if ($oldImage) {
            Storage::delete($oldImage->image_url); // حذف الصورة من التخزين
            $oldImage->delete(); // حذف السجل من قاعدة البيانات
        }

        // حفظ الصورة الجديدة
        $imagePath = $request->file('new_category_image')->store('category_images', 'public');

        // إنشاء سجل جديد للصورة
        $image = Image::create(['image_url' => $imagePath]);
        CategoryImage::create([
            'category_id' => $category->id,
            'image_id' => $image->id,
        ]);
    }
    return $this->SuccessResponse($category, 'Category name and image updated successfully', 200);
    
    }


    // عرض كل العقود الخاصة في الخدمة المحددة
    public function getAllContractorsInOneCategory(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'category_id'=>'required|exists:categories,id'
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        $allContractors=Contractor::select('id','user_id','category_id','description')->with([
            'user'=>function($query)
            {
                $query->select('id','username','phone','email','city','country','address');
            },
            'rating' =>function($query)
            {
                $query->select('contractor_id','rate');
            }
            ])
            ->where('category_id',$request->category_id)->get();

        if($allContractors->isEmpty())
        {
            return $this->ErrorResponse('No contractors found in this category',404);
        }

        return $this->SuccessResponse($allContractors,'All contractors in this category',200);
    }


    // البحث عن خدمة
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
