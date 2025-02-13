<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\FixitTrait;
use App\Models\Portfolio;
use App\Models\Contractor;
use App\Models\Portfolio_image;
use App\Models\Image;

use Illuminate\Support\Facades\Validator;


class PortfolioController extends Controller
{
    use FixitTrait;


    // اضافة معرض اعمال
    public function addPortfolio(Request $request)
    {
        // تحقق من صحة البيانات المدخلة
        $validation = Validator::make($request->all(),[
            'contractor_id'=>'required|exists:contractors,id',
            'title' => 'required|string|max:20'
        ]);

        // التحقق من وجود أخطاء في التحقق
        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        // التحقق من صلاحيات المستخدم الحالي
        $currentUser = Auth()->user();

        //لاتحقق من ان ال contractor يلي بدي ضفلو ال portfolio هو نفسو المستخدم يلي مسجل دخول
        $contractor = Contractor::find($request->contractor_id);
        if (!$contractor || $contractor->user_id !== $currentUser->id) {
            return $this->ErrorResponse('Unauthorized to add portfolio for this contractor', 403);
        }

        // إنشاء Portfolio جديد
        $newPortfolio= Portfolio::create([
            'contractor_id'=> $request->contractor_id,
            'title' => $request->title
        ]);
        return $this->SuccessResponse($newPortfolio,'Portfolio added successfully',201);
    }

    // اضافة صورة الى معرض الاعمال
    public function addImageToPortfolio(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'portfolio_id' => 'required|exists:portfolios,id',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }
        $imagePath = $request->file('image')->store('potfolio_image','public');

        $image = Image::create([
            'name'=> $imagePath
        ]);

        $imagePortfolio = Portfolio_image::create([
            'image_id' => $image->id,
            'portfolio_id' => $request -> portfolio_id
        ]);
        return $this->SuccessResponse($imagePortfolio,'Image added to portfolio successfully',201);
    }


    // عرض الصور الخاصة بالمهمة
    public function getTaskPortfolioImages(Request $request)
    {
        // تحقق من وجود الـ task_id
        $validation = Validator::make($request->all(), [
            'task_id' => 'required|exists:tasks,id'
        ]);

        if ($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(), 422);
        }

        $portfolio = Portfolio::whereHas('contractor.task',function($query) use ($request)
        {
            $query->where('id',$request->task_id);
        })
        ->with(['portfolio_image.image' => function ($imageQuery) {
            $imageQuery->select('id', 'name');
        }])
        ->get();

            if ($portfolio->isEmpty())
            {
                return $this->ErrorResponse('No portfolios found for the specified task',404);
            }
        return $this->SuccessResponse($portfolio,'Portfolios with images retrieved successfully', 200);
    }


    // حذف صورة الى ال معرض الاعمال
    public function deletePortfolioImage(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'image_id' => 'required|exists:images,id'
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        // البحث عن الصورة في جدول portfolio_images باستخدام image_id
        $image = Portfolio_image::where('image_id',$request->image_id)->first();

        // التحقق إذا لم يتم العثور على الصورة في جدول portfolio_images
        if(!$image)
        {
            return $this->ErrorResponse('Image not found',404);
        }

        // حذف الصورة من جدول portfolio_images
        $image->delete();
        // حذف الصورة من جدول images
        Image::where('id',$request->image_id)->delete();

        return $this->SuccessResponse(null,'Image deleted successfully',200);
    }

    //عرض صورة من معرض الاعمال
    public function showPortfolioImage(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'image_id' => 'required|exists:images,id'
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        $image = Portfolio_image::with('image')->where('image_id',$request->image_id)->first();

        if(!$image)
        {
            return $this->ErrorResponse('Image not found',404);
        }

        return $this->SuccessResponse($image,'Image retrieved successfully',200);
    }

}
