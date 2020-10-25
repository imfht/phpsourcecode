<?php
/* 2017年2月19日 星期日 个人日志
 *
 */
namespace app\common\model;
use app\common\model\BaseModel;
class Gcenter extends BaseModel{
    protected $table = 'gen_center';
    protected $pk = 'gen_no';    
    public function getTitle($genno)
    {
        return $this->db()->where('gen_no',$genno)->value('gen_title');
    }
}