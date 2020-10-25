<?php

/* v3.1.0  */

class F_Property extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = fc_lang('商品属性'); // 字段名称
		$this->fieldtype = array(
			'TEXT' => ''
		); // TRUE表全部可用字段类型,自定义格式为 array('可用字段类型名称' => '默认长度', ... )
		$this->defaulttype = 'TEXT'; // 当用户没有选择字段类型时的缺省值
    }
	
	/**
	 * 字段相关属性参数
	 *
	 * @param	array	$value	值
	 * @return  string
	 */
	public function option($option) {
		
		$width = isset($option['width']) ? $option['width'] : '80%';
		unset($option['width']);
		
		$str = '
		<div class="form-group">
			<label class="col-md-2 control-label">'.fc_lang('宽度').'：</label>
			<div class="col-md-9">
				<label><input type="text" class="form-control" size="10" name="data[setting][option][width]" value="'.$option['width'].'"></label>
				<span class="help-block">'.fc_lang('[整数]表示固定宽带；[整数%]表示百分比').'</span>
			</div>
		</div>
		<div class="form-group dr_option" id="dr_option_0">
			<label class="col-md-2 control-label"><a href="javascript:;" onclick="dr_add_option()" style="color:blue">[+]</a>&nbsp;'.fc_lang('字段说明').'：</label>
			<div class="col-md-9"><div class="form-control-static">'.fc_lang('选择框与复选框类型的选项值以,分隔').'</div></div>
		</div>';
		if ($option) {
			foreach ($option as $i => $t) {
				$str.= '<div class="form-group dr_option" id="dr_option_'.$i.'" >';
				$str.= '<label class="col-md-2 control-label"><a href="javascript:;" onclick="dr_add_option()" style="color:blue">[+]</a>&nbsp;'.fc_lang('属性名称').'：</span>';
				$str.= '<div class="col-md-9"><label><input type="text" name="data[setting][option]['.$i.'][name]" value="'.$t['name'].'" class="form-control" /></label>';
				$str.= '<label>&nbsp;&nbsp;'.fc_lang('类型').'：</label><label><select class="form-control" name="data[setting][option]['.$i.'][type]">';
				$str.= '<option value="1" '.($t['type'] == 1 ? "selected" : "").'> - '.fc_lang('文本框').' - </option>';
				$str.= '<option value="2" '.($t['type'] == 2 ? "selected" : "").'> - '.fc_lang('选择框').' - </option>';
				$str.= '<option value="3" '.($t['type'] == 3 ? "selected" : "").'> - '.fc_lang('复选框').' - </option>';
				$str.= '</select></label>';
				$str.= '<label>&nbsp;&nbsp;'.fc_lang('默认值/选项值').'：</label><label><input type="text" name="data[setting][option]['.$i.'][value]" value="'.$t['value'].'" class="form-control"></label> <label><a onclick="$(\'#dr_option_'.$i.'\').remove()" href="javascript:;">'.fc_lang('删除').'</a></label>';
				$str.= '</div></div>';
			}
		}
		$str.= '
		<script type="text/javascript">
		var id=$(".dr_option").size();
		function dr_add_option() {
			id ++;
			var html = "";
			html+= "<div class=\"form-group dr_option\" id=\"dr_option_"+id+"\" >";
			html+= "<label class=\"col-md-2 control-label\"><a href=\"javascript:;\" onclick=\"dr_add_option()\" style=\"color:blue\">[+]</a>&nbsp;'.fc_lang('属性名称').'：</label>";
			html+= "<div class=\"col-md-9\">";
			html+= "<label><input type=\"text\" name=\"data[setting][option]["+id+"][name]\" value=\"\" class=\"form-control\" /></label>";
			html+= "<label>&nbsp;&nbsp;'.fc_lang('网站').'：</label><label><select class=\"form-control\" name=\"data[setting][option]["+id+"][type]\">";
			html+= "<option value=\"1\"> - '.fc_lang('文本框').' - </option>";
			html+= "<option value=\"2\"> - '.fc_lang('选择框').' - </option>";
			html+= "<option value=\"3\"> - '.fc_lang('复选框').' - </option>";
			html+= "</select></label>";
			html+= "<label>&nbsp;&nbsp;'.fc_lang('默认值/选项值').'：</label><label><input type=\"text\" name=\"data[setting][option]["+id+"][value]\" class=\"form-control\"></label><label><a onclick=\"$(\'#dr_option_"+id+"\').remove()\" href=\"javascript:;\">'.fc_lang('删除').'</a></label>";
			html+= "</div>";
			html+= "</div>";
			$("#dr_option").append(html);
		}
		</script>
		';
		return $str.
        '<div class="form-group">
				<label class="col-md-2 control-label">'.fc_lang('友情提示').'：</label>
				<div class="col-md-9" style="color:blue"> <div class="form-control-static">'.fc_lang('此字段不能参与搜索条件筛选').'</div></div>
			</div>';
	}
	
	/**
	 * 字段输出
	 */
	public function output($value) {
		return dr_string2array($value);
	}
	
	/**
	 * 字段入库值
	 */
	public function insert_value($field) {

        $data = array();
        $value = $this->ci->post[$field['fieldname']];
        if ($value) {
            $i = 1;
            foreach ($value as $t) {
                $data[$i] = $t;
                $i++;
            }
        }

		$this->ci->data[$field['ismain']][$field['fieldname']] = dr_array2string($data);
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
		// 显示框宽度设置
		$width = isset($cfg['option']['width']) && $cfg['option']['width'] ? $cfg['option']['width'] : '80%';
		unset($cfg['option']['width']);
		// 字段提示信息
		$tips = isset($cfg['validate']['tips']) && $cfg['validate']['tips'] ? '<div class="onShow" id="dr_'.$name.'_tips">'.$cfg['validate']['tips'].'</div>' : '';
		// 字段默认值
		$value = $value ? dr_string2array($value) : array();
		// 禁止修改
		$disabled = !IS_ADMIN && $id && $value && isset($cfg['validate']['isedit']) && $cfg['validate']['isedit'] ? 'disabled' : ''; 
		$str = '';
		// 加载js
		if (!defined('FINECMS_FILES_LD')) {
			$str.= '<script type="text/javascript" src="'.MEMBER_PATH.'statics/js/jquery-ui.min.js"></script>';
			define('FINECMS_FILES_LD', 1);//防止重复加载JS
		}
		$str.= '<fieldset class="blue pad-10" style="width:'.$width.(is_numeric($width) ? 'px' : '').';">';
        $str.= '	<legend>'.$cname.'</legend>';
        $str.= '	<div class="picList" id="list_'.$name.'_property">';
		$str.= '		<ul id="'.$name.'-sort-items">';
		$i = 0;
		if (isset($cfg['option']) && $cfg['option']) {
		    // 默认属性选项
            $i = 1;
			foreach ($cfg['option'] as $t) {
				$str.= '<li id="dr_items_'.$name.'_'.$i.'">';
				$str.= '属性：<input type="text" '.$disabled.' class="input-text" style="width:140px;" value="'.$t['name'].'" name="data['.$name.']['.$i.'][name]">&nbsp;&nbsp;';
				$str.= '值：';
				switch ($t['type']) {
					case 1:
						$v = $value[$i]['value'] ? $value[$i]['value'] : $t['value'];
						$str.= '<input '.$disabled.' type="text" class="input-text" style="width:300px;" value="'.$v.'" name="data['.$name.']['.$i.'][value]" />';
						break;
					case 2:
						$v = @explode(',', $t['value']);
						$str.= '<select '.$disabled.' name="data['.$name.']['.$i.'][value]">';
						$str.= '<option value=""> -- </option>';
						if ($v) {
							foreach ($v as $c) {
								$selected = isset($value[$i]['value']) && $value[$i]['value'] == $c ? 'selected' : '';
								$str.= '<option value="'.$c.'" '.$selected.'> '.$c.' </option>';
							}
						}
						$str.= '</select>';
						break;
					case 3:
						$v = @explode(',', $t['value']);
						if ($v) {
							foreach ($v as $c) {
								$selected = isset($value[$i]['value']) && @in_array($c, $value[$i]['value']) ? 'checked' : '';
								$str.= '<input '.$disabled.' type="checkbox" name="data['.$name.']['.$i.'][value][]" value="'.$c.'" ' . $selected . ' />'.$c.'&nbsp;&nbsp;&nbsp;';
							}
						}
				}
                unset($value[$i]);
                $i++;
			}
		}
		// 剩下自定义属性
		if ($value) {
			foreach ($value as $t) {
                $str.= '<li id="dr_items_'.$name.'_'.$i.'">';
                $str.= '属性：<input type="text" '.$disabled.' class="input-text" style="width:140px;" value="'.$t['name'].'" name="data['.$name.']['.$i.'][name]">&nbsp;&nbsp;';
                $str.= '值：';
                $str.= '<input type="text" '.$disabled.' class="input-text" style="width:300px;" value="'.$t['value'].'" name="data['.$name.']['.$i.'][value]" />';
                $str.= '<a href="javascript:;" onclick="$(\'#dr_items_'.$name.'_'.$i.'\').remove()">'.fc_lang('删除').'</a></li>';
                $i++;
			}
		}
		
		$str.= '		</ul>';
		$str.= '	</div>';
		$str.= '</fieldset>';
		$str.= '<div class="bk10"></div>';
		$str.= '<div class="">';
		$str.= '	<a href="javascript:;" class="btn blue btn-sm" onClick="dr_add_property_'.$name.'()"> <i class="fa fa-flag"></i> 添加属性 </a>';
		$str.= '</div>';
		$str.= '<script type="text/javascript">
		$("#'.$name.'-sort-items").sortable();
		function dr_add_property_'.$name.'() {
			var id=($("#'.$name.'-sort-items li").size() + 1) * 10;
			var html = "<li id=\"dr_items_'.$name.'_"+id+"\">";
			html+= "属性：<input type=\"text\" class=\"input-text\" style=\"width:140px;\" value=\"\" name=\"data['.$name.']["+id+"][name]\">&nbsp;&nbsp;";
			html+= "值：<input type=\"text\" class=\"input-text\" style=\"width:300px;\" value=\"\" name=\"data['.$name.']["+id+"][value]\">&nbsp;&nbsp;";
			html+= "<a href=\"javascript:;\" onclick=\"$(\'#dr_items_'.$name.'_"+id+"\').remove()\">'.fc_lang('删除').'</a></li>";
			$("#'.$name.'-sort-items").append(html);
		}
		</script><span class="help-block">'.$tips.'</span>';
		return $this->input_format($name, $text, $str);
	}
	
}