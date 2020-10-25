<?php
namespace app\common\model;

use think\Model;

class Addonarticle extends Model
{
	//新增和更新自动完成列表
//  protected $auto = ['content'];
//
//
//  public function setContentAttr($value, $data)
//  {
//      return htmlspecialchars($data['content']);
//  }



    public function getContentAttr($value, $data)
    {
        return htmlspecialchars_decode($data['content']);
    }


}