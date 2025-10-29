<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfilePhotoController extends Controller
{
    public function me()
    {
        $user = Auth::user();
        if (!$user->profile_photo) abort(404);

        // ถ้า DB เก็บแค่ชื่อไฟล์ เช่น "1757585765_1_ProfileMe.png"
        //$path = 'private/profile_photos/'.$user->profile_photo;

        // ถ้า DB เก็บ path เต็ม ก็ใช้ตามนั้น:
         $path = $user->profile_photo;

        abort_unless(Storage::disk('local')->exists($path), 404);
        return Storage::disk('local')->response($path); // ส่งไฟล์ออกไป
    }
}
