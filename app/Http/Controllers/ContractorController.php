<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\FixitTrait;
use App\Models\User;
use App\Models\Contractor;
use App\Models\Task;
use Illuminate\Support\Facades\Validator;

class ContractorController extends Controller
{
    use FixitTrait;


    // عرض صفحة العامل مع التفاصيل والتقييمات
    public function getContractorProfilePage(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'contractor_id' => 'required|exists:contractors,id'
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        $contractor_id = Contractor::select('user_id','id','category_id','description')
        ->with([
            'portfolio.Portfolio_image.image'=>function($portfolio_image)
        {
            $portfolio_image->select('id', 'name');
        },'rating'=>function($ratinContractor)
        {
            $ratinContractor->select('contractor_id','user_id','comment','rate');
        },
        'user'=> function($userDetails){
            $userDetails->select('id','username','phone','email');
        }
        ])
        ->where('id',$request->contractor_id)->first();

        if (!$contractor_id)
        {
            return $this->ErrorResponse('Contractor not found', 404);
        }

        return $this->SuccessResponse($contractor_id,'Contractor profile retrieved successfully',200);
    }


    // البحث عن العامل الاقرب
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


    // عرض المهام الخاصة بالعامل
    public function getTasksOfContractor(Request $request)
    {

        // تحقق من صحة البيانات المدخلة
        $validation = Validator::make($request->all(), [
        'contractor_id' => 'required|exists:contractors,id'
        ]);

       // التحقق من وجود أخطاء في التحقق
       if ($validation->fails()) {
        return $this->ErrorResponse($validation->errors(), 422);
    }

      // جلب المهام للمقاول المحدد في الطلب
      $allTasks = Task::with(['user'=>function($query){
        $query->select('id','username','email','city','country','address','phone');
    }])
    ->where('contractor_id', $request->contractor_id)->get();

    if ($allTasks->isEmpty())
    {
        return $this->ErrorResponse('No tasks found', 404);
    }

    return $this->SuccessResponse($allTasks, 'All tasks', 200);
    }

}
