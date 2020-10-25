<?php
namespace app\common\model;

use think\Model;

class Banner extends Model
{
    public function moduleClass()
    {
        return $this->hasOne('ModuleClass', 'id', 'mid')->field('id, title');
    }

    public function getStatusTurnAttr($value, $data)
    {
        $turnArr = [0=>'停用', 1=>'在用'];
        return $turnArr[$data['status']];
    }

    /**
     * @Title: banners
     * @Description: todo(banner模块数据)
     * @param int $mid
     * @param string $limit
     * @author 戏中有你
     * @date 2018年1月17日
     * @throws
     */
    public function banners($mid, $limit){
        $where = [
            'mid' => $mid,
            'status' => 1,
        ];
        $result = $this->where($where)->order('sorts ASC,id ASC')->limit($limit)->select();
        return $result;
    }
}