<?php

namespace app\kbcms\validate;
use think\Validate;
/**
 *  模型属性验证
 */
class FieldExpand extends Validate{
    // 验证规则
    protected $rule = [
        ['fieldset_id', 'require', '无法获取字段集ID'],
        ['name', 'require', '字段名称不能为空'],
        ['field', 'require|validateField|alphaDash', '字段名不能为空|已存在相同的字段|字段名只能为英文数字和下划线！'],
        ['type', 'require', '字段类型未选择'],
    ];

    protected $scene = array(
        'add'     => 'fieldset_id,name,field,type',//新增数据时验证
        'edit'     => 'fieldset_id,name,field,type',//修改数据时验证
    );
    /**
     * 验证字段是否重复
     * @param int $field 字段名
     * @return bool 状态
     */
    protected function validateField($field){
        if(empty($field)){
            return false;
        }
        $fieldsetId = input('post.fieldset_id');
        $fieldId = input('post.field_id');
        $map = array();
        $map['fieldset_id'] = $fieldsetId;
        if($fieldId){
            $map['field_id'] = ['neq',$fieldId];
        }
        $map['field'] = $field;
        $info = model('Field')->getWhereInfo($map);
        if(empty($info)){
            return true;
        }else{
            return false;
        }

    }
}