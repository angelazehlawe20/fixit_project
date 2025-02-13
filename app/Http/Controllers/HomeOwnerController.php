<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\FixitTrait;
use App\Models\Contract;
use App\Models\Receipt;

class HomeOwnerController extends Controller
{
    use FixitTrait;


    // العقود الخاصة بالمستخدم
    public function getAllContractsForHomeowner(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'user_id' => 'required|exists:users,id'
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        $user_id = $request->user_id;

        $contracts = Contract::with([
            'task' => function ($query) use ($user_id) {
                $query->select('id', 'user_id', 'contractor_id', 'address', 'title', 'task_status')
                      ->where('user_id', $user_id); // تصفية المهام الخاصة بصاحب العمل
            },
        ])
        ->whereHas('task', function ($query) use ($user_id) {
            $query->where('user_id', $user_id); // التأكد من أن العقود مرتبطة بالمستخدم
        })
        ->get();

        return $this->SuccessResponse($contracts, 'Contracts retrieved successfully', 200);
    }


    //الايصالات المتعلقة بالمستخدم
    public function getAllReceiptsForHomeowner(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'user_id' => 'required|exists:users,id'
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        $user_id = $request->user_id;

        $receipts = Contract::with([
            'task' => function ($query) use ($user_id) {
                $query->select('id', 'user_id', 'contractor_id', 'address', 'title', 'task_status')
                      ->where('user_id', $user_id); // المهام الخاصة بصاحب العمل
            },
            'receipt'=>function($query) use ($user_id)
            {
                $query->select('id','contract_id');
            },
            'task.task_image.image'=>function($query)
            {
                $query->select('id','name');
            },
            'task.contractor.portfolio.portfolio_image.image'=>function($query)
            {
                $query->select('id','name');
            }
        ])
        ->whereHas('task', function ($query) use ($user_id) {
            $query->where('user_id', $user_id); // التأكد من أن الايصالات مرتبطة بالمستخدم
        })
        ->get();
        return $this->SuccessResponse($receipts,'Receipts retrieved successfully',200);
    }

}
