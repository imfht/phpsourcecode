<?php

namespace app\kbcms\validate;
use think\Validate;
/**
 *  模型属性验证
 */
class FieldsetForm extends Validate{
    // 验证规则
    protected $rule = [
        ['name', 'require|max:25', '模型名称不能为空|模型名称不能超过25个字符'],
        ['table', 'unique:fieldset|require|max:25', '已存在相同的数据表|表名不能为空|表名不能超过25个字符'],
        ['id','fieldForm','无法删除该表单，请先删除该表单字段！']
    ];

    protected $scene = array(
        'add'     => 'name,table',//新增数据时验证
        'edit'    => 'name',//修改数据时验证
        'del'     =>'id'//删除数据验证
    );

    protected function fieldForm(){
        $where['fieldset_id']=['eq',input('id')];
        $check_info=model('Field')->getWhereInfo($where);
        if (!empty($check_info)){
            return false;
        }else{
            return true;
        }
    }
}