<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\FixitTrait;
use App\Models\Task;
use App\Models\Task_image;
use App\Models\Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    use FixitTrait;

    public function showTask(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'task_id' => 'required|exists:tasks,id'
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        $task = Task::with('task_image')->where('id',$request->task_id)->first();
        if(!$task)
        {
            return $this->ErrorResponse('Task not found',404);
        }
        return $this->SuccessResponse($task,'Task retrieved successfully',200);
    }



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
      $allTasks = Task::where('contractor_id', $request->contractor_id)->get();

    if ($allTasks->isEmpty())
    {
        return $this->ErrorResponse('No tasks found', 404);
    }

    return $this->SuccessResponse($allTasks, 'All tasks', 200);
    }



    public function addTask(Request $request)
    {

        // التحقق من صحة البيانات المدخلة
        $validation = Validator::make($request->all(), [
            'contractor_id' => 'required|exists:contractors,id',
            'title' => 'required|string|max:50',
            'description' => 'required|string|max:500',
            'city' => 'required|string',
            'country' => 'required|string',
            'address' => 'required|string',
            'task_images' => 'nullable|array',  // إضافة التحقق للصور
            //* هذه العلامة تستخدم لتطبيق قاعدة التحقق على كل عنصر داخل المصفوفة، أي لكل صورة في المصفوفة task_images
            'task_images.*' => 'image|mimes:jpeg,png,jpg|max:2048', // التحقق من كل صورة
        ]);
        // التحقق من وجود أخطاء في التحقق
        if ($validation->fails()) {
            return $this->ErrorResponse($validation->errors(), 422);
        }
        // الحصول على المستخدم الحالي
        $currentUser = Auth()->user();

        // إنشاء مهمة جديدة
        $newTask = Task::create([
            'user_id' => $currentUser->id,
            'contractor_id' => $request->contractor_id,
            'title' => $request->title,
            'description' => $request->description,
            'city' => $request->city,
            'country' => $request->country,
            'address' => $request->address,
        ]);

        // إذا كانت هناك صور في الطلب
        if ($request->hasFile('task_images')) {
            $taskImages = $request->file('task_images');
            foreach ($taskImages as $image) {
                // تخزين الصورة في مجلد 'task_images' داخل التخزين العام
                $imagePath = $image->store('task_images', 'public');

                // إنشاء سجل في جدول task_images
                Task_image::create([
                    'task_id' => $newTask->id,
                    'image_id' => Image::create(['name' => $imagePath])->id,  // حفظ الصورة في جدول Image أولًا
                ]);
            }
        }
        return $this->SuccessResponse($newTask, 'Task added successfully', 201);
    }


    public function addTaskImage(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $validation = Validator::make($request->all(),[
            'taskId' => 'required|exists:tasks,id',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // التحقق من وجود أخطاء في التحقق
        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        // حفظ الصورة في النظام
        //بتنحفظ الصورة بالمسار storage/app/public/images
        $imagePath = $request->file('image')->store('Task_images','public');

        // إضافة السجل في جدول images
        $saveImage = Image::create([
            'name' => $imagePath,
        ]);

        // إضافة الربط بين المهمة والصورة في جدول task_images
        $Image_added = Task_image::create([
            'task_id' => $request->taskId,
            'image_id' => $saveImage->id
        ]);

        return $this->SuccessResponse($Image_added,'Image added to task successfully.',201);
    }

    public function deleteTaskImage(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'image_id' => 'required|exists:images,id'
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }
        $image = Task_image::where('image_id',$request->image_id)->first();

        if(!$image)
        {
            return $this->ErrorResponse('Task image not found',404);
        }
        $image->delete();

        Image::where('id',$request->image_id)->delete();

        return $this->SuccessResponse(null,'Image deleted successfully',200);
    }

    public function showTaskImages(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'taskId' =>'required|exists:tasks,id'
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        $taskImages = Task_image::with('image')->where('task_id',$request->taskId)->get();

        if($taskImages->isEmpty())
        {
            return $this->ErrorResponse('There are no images for this task',404);
        }
        return $this->SuccessResponse($taskImages,'Images for this task',200);
    }

    public function acceptTask(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'task_id' => 'required|exists:tasks,id'
        ]);

        if($validation->fails())
        {
            return $this->ErrorResponse($validation->errors(),422);
        }

        $task_id = Task::where('id',$request->task_id)->first();

        $task_id->update(['task_status'=>'accept']);

        return $this->SuccessResponse($task_id,'Status of task updated successfully',200);
    }

}
