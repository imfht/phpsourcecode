<?php

/**
 * Dayrui Website Management System
 *
 * @since		version 2.0.0
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 */

class F_Order_buy extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = '订单购买流程'; // 字段名称
		$this->fieldtype = array('VARCHAR' => 200); // TRUE表全部可用字段类型,自定义格式为 array('可用字段类型名称' => '默认长度', ... )
		$this->defaulttype = 'VARCHAR'; // 当用户没有选择字段类型时的缺省值
    }
	
	/**
	 * 字段相关属性参数
	 *
	 * @param	array	$value	值
	 * @return  string
	 */
	public function option($option) {

		return '';
	}
	
	/**
	 * 字段表单输入
	 *
	 * @param	string	$cname	字段别名
	 * @param	string	$name	字段名称
	 * @param	array	$cfg	字段配置
	 * @param	string	$value	值
	 * @return  string
	 */
	public function input($cname, $name, $cfg, $value = NULL, $id = 0) {
		// 字段显示名称
		$text = (isset($cfg['validate']['required']) && $cfg['validate']['required'] == 1 ? '<font color="red">*</font>' : '').''.$cname.'：';
		// 表单附加参数
		$attr = isset($cfg['validate']['formattr']) && $cfg['validate']['formattr'] ? $cfg['validate']['formattr'] : '';
		// 字段提示信息
		$tips = isset($cfg['validate']['tips']) && $cfg['validate']['tips'] ? '<div class="help-block" id="dr_'.$name.'_tips">'.$cfg['validate']['tips'].'</div>' : '';
		// 字段默认值
		$value = intval($value);
		// 当字段必填时，加入html5验证标签
		if (isset($cfg['validate']['required'])
            && $cfg['validate']['required'] == 1) {
            $attr.= ' required="required"';
        }
		// 表单选项
		$disabled = !IS_ADMIN && $id && $value && isset($cfg['validate']['isedit']) && $cfg['validate']['isedit'] ? 'disabled' : '';
		$str = '<label><select class="form-control" '.$disabled.' name="data['.$name.']" id="dr_'.$name.'" '.$attr.' >';
        $options = array(
            0 => fc_lang('付款→发货→收货→完成'),
            1 => fc_lang('付款→发货→完成'),
            2 => fc_lang('付款→完成'),
        );
        foreach ($options as $v => $n) {
            $str.= '<option value="'.$v.'" '.($v == $value ? ' selected' : '').'>'.$n.'</option>';
        }
		$str.= '</select></label>'.$tips;
		return $this->input_format($name, $text, $str);
	}
	
}