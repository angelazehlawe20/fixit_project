<?php

namespace App\Traits;

trait FixitTrait
{
    public function SuccessResponse($data=null,$message=null,$code=null)
    {
        // إرجاع استجابة JSON تحتوي على البيانات والرسالة وكود الاستجابة
        return response()->json([
            'data'=>$data,
            'message'=>$message,
            'code'=>$code
        ],$code);
    }

    public function ErrorResponse($message=null,$code=null)
    {
        // إرجاع استجابة JSON تحتوي على رسالة الخطأ وكود الاستجابة
        return response()->json([
            'message'=>$message,
            'code'=>$code
        ],$code);
    }
}

