<?php
namespace app\Finance\model;
class Forgan extends \think\Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'finc_organ';
    // 设置数据表主键
    protected $pk = 'id';
    // 设置当前数据表的字段信息
    protected $field = [
        'finc_no',
        'center_id'
    ];
    //自定义初始化
    protected function initialize()
    {
        $this->center_id = uInfo('cid');
    }
    /*
    // 生成 select 框
    public function element()
    {
        //$data = $this->select();
        $data = $this->field('id,name')->select();
        return $data;
        $html = '';
        foreach($data as $k->$obj){
            //$v = $obj->data;
            $v = $k->data;
            $html .= '<option value="'.$v['id'].'">'.$v['name'].'</option>';
            //$html .= '<option value="'.$obj->id.'">'.$obj->name.'</option>';
        }
        
        return $html;
    }
    */
}