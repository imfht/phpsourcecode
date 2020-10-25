<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function view()
    {
        $title = '上传';
        return view('demo.upload', compact('title'));
    }

    public function upload_touch()
    {
        $allowExt = ["jpg", "png", 'jpeg', 'gif', 'bmp'];
        $dirPath  = public_path('uploads/' . date('Ym'));
        //如果上传的是图片
        $file = $this->request->file('file');
        //如果目标目录不能创建
        if (!is_dir($dirPath) && !mkdir($dirPath, 0777, true)) {
            return $this->setJson(11, '上传目录没有创建文件夹权限');
        }
        //如果目标目录没有写入权限
        if (is_dir($dirPath) && !is_writable($dirPath)) {
            return $this->setJson(12, '上传目录没有写入权限');
        }
        //校验文件
        if (isset($file) && $file->isValid()) {
            $ext = $file->getClientOriginalExtension(); //上传文件的后缀
            //判断是否是图片
            if (empty($ext) or in_array(strtolower($ext), $allowExt) === false) {
                return $this->setJson(13, '不允许的文件类型');
            }
            //生成文件名
            $fileName = uniqid() . '_' . dechex(microtime(true)) . '.' . $ext;
            try {
                $path    = $file->move('uploads/' . date('Ym'), $fileName);
                $webPath = '/' . $path->getPath() . '/' . $fileName;
                return $this->setJson(0, 'ok', url($webPath));
            } catch (\Exception $ex) {
                return $this->setJson($ex->getCode(), $ex->getMessage());
            }
        }
        return $this->setJson(400, '上传失败');
    }

    public function upload_base64()
    {
        $base64  = $this->request->input('base64');
        $maxSize = 5 * pow(1024, 2);//允许5m大小上传
        $dirPath = public_path('uploads/' . date('Ym') . '/');
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }
        $suffixAll = array('jpg', 'gif', 'png', 'jpeg');
        if (!preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $matches)) {
            return $this->setJson(100, '解析Base64错误');
        }
        //判断后缀
        $suffix = $matches[2];
        if (!in_array($suffix, $suffixAll)) {
            return $this->setJson(101, '不支持' . $suffix . '后缀上传,只允许' . join(',', $suffixAll) . '格式的上传');
        }
        $imgBase64Data = str_replace($matches[1], '', $base64);
        //判断大小
        $size = strlen($imgBase64Data);
        if ($size > $maxSize) {
            return $this->setJson(102, '上传的图片太大,允许' . $maxSize / pow(1024, 2) . 'M');
        }
        $imgData   = base64_decode($imgBase64Data);
        $img_name  = uniqid() . '.' . $suffix;
        $full_path = $dirPath . $img_name;
        $bool      = file_put_contents($full_path, $imgData);
        if ($bool) {
            $path = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', $dirPath));
//            $url  = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $path . $img_name;
            $url = asset($path . $img_name);
            return $this->setJson(0, '上传成功', $url);
        }
        return $this->setJson(400, '上传失败');
    }
}
