<?php
namespace app\common\model;

use think\Model;

class Addonvideo extends Model
{
    public function getContentAttr($value, $data)
    {
        return htmlspecialchars_decode($data['content']);
    }
}