<?php

namespace App\Http\Controllers;

use App\Plugin\PHPExcelHelp;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function view()
    {
        $title = '导入导出操作';
        return view('demo.report', compact('title'));
    }

    public function import()
    {
        $maxSize  = 10;//单位:m
        $extAllow = ['xls', 'xlsx'];
        $dir      = public_path('uploads/');
        $file     = $this->request->file('file');
        if (!$file->isValid()) {
            return $this->setJson(10, '文件校正失败');
        }
        $ext = $file->getClientOriginalExtension(); //上传文件的后缀
        if (!in_array($ext, $extAllow)) {
            return $this->setJson(10, '只支持 . ' . join(',', $extAllow) . '格式');
        }
        //判断大小
        $fileSize = round($file->getSize() / pow(1024, 2), 2);
        if ($fileSize > $maxSize) {
            $format = '原文件:%sM, 限制大小:%sM';
            return $this->setJson(12, sprintf($format, $fileSize, $maxSize));
        }
        //如果目标目录不能创建
        if (!is_dir($dir) && !mkdir($dir)) {
            return $this->setJson(14, '上传目录没有创建文件夹权限');
        }
        //如果目标目录没有写入权限
        if (is_dir($dir) && !is_writable($dir)) {
            return $this->setJson(15, '上传目录没有写入权限');
        }
        //生成文件名
        $fileName = date('Ymd_H_i_s') . '_' . uniqid() . '.' . $ext;
        try {
            $path      = $file->move($dir, $fileName);
            $list_data = PHPExcelHelp::import($path->getRealPath());
            if (count($list_data) > 0) {
//                @unlink($path->getRealPath());
                return $this->setJson(0, '导入完成', $list_data);
            }
            return $this->setJson(4, '导入失败');
        } catch (\Exception $ex) {
            return $this->setJson(400, $ex->getTraceAsString());
        }
    }

    public function export()
    {
        $data = $this->data();
        $list = [];
        foreach ($data as $item) {
            $list[] = array_values($item);
        }
        PHPExcelHelp::export([
            'ID',
            '名称',
            '性别',
            '所属公司',
            '学校',
            '时间'], $list);
    }

    public function data()
    {
        return [
            [
                'id'         => 1,
                'name'       => '小明',
                'sex'        => 1,
                'company'    => '谷歌公司',
                'school'     => '耶鲁',
                'updated_at' => time(),
            ],
            [
                'id'         => 2,
                'name'       => '小芳',
                'sex'        => 0,
                'company'    => '丰田公司',
                'school'     => '早稻田大学',
                'updated_at' => time(),
            ],
            [
                'id'         => 3,
                'name'       => '小李',
                'sex'        => 1,
                'company'    => '索尼公司',
                'school'     => '东京大学',
                'updated_at' => time(),
            ],
            [
                'id'         => 4,
                'name'       => '小王',
                'sex'        => 1,
                'company'    => '松下公司',
                'school'     => '关西学院大学',
                'updated_at' => time(),
            ],
            [
                'id'         => 5,
                'name'       => '小余',
                'sex'        => 0,
                'company'    => '第一生命公司',
                'school'     => '神户国际大学',
                'updated_at' => time(),
            ],
        ];
    }
}
