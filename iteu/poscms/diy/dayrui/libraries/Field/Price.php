<?php



class F_Price extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = fc_lang('订单价格字段'); // 字段名称
		$this->fieldtype = array('DECIMAL' => '10,2'); // TRUE表全部可用字段类型,自定义格式为 array('可用字段类型名称' => '默认长度', ... )
		$this->defaulttype = 'DECIMAL'; // 当用户没有选择字段类型时的缺省值
    }
	
	/**
	 * 字段相关属性参数
	 *
	 * @param	array	$value	值
	 * @return  string
	 */
	public function option($option) {

        $option['value'] = isset($option['value']) ? $option['value'] : '';
		$option['width'] = isset($option['width']) ? $option['width'] : 200;
        $option['unique'] = isset($option['unique']) ? $option['unique'] : 0;
		$option['fieldtype'] = isset($option['fieldtype']) ? $option['fieldtype'] : '';
		$option['is_mb_auto'] = isset($option['is_mb_auto']) ? $option['is_mb_auto'] : '';
		$option['fieldlength'] = isset($option['fieldlength']) ? $option['fieldlength'] : '';
		return '
			<div class="form-group">
				<label class="col-md-2 control-label">'.fc_lang('宽度').'：</label>
				<div class="col-md-9">
					<label><input type="text" class="form-control" size="10" name="data[setting][option][width]" value="'.$option['width'].'"></label>
					<span class="help-block">'.fc_lang('[整数]表示固定宽带；[整数%]表示百分比').'</span>
				</div>
			</div>
            <div class="form-group">
                <label class="col-md-2 control-label">'.fc_lang('移动端自动宽度').'：</label>
                <div class="col-md-9">
					<div class="radio-list">
						<label class="radio-inline"><input type="radio" value="0" name="data[setting][option][is_mb_auto]" '.(!$option['is_mb_auto'] ? 'checked' : '').'> '.fc_lang('是').'</label>
						<label class="radio-inline"><input type="radio" value="1" name="data[setting][option][is_mb_auto]" '.($option['is_mb_auto'] ? 'checked' : '').'> '.fc_lang('否').'</label>
					</div>
                </div>
            </div>
			<div class="form-group">
				<label class="col-md-2 control-label">'.fc_lang('适用范围').'：</label>
				<div class="col-md-9">
					<div class="form-control-static">'.fc_lang('该字段用于订单模块的价格绑定，只能用于内容模块主表').'</div>
				</div>
			</div>
			';
	}

    /**
     * 字段入库值
     *
     * @param	array	$field	字段信息
     * @return  void
     */
    public function insert_value($field) {
        $this->ci->data[$field['ismain']][$field['fieldname']] = (float)$this->ci->post[$field['fieldname']];
    }

	/**
	 * 字段表单输入
	 *
	 * @param	string	$cname	字段别名
	 * @param	string	$name	字段名称
	 * @param	array	$cfg	字段配置
	 * @param	array	$value	值
	 * @param	array	$id		当前内容表的id（表示非发布操作）
	 * @return  string
	 */
	public function input($cname, $name, $cfg, $value = NULL, $id = 0) {
		// 字段显示名称
		$text = (isset($cfg['validate']['required']) && $cfg['validate']['required'] == 1 ? '<font color="red">*</font>' : '').''.$cname.'：';
		// 表单宽度设置
		if (IS_MOBILE && empty($cfg['option']['is_mb_auto'])) {
			$width = '100%';
		} else {
			$width = isset($cfg['option']['width']) && $cfg['option']['width'] ? $cfg['option']['width'] : '200';
		}
		$width = 'style="width:'.$width.(is_numeric($width) ? 'px' : '').';"';
		// 表单附加参数
		$attr = isset($cfg['validate']['formattr']) && $cfg['validate']['formattr'] ? $cfg['validate']['formattr'] : '';
		// 字段提示信息
		$tips = ($name == 'title' && APP_DIR) || (isset($cfg['validate']['tips']) && $cfg['validate']['tips']) ? '<span class="onShow" id="dr_'.$name.'_tips">'.$cfg['validate']['tips'].'</span>' : '';
		// 字段默认值
		$value = (strlen($value) ? $value : $this->get_default_value($cfg['option']['value']));
		// 禁止修改
		$disabled = !IS_ADMIN && $id && $value && isset($cfg['validate']['isedit']) && $cfg['validate']['isedit'] ? ' disabled' : '';
		// 当字段必填时，加入html5验证标签
		$required = '';
		return $this->input_format($name, $text, '<input class=" form-control" type="text" name="data['.$name.']" id="dr_'.$name.'" value="'.$value.'" '.$width.$disabled.$required.' '.$attr.' />'.$tips);
	}
	
}