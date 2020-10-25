<?php

namespace App\Http\Controllers\Set;

use App\Http\Controllers\Controller;

use App\User;
use Illuminate\Support\Facades\DB;

class UploadController extends Controller
{
    /**
     * @return array
     */
      public function upload()
      {
          $file = \Request::file('photo');
          if($file == null){
              return redirect('/setting')
                  ->withErrors('请选择图片！');
          }
          $name = \Request::input('name');
          $allowed_extensions = ["png", "jpg", "gif"];
          if ($file->getClientOriginalExtension() && !in_array($file->getClientOriginalExtension(), $allowed_extensions)) {
              return redirect('/setting')->withErrors('只能上传图片，格式为png,jpg,gif');
          }
          $destinationPath = 'storage/uploads/'; //public 文件夹下面建 storage/uploads 文件夹
          $extension = $file->getClientOriginalExtension();
          $fileName = str_random(10).'.'.$extension;
          $file->move($destinationPath, $fileName);
          $filePath = asset($destinationPath.$fileName);
          $info=User::where('id',\Auth::id())->update(['thumb'=>$filePath]);
          return redirect('/biji')
              ->withSuccess('修改头像成功.');
      }
}
