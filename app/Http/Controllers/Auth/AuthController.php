<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\FixitTrait;
use App\Models\User;
use App\Models\Contractor;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\VerificationError;


class AuthController extends Controller
{
    use FixitTrait;

    public function register(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'role' =>'required|in:admin,homeowner,contractor',
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'country' => 'required|string',
            'city' => 'required|string',
            // تحقق من أن الـ contractor قدم خدمة
            'category' => 'required_if:role,contractor|exists:categories,id',
            'description' => 'required_if:role,contractor|string|max:1000'
        ]);

        // رسالة الخطأ الذي حدث في البيانات المدخلة(غير مطابق للشروط التي حددت)
        if ($validator->fails()) {
            return $this->ErrorResponse($validator->errors(),422);
        }

        // إضافة المستخدم الذي نقوم بانشائه في ال database
        $user = User::create([
            'role' => $request->role,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'country' => $request->country,
            'city' => $request->city,
        ]);

        // // إذا كان المستخدم من نوع contractor، نربط جدول contractor بالخدمة والوصف المناسبين
        if($request->role === 'contractor')
        {
            $contractor =Contractor::create([
                'user_id' => $user->id,
                'category_id' => $request->category,
                'description' => $request->description
            ]);
        }
        // إرجاع بيانات المستخدم مع بيانات contractor
        $user->load('contractor');  // تحميل بيانات الـ contractor المرتبطة بالمستخدم

        return $this->SuccessResponse($user,'User registered successfully',201);
    }



    public function login(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);
        // رسالة الخطأ الذي حدث في البيانات المدخلة(غير مطابق للشروط التي حددت)
        if ($validator->fails()) {
            return $this->ErrorResponse($validator->errors(),422);
        }
        // مطابقة البريد الالكتروني المدخل من قبل المستخدم مع البريد الموجود في ال db
        $user= User::where('email',$request->email)->first();

        // في حال عدم وجود البريد او عدم تطابق كلمة السر المدخلة مع المحفوظة في الداتابيز يعيد رسالة خطأ
        if(!$user || !Hash::check($request->password, $user->password))
        {
            return $this->ErrorResponse('Invalid email or password',401);
        }

        // إنشاء توكن خاصة بالمستخدم
        $token=$user->createToken('auth_token')->plainTextToken;

        return $this->SuccessResponse($token,'Logged in successfully',200);
    }



    /*public function forgetPassword(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'email'=>'required|email|exists:users,email'
        ]);

        if($validator->fails())
        {
            return $this->ErrorResponse($validator->errors(), 422);
        }

    }


    public function resetPassword(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'email'=>'required|email|exists:users,email',
            'password'=>'required|string|min:8|confirmed'
        ]);

        if($validator->fails())
        {
            return $this->ErrorResponse($validator->errors(), 422);
        }

        $user= $this->verifyOTPCode($request,true);

        if ($user instanceof VerificationError) {
            // في حال فشل التحقق، استرجع رسالة خطأ باستخدام ErrorResponse من الـ trait
            return $this->ErrorResponse('Verification failed', 401);
        }

        $user->password=Hash::make($request->password);
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();
        return $this->SuccessResponse(null, 'Password has been reset successfully.', 200);
    }
    */


    public function changePassword(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $validator=Validator::make($request->all(),[
            'new_password'=>'required|string|min:8|confirmed'
        ]);

        // في حال فشل التحقق من البيانات المدخلة
        if($validator->fails())
        {
            return $this->ErrorResponse($validator->errors(),422);
        }
        // العثور على المستخدم الحالي
        $user=Auth::user();    // الحصول على المستخدم الذي قام بتسجيل الدخول

        if(!$user){
            return $this->ErrorResponse('you must login',401);
        }
        // تحديث كلمة المرور الجديدة
        $user->password=Hash::make($request->new_password);
        $user->save();

        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        $new_token = $user->createToken('auth_token')->plainTextToken;

        return $this->SuccessResponse($new_token,'Password changed successfully.',200);
    }


    public function getUser(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'user_id' => 'required|exists:users,id',
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        $user = User::with('contractor')->find($request->user_id);

        return $this->SuccessResponse($user,'User found successfully',200);
    }


    public function editProfileInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' =>'in:admin,homeowner,contractor',
            'username' => 'string|max:255',
            'email' => 'email|unique:users,email,'. Auth::id(), //لكي يأخذ في الحسبان البريد الإلكتروني الحالي عند التحقق من الفريدة
            'password' => 'string|min:8|confirmed',
            'phone' => 'string|max:15',
            'address' => 'string',
            'country' => 'string',
            'city' => 'string',
            'category' => 'exists:categories,id',
            'description' => 'string|max:1000'
        ]);

        // رسالة الخطأ الذي حدث في البيانات المدخلة(غير مطابق للشروط التي حددت)
        if ($validator->fails()) {
            return $this->ErrorResponse($validator->errors(),422);
        }

        $user=Auth()->user();

        // إضافة المستخدم الذي نقوم بانشائه في ال database
        $user ->update([
            'role' => $request->role ?: $user->role, //اذا طلب تغييره تتحدث بياناته والا تبقى كما هي
            'username' => $request->username ?: $user->username,
            'email' => $request->email ?: $user->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'phone' => $request->phone ?: $user->phone,
            'address' => $request->address ?: $user->address,
            'country' => $request->country ?: $user->country,
            'city' => $request->city ?: $user->city,
        ]);

        // // إذا كان المستخدم من نوع contractor، نربط جدول contractor بالخدمة والوصف المناسبين
        if($request->role === 'contractor')
        {
            // تحقق مما إذا كانت category قد تم توفيرها في الطلب
            if($request->has('category'))
            {
                $contractor = Contractor::where('user_id',$user->id)->first();
                if($contractor)
                {
                    // إذا لم يكن هناك سجل contractor لهذا المستخدم
                    $contractor->update([
                        'category_id'=>$request->category ?: $contractor->category_id,
                        'description' => $request->description ?: $contractor->description,
                    ]);
                }
                else
                {
                    // إذا لم يكن هناك سجل contractor لهذا المستخدم
                    Contractor::create([
                        'user_id' => $user->id,
                        'category_id' => $request->category,
                        'description' => $request->description,
                    ]);
                }
            }
        }
        return $this->SuccessResponse($user, 'Profile updated successfully', 200);
    }



}

