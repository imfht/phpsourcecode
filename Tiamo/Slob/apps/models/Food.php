<?php
/**
 * Created by PhpStorm.
 * User: xiangdong
 * Date: 17/5/27
 * Time: 下午2:17
 */

namespace App\Model;


class Food
{
    public function attributeLabels()
    {
        return [
            'imgs' => '图片',
            'fee' => '费用',
            'traffic' => '交通',
            'video' => '视频',
            'des' => '介绍',
            'score' => '分数',
            'open_time' => '经营时间',
            'phone' => '电话',
            'tip' => '旅行贴士',
        ];
    }

    public function getData($old = null)
    {
        $data = [];
        $att = $this->attributeLabels();
        foreach ($att as $key => $value) {
            if (getRequest($key)) {
                $data[$key] = getRequest($key);
            }
        }
        //上传图片
        if (!empty($_FILES)) {
            $imgs = [];
            $uploadPath = WEBPATH . "/local/";
            $num = count($_FILES['imgs']['name']);
            for ($i = 0; $i < $num; $i++) {
                $path = $_FILES['imgs']['tmp_name'][$i];
                if (!$path) {
                    continue;
                }
                $file_name = md5($path . time()) . $_FILES['imgs']['name'][$i];
                if (!file_exists($uploadPath . $file_name)) {
                    move_uploaded_file($path, $uploadPath . $file_name);
                    $imgs[] = $file_name;
                }
            }
            if (!empty($imgs)) {
                $data['imgs'] = $imgs;
            } else {
                $data['imgs'] = $old['imgs'];
            }
        }
        return $data;
    }

    public function analyzeData($data)
    {
        $list = json_decode($data, 1);
        $result = [];
        $att = $this->attributeLabels();
        foreach ($list as $key => $value) {
            if (isset($att[$key])) {
                if ($key == 'imgs') {
                    foreach ($value as &$img) {
                        if (strpos($img, WEBROOT) === false) {
                            $img = WEBROOT . "/local/" . $img;
                        }
                    }
                    $result[$key] = $value;
                } else {
                    $result[$key] = $value;
                }
            }
        }
        return $result;
    }
}