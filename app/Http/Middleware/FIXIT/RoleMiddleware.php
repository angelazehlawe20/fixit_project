<?php

namespace App\Http\Middleware\FIXIT;

use Closure;
use Illuminate\Http\Request;
use App\Traits\FixitTrait;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    use FixitTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */


    public function handle(Request $request, Closure $next,...$roles): Response
    {
        // التحقق مما إذا كان المستخدم مسجل دخول أم لا
        if (!Auth::check()) {

        // إذا لم يكن مسجل دخول، يتم إرجاع استجابة خطأ
            return $this->ErrorResponse('Unauthorized.',401);
        }

        // الحصول على role المستخدم الحالي من خلال Auth
        $userRole = Auth::user()->role;

        // التحقق مما إذا كان role المستخدم موجوداً في roles المسموح بها
        if (!in_array($userRole, $roles)) {
            return $this->ErrorResponse('Access denied.',403);
        }

        // السماح بالوصول إلى الطلب القادم إذا تم التحقق من ال role
        return $next($request);
    }
}
