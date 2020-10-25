<?php
/* 2017年1月24日 星期二
 * 基础-模式，用于 model 类的扩展
*/
namespace app\common\model;
use think\Model;
class BaseModel extends Model{
    // 获取表名
    public function getTable()
    {
        return $this->db()->getTable();
    }
    // 行转列
    // $wh 条件  string/array string 为id查询否则为map查询
    public function concat_ws($wh,$field,$limiter=null)
    {
        if(is_string($wh)){
            if(substr_count($wh,'=') == 0) $wh = $this->pk.'="'.$wh.'"';
        }
        $key = 'tp_cws';
        $field = 'group_concat('.$field.') as '.$key;
        $data = $this->db()->where($wh)->field($field)->find();
        if(isset($data[$key])){            
            $value = $data[$key];
            if($limiter) $value = str_replace(',',$limiter,$value);
            return $value;
        }
        return "";
    }
}