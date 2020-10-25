<?php



class F_Hidden extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = IS_ADMIN ? fc_lang('隐藏域') : ''; // 字段名称
    }
	
	/**
	 * 字段相关属性参数
	 *
	 * @param	array	$value	值
	 * @return  string
	 */
	public function option($option) {

		return '
            <div class="form-group">
                <label class="col-md-2 control-label">'.fc_lang('后台是否显示').'：</label>
                <div class="col-md-9">
					<div class="radio-list">
						<label class="radio-inline"><input type="radio" value="1" name="data[setting][option][is_admin_show]" '.($option['is_admin_show'] ? 'checked' : '').'> '.fc_lang('是').'</label>
						<label class="radio-inline"><input type="radio" value="0" name="data[setting][option][is_admin_show]" '.(!$option['is_admin_show'] ? 'checked' : '').'> '.fc_lang('否').'</label>
					</div>
                </div>
            </div>
			<div class="form-group">
				<label class="col-md-2 control-label">'.fc_lang('默认值').'：</label>
				<div class="col-md-9">
					<label><input id="field_default_value" type="text" class="form-control" size="20" value="'.$option['value'].'" name="data[setting][option][value]"></label>
					<label>'.$this->member_field_select().'</label>
					<span class="help-block">'.fc_lang('也可以设置会员表字段，表示用当前登录会员信息来填充这个值').'</span>
				</div>
			</div>
			'.$this->field_type($option['fieldtype'], $option['fieldlength']);
	}

    /**
     * 字段入库值
     *
     * @param	array	$field	字段信息
     * @return  void
     */
    public function insert_value($field) {
		// 格式化入库值
		$value = $this->ci->post[$field['fieldname']];
		if (in_array($field['setting']['option']['fieldtype'], array('INT', 'TINYINT', 'SMALLINT'))) {
			$this->ci->data[$field['ismain']][$field['fieldname']] = $value ? (int)$value : 0;
		} elseif (in_array($field['setting']['option']['fieldtype'], array('DECIMAL', 'FLOAT'))) {
			$this->ci->data[$field['ismain']][$field['fieldname']] = $value ? (float)$value : 0;
		} elseif ($field['setting']['option']['fieldtype'] == 'MEDIUMINT') {
			$this->ci->data[$field['ismain']][$field['fieldname']] = $value ? $value : 0;
		} else {
			$this->ci->data[$field['ismain']][$field['fieldname']] = htmlspecialchars($value);
		}
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
        // 字段默认值
		$value = (@strlen($value) ? $value : $this->get_default_value($cfg['option']['value']));
		// 禁止修改
        if (!IS_ADMIN && $id && $value && isset($cfg['validate']['isedit']) && $cfg['validate']['isedit']) {
            $str = '<input type="hidden" name="data['.$name.']" id="dr_'.$name.'" value="'.$value.'"> <div class="form-control-static">'.$value.'</div>'.($cfg['append'] ? $cfg['append'] : '');
        } else {
            // 当字段必填时，加入html5验证标签
            $required = isset($cfg['validate']['required']) && $cfg['validate']['required'] == 1 ? ' required="required"' : '';
            $str = '<input class="form-control" type="hidden" name="data['.$name.']" id="dr_'.$name.'" value="'.$value.'" '.$required.' />';
			if ($cfg['option']['is_admin_show']) {
				$str.= '<div class="form-control-static">'.$value.'</div>';
			}
        }
		return IS_ADMIN && $cfg['option']['is_admin_show'] ? $this->input_format($name, $text, $str) : '';
	}
	
}