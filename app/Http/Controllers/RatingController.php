<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\Contractor;
use Illuminate\Support\Facades\Validator;
use App\Traits\FixitTrait;
use Carbon\Carbon;


class RatingController extends Controller
{
    use FixitTrait;

    public function addRate(Request $request)
    {
        // تحقق من صحة البيانات المدخلة
        $validation = Validator::make($request->all(),[
            'contractor_id' => 'required|exists:contractors,id',
            'comment' => 'string|max:1000',
            'rate' => 'required|integer|min:1|max:5',
        ]);
        
        // التحقق من وجود أخطاء في التحقق
        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        $currentUser = Auth()->user();

        // تحقق من ان المستخدم مسجل دخول
        if(!$currentUser)
        {
            return $this->ErrorResponse('User not authenticated',401);
        }

        // انشاء rate , comment جديد
        $newRate = Rating::create([
            'user_id' => $currentUser->id,
            'contractor_id' => $request->contractor_id,
            'comment' => $request->comment,
            'rate' => $request->rate,
            'rated_at' => Carbon::now(),
        ]);

        return $this->SuccessResponse($newRate,'Rate added successfully',201);
    }



    public function sortContractorsByRating(Request $request)
    {
        // تحقق من صحة البيانات المدخلة
        $validation=Validator::make($request->all(),[
            'category_id' => 'required|exists:categories,id',
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        $sortContractors = Contractor::where('category_id',$request->category_id)
                                    ->with('rating')    //اسم العلاقة في ال model
                                    ->withAvg('rating','rate')    //rating اسم العلاقة , rate اسم الحقل الذي ترغب في حساب متوسطه داخل العلاقة rating
                                    ->orderByDesc('rating_avg_rate')  //ترتيب التقييمات تنازي(من اعلى تقييم الى اقل تقييم)
                                    ->get();

        return $this->SuccessResponse($sortContractors,'Contractors ordered by rating',200);
    }


    public function getAllRatingForContractor(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'contractor_id'=>'required|exists:contractors,id'
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        $allRatingForContractor = Rating::where('contractor_id',$request->contractor_id)->with('user')->get();  //('user') تدل على اسم العلاقة في ال model

        if($allRatingForContractor->isEmpty())
        {
            return $this->ErrorResponse('No reviews found for this contractor',404);
        }
        return $this->SuccessResponse($allRatingForContractor,'all reviews for this contractor',200);
    }

}
