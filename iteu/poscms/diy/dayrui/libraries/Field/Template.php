<?php

/* v3.1.0  */

class F_Template extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = IS_ADMIN ? fc_lang('模板文件') : ''; // 字段名称
		$this->fieldtype = TRUE; // TRUE表全部可用字段类型,自定义格式为 array('可用字段类型名称' => '默认长度', ... )
		$this->defaulttype = 'VARCHAR'; // 当用户没有选择字段类型时的缺省值
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
				<label class="col-md-2 control-label">'.fc_lang('默认值').'：</label>
				<div class="col-md-9">
					<label><input id="field_default_value" type="text" class="form-control" size="20" value="'.$option['value'].'" name="data[setting][option][value]"></label>
					<label>'.$this->member_field_select().'</label>
					<span class="help-block">'.fc_lang('也可以设置会员表字段，表示用当前登录会员信息来填充这个值').'</span>
				</div>
			</div>';
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
		$text = (isset($cfg['validate']['required']) && $cfg['validate']['required'] == 1 ? '<font color="red">*</font>' : '').'&nbsp;'.$cname.'：';
		// 表单附加参数
		$attr = isset($cfg['validate']['formattr']) && $cfg['validate']['formattr'] ? $cfg['validate']['formattr'] : '';
		// 字段提示信息
		$tips = isset($cfg['validate']['tips']) && $cfg['validate']['tips'] ? '<div class="onShow" id="dr_'.$name.'_tips">'.$cfg['validate']['tips'].'</div>' : '';
		// 字段默认值
		$value = strlen($value) ? $value : $this->get_default_value($cfg['option']['value']);
		// 当字段必填时，加入html5验证标签
		if (isset($cfg['validate']['required'])
            && $cfg['validate']['required'] == 1) {
            $attr.= ' required="required"';
        }
		// 表单选项
		$disabled = !IS_ADMIN && $id && $value && isset($cfg['validate']['isedit']) && $cfg['validate']['isedit'] ? 'disabled' : ''; 
		$str = '<label><select class="form-control" '.$disabled.' name="data['.$name.']" id="dr_'.$name.'" '.$attr.' ><option value=""> --- </option>';
        // 文件
        $this->ci->load->helper('directory');
        if (APP_DIR && APP_DIR != 'member' && is_dir(FCPATH.'module/'.APP_DIR)) {
            // 模块
            $tpl = $this->ci->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'site', SITE_ID, 'template');
            $tpl = $tpl ? $tpl : 'default';
            $files = directory_map(FCPATH.'module/'.APP_DIR.'/templates/'.$tpl.'/', 1);
            $files2 = directory_map(FCPATH.'dayrui/templates/'.$tpl.'/'.APP_DIR.'/', 1);
			if ($files2) {
				$files = array_merge($files2, $files);
			}
        } elseif (APP_DIR == 'member' || IS_MEMBER) {
            if (defined('IS_SPACE_THEME')) {
                // 会员空间
                $files = directory_map(FCPATH.'module/member/templates/space/'.IS_SPACE_THEME.'/', 1);
            } else {
                // 会员中心
                $files = directory_map(FCPATH.'module/member/templates/member/'.MEMBER_TEMPLATE.'/', 1);
            }
        } else {
            // 网站
            $files = directory_map(FCPATH.'dayrui/templates/'.SITE_TEMPLATE.'/', 1);
        }
        if ($files) {
			foreach ($files as $t) {
				if ($t && strpos($t, '.html')) {
					$selected = '';
					$selected = $t==$value ? ' selected' : '';
					$str.= '<option value="'.$t.'" '.$selected.'>'.$t.'</option>';
				}
			}
        }
		$str.= '</select></label>'.$tips;
		return $this->input_format($name, $text, $str);
	}
	
}