<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\FixitTrait;
use App\Models\Receipt;

class ReceiptController extends Controller
{
    use FixitTrait;

    // انشاء ايصال
    public function addReceipt(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'contract_id' => 'required|unique:receipts,contract_id|exists:contracts,id',
            'amount' => 'required|numeric',
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        $receipt = Receipt::create([
            'contract_id' => $request->contract_id,
            'amount' => $request->amount,
        ]);

        return $this->SuccessResponse($receipt,'receipt added successfully',201);
    }


    // عرض تفاصيل الايصال
    public function getReceipt(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'receipt_id' => 'required|exists:receipts,id'
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        // جلب بيانات Receipt مع بيانات Contract و Task المرتبطة بها
        $receipt = Receipt::select('id','contract_id','amount','status')
    ->with(['contract' => function($query) {
        $query->select('id','task_id','price','payment_date','end_date')
            ->with(['task' => function($taskQuery) {
                $taskQuery->select('id','title','description','address','country','city')
                    ->with(['task_image.image' => function($imageQuery) {
                        $imageQuery->select('id', 'name'); // صور مرتبطة بـ Task
                    }]);
            }]);
    }])
    ->where('id', $request->receipt_id)
    ->first();

        if(!$receipt)
        {
            return $this->ErrorResponse('Receipt not found',404);
        }

        return $this->SuccessResponse($receipt,'Receipt retrieved successfully',200);
    }
}


