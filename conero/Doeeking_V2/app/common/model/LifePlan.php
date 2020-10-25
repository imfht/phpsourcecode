<?php
/* 2017年2月19日 星期日 个人日志
 *
 */
namespace app\common\model;
use app\common\model\BaseModel;
class LifePlan extends BaseModel{
    protected $table = 'life_plan';
    protected $pk = 'listno';    
    // 模型关联
    public function el()
    {
        return $this->hasMany('LifePlanEl','p_listno');
    }
}