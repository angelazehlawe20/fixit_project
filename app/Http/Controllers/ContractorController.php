<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\FixitTrait;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class ContractorController extends Controller
{
    use FixitTrait;

    public function searchNearbyContractors(Request $request)
    {
        // تحقق من صحة البيانات المدخلة
        $validation=Validator::make($request->all(),[
            'category_id' => 'required|exists:categories,id',
            'search'=>'required|string'
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

        // جلب اصحاب العمل القريبين الذي يجري البحث عنهم ضمن category معينة
        $nearbyContractors = User::where('role', 'contractor')
                                ->whereHas('contractor', function ($query) use ($request) {
                                    $query->where('category_id', $request->category_id);
                                })
                                ->where('city', $currentUser->city)
                                ->where('country', $currentUser->country)
                                ->where('username', 'like', '%' . $request->search . '%')
                                ->get();

        if($nearbyContractors->isEmpty())
        {
            return $this->ErrorResponse('Sorry No Result Found',404);
        }
        return $this->SuccessResponse($nearbyContractors,'Contractors found in your area',200);
    }

}
