<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\FixitTrait;
use App\Models\Contract;

class HomeOwnerController extends Controller
{
    use FixitTrait;


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
            'receipt' => function ($query) {
                $query->select('id', 'contract_id', 'amount', 'status');
            }
        ])
        ->whereHas('task', function ($query) use ($user_id) {
            $query->where('user_id', $user_id); // التأكد من أن العقود مرتبطة بالمستخدم
        })
        ->get();

        return $this->SuccessResponse($contracts, 'Contracts retrieved successfully.', 200);
    }

}
