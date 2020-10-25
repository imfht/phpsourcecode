<?php
/* 2017年2月19日 星期日 个人日志
 *
 */
namespace app\common\model;
use app\common\model\BaseModel;
class Gnode extends BaseModel{
    protected $table = 'gen_node';
    protected $pk = 'pers_id';    
    public function selectNodeDiv()
    {
        $ret = [];
        return $ret;
    }
}