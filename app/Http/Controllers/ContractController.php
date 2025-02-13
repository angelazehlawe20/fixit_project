<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\FixitTrait;
use App\Models\Contract;
use Carbon\Carbon;


class ContractController extends Controller
{
    use FixitTrait;

    // انشاء عقد
    public function addContract(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'task_id' => 'required|unique:contracts,task_id|exists:tasks,id',
            'payment_date' => 'required|date',
            'price' => 'required|numeric',
            'end_date' => 'required|date',
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        $contract = Contract::create([
            'task_id' => $request->task_id,
            'payment_date' => Carbon::createFromFormat('d-m-Y',$request->payment_date),
            'price' => $request->price,
            //يتم تحويل التواريخ إلى التنسيق المناسب (Y-m-d) قبل تخزينها في قاعدة البيانات
            'end_date' => Carbon::createFromFormat('d-m-Y',$request->end_date)
        ]);

        return $this->SuccessResponse($contract,'contract added successfully',201);
    }


    // قبول العقد
    public function acceptContract(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'contract_id' => 'required|exists:contracts,id'
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        $contract_id = Contract::where('id',$request->contract_id)->first();

        $contract_id->update(['contract_status'=>'accept']);

        return $this->SuccessResponse($contract_id,'Status of contract updated successfully',200);
    }


    // رفض العقد
    public function rejectContract(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'contract_id' => 'required|exists:contracts,id'
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        $contract_id = Contract::where('id',$request->contract_id)->first();

        $contract_id->update(['contract_status'=>'reject']);

        return $this->SuccessResponse($contract_id,'Status of contract updated successfully',200);
    }


    // رؤية العقد وتفاصيله
    public function viewContract(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'contract_id' => 'required|exists:contracts,id'
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        $contract = Contract::select('task_id','payment_date','price','end_date',)->with(['task'=>function($query)
        {
            $query->select('id','title','description','city','country','address');
        },
        'task.task_image.image' =>function($query)
        {
            $query->select('id','name');
        },
        ])
        ->where('id',$request->contract_id)->first();

        if(!$contract)
        {
            return $this->ErrorResponse('contract not found',404);
        }
        return $this->SuccessResponse($contract,'contract retrieved successfully',200);
    }

}
