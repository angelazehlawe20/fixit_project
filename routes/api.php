<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContractorController;
use App\Http\Controllers\HomeOwnerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ContractController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

    //مسار لتغيير كلمة المرور القديمة
    Route::post('/changePassword',[AuthController::class,'changePassword'])->middleware('auth:sanctum');
    // مسار لتسجيل مستخدم جديد باستخدام دالة 'register' من AuthController
    Route::post('/registerUser',[AuthController::class,'register']);
    // مسار لتسجيل الدخول باستخدام دالة 'login' من AuthController
    Route::post('/login', [AuthController::class, 'login']);
    // مسار لطلب كلمة مرور جديدة عبر البريد الإلكتروني
    //Route::post('/forgetPassword',[AuthController::class,'forgetPassword']);

    // مسار لإعادة تعيين كلمة المرور باستخدام OTP
    //Route::post('/resetPassword',[AuthController::class,'resetPassword']);



//________________________________________________________________________


// مجموعة مسارات خاصة بال (homeowner) فقط، محمية بواسطة Middleware 'role:homeowner'
Route::middleware(['role:homeowner'])->group(function () {
    //مسار لاستعراض جميع الفئات
    Route::get('/getCategories',[CategoryController::class,'getAllCategories'])->middleware('auth:sanctum');
    //مسار لعرض جميع ال contractor ضمن الفئة المحدد الواحدة
    Route::post('/getAllContractorsInOneCategory',[CategoryController::class,'getAllContractorsInOneCategory'])->middleware('auth:sanctum');
    //مسار للبحث عن ال contractors القريبين من ال homeowner
    Route::post('/searchNearbyContractors',[ContractorController::class,'searchNearbyContractors'])->middleware('auth:sanctum');
    //مسار لاضافة تقييم وتعليق لصاحب العمل
    Route::post('/addRate',[RatingController::class,'addRate'])->middleware('auth:sanctum');
    //مسار لترتيب اصحاب العمل حسب تقييماتهم
    Route::post('/sortContractorsByRating',[RatingController::class,'sortContractorsByRating'])->middleware('auth:sanctum');
    //مسار لعرض تقييمات صاحب عمل محدد
    Route::post('/getAllRatingForContractor',[RatingController::class,'getAllRatingForContractor'])->middleware('auth:sanctum');
    //مسار للبحث عن فئة معينة
    Route::post('/searchCategory',[CategoryController::class,'searchCategory'])->middleware('auth:sanctum');
    //مسار لاضافة مهمة جديدة
    Route::post('/addTask',[TaskController::class,'addTask'])->middleware('auth:sanctum');
    //مسار لاضافة صورة ل task
    Route::post('/addTaskImage',[TaskController::class,'addTaskImage'])->middleware('auth:sanctum');
    //مسار لعرض الايصال
    Route::post('/getReceipt',[ReceiptController::class,'getReceipt'])->middleware('auth:sanctum');
    //مسار لحذف صورة من تاسك معين
    Route::post('/deleteTaskImage',[TaskController::class,'deleteTaskImage'])->middleware('auth:sanctum');
    //مسار لقبول عقد
    Route::post('/acceptContract',[ContractController::class,'acceptContract'])->middleware('auth:sanctum');
    //مسار لرفض عقد
    Route::post('/rejectContract',[ContractController::class,'rejectContract'])->middleware('auth:sanctum');
    //مسار لعرض صفحة ال contractor
    Route::post('/getContractorProfilePage',[ContractorController::class,'getContractorProfilePage'])->middleware('auth:sanctum');
    //مسار لعرض العقود المرسلة لصاحب البيت
    Route::post('/getAllContractsForHomeowner',[HomeOwnerController::class,'getAllContractsForHomeowner'])->middleware('auth:sanctum');
    //مسار لعرض الايصالات المرسلة لصاحب البيت
    Route::post('/getAllReceiptsForHomeowner',[HomeOwnerController::class,'getAllReceiptsForHomeowner'])->middleware('auth:sanctum');
    //مسار لعرض تفاصيل عقد
    Route::post('/viewContract',[ContractController::class,'viewContract'])->middleware('auth:sanctum');




});

// مجموعة مسارات خاصة بال (contractor) فقط، محمية بواسطة Middleware 'role:contractor'
Route::middleware(['role:contractor'])->group(function () {
    //مسار لاضافة معرض اعمال لصاحب عمل معين
    Route::post('/addPortfolio',[PortfolioController::class,'addPortfolio'])->middleware('auth:sanctum');
    //مسار لعرض المهام الخاصة ب contractor معين
    Route::post('/getTasksOfContractor',[ContractorController::class,'getTasksOfContractor'])->middleware('auth:sanctum');
    //مسار لاضافة ايصال
    Route::post('/addReceipt',[ReceiptController::class,'addReceipt'])->middleware('auth:sanctum');
    //مسار لاضافة عقد
    Route::post('/addContract',[ContractController::class,'addContract'])->middleware('auth:sanctum');
    //مسار لاضافة صورة الى معرض الاعمال لصاحب العمل
    Route::post('/addImageToPortfolio',[PortfolioController::class,'addImageToPortfolio'])->middleware('auth:sanctum');
    //مسار لحذف صورة من معرض الاعمال
    Route::post('/deletePortfolioImage',[PortfolioController::class,'deletePortfolioImage'])->middleware('auth:sanctum');
    //مسار لعرض تاسك معين
    Route::post('/showTask',[TaskController::class,'showTask'])->middleware('auth:sanctum');
    //مسار لقبول التاسك
    Route::post('/acceptTask',[TaskController::class,'acceptTask'])->middleware('auth:sanctum');

});

Route::middleware(['role:homeowner,contractor'])->group(function () {

    // مسار لتعديل البيانات الشخصية للمستخدم (سواء كان صاحب منزل او صاحب عمل)
    Route::post('/editProfileInfo',[AuthController::class,'editProfileInfo'])->middleware('auth:sanctum');
    //مسار لعرض الصور الخاصة ب task معين
    Route::post('/showTaskImages',[TaskController::class,'showTaskImages'])->middleware('auth:sanctum');
    //مسار لعرض بيانات مستخدم (صاحب بيت او صاحب عمل)
    Route::post('/getUser',[AuthController::class,'getUser'])->middleware('auth:sanctum');
    //مسار لعرض صور صاحب العمل ل تاسك معين
    Route::post('/getTaskPortfolioImages',[PortfolioController::class,'getTaskPortfolioImages'])->middleware('auth:sanctum');
    //مسار لعرض صورة من معرض الاعمال
    Route::post('/showPortfolioImage',[PortfolioController::class,'showPortfolioImage'])->middleware('auth:sanctum');

});


// مجموعة مسارات خاصة بال (admin) فقط، محمية بواسطة Middleware 'role:admin'
//Route::middleware(['role:admin'])->group(function () {
    //مسار لتغيير اسم الفئة
    Route::post('/editCategoryNameOrImage',[CategoryController::class,'editCategorynameOrImage'])->middleware('auth:sanctum');
    //مسار لاضافة خدمة لصاحب العمل
    Route::post('/addCategory',[CategoryController::class,'addCategory'])->middleware('auth:sanctum');
//});



