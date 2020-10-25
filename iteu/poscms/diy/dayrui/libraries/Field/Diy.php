<?php

/* v3.1.0  */

class F_Diy extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = 'DIY（需要开发经验）'; // 字段名称
        $this->fieldtype = TRUE; // TRUE表全部可用字段类型,自定义格式为 array('可用字段类型名称' => '默认长度', ... )
        $this->defaulttype = 'TEXT'; // 当用户没有选择字段类型时的缺省值
    }
	
	/**
	 * 字段相关属性参数
	 *
	 * @param	array	$value	值
	 * @return  string
	 */
	public function option($option) {

        $option['type'] = isset($option['type']) ? $option['type'] : 0;
        $option['code'] = isset($option['code']) ? $option['code'] : '';
        $option['file'] = isset($option['file']) ? $option['file'] : '';

        $this->ci->load->helper('directory');
        $str = '<select class="form-control" name="data[setting][option][file]"><option value=""> -- </option>';
        $files = directory_map(WEBPATH.'cache/field/', 1);
        if ($files) {
            foreach ($files as $t) {
                if ($t && strpos($t, 'sys_') !== 0) {
                    $str.= '<option value="'.$t.'" '.($option['file'] == $t ? 'selected' : '').'> '.$t.' </option>';
                }
            }
        }
        $str.= '</select>';

		return '<div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('类型').'：</label>
                    <div class="col-md-9">
                    <div class="radio-list">
                    <label class="radio-inline"><input type="radio" onclick="dr_diy_type(0)" value="0" name="data[setting][option][type]" '.(!$option['type'] ? 'checked' : '').'> '.fc_lang('代码模式').'</label>
                    <label class="radio-inline"><input type="radio" onclick="dr_diy_type(1)" value="1" name="data[setting][option][type]" '.($option['type'] ? 'checked' : '').'> '.fc_lang('文件模式').'</label>
                    </div>
                    </div>
                </div>
                <div class="form-group" id="dr_diy_type_0" style="display:none">
                    <label class="col-md-2 control-label">'.fc_lang('代码').'：</label>
                    <div class="col-md-9">
                    <textarea class="form-control" name="data[setting][option][code]" style="height:150px;width:80%;">'.$option['code'].'</textarea>
					<span class="help-block">'.fc_lang('将设计好的代码放到这里，<a style="color:blue" href="http://help.dayrui.com/216.html" target="_blank">设计指南</a>').'</span>
                    </div>
                </div>
                <div class="form-group" id="dr_diy_type_1" style="display:none">
                    <label class="col-md-2 control-label">'.fc_lang('文件').'：</label>
                    <div class="col-md-9">
                    <label>'.$str.'</label>
                    <span class="help-block">'.fc_lang('将设计好的文件上传到/cache/field/目录之下，<a href="http://help.dayrui.com/215.html" target="_blank">设计指南</a>').'</span>
                    </div>
                </div>
                <script>$("#dr_diy_type_'.$option['type'].'").show();</script>
				<div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('默认值').'：</label>
                    <div class="col-md-9">
                    <label><input id="field_default_value" type="text" class="form-control" size="20" value="'.$option['value'].'" name="data[setting][option][value]"></label>
					<label>'.$this->member_field_select().'</label>
					<span class="help-block">'.fc_lang('也可以设置会员表字段，表示用当前登录会员信息来填充这个值').'</span>
                    </div>
                </div>'.$this->field_type($option['fieldtype'], $option['fieldlength']);
	}
	
	/**
	 * 字段入库值
	 */
	public function insert_value($field) {
		$data = $this->ci->post[$field['fieldname']];
		if (is_array($data)) {
			$data = dr_array2string($data);
		}
		$this->ci->data[$field['ismain']][$field['fieldname']] = $data;
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
		$tips = isset($cfg['validate']['tips']) && $cfg['validate']['tips'] ? '<span class="help-block" id="dr_'.$name.'_tips">'.$cfg['validate']['tips'].'</span>' : '';
		// 字段默认值
		$value = @strlen($value) ? $value : $this->get_default_value($cfg['option']['value']);
		// 当字段必填时，加入html5验证标签
		if (isset($cfg['validate']['required'])
            && $cfg['validate']['required'] == 1) {
            $attr.= ' required="required"';
        }
		// 表单选项
		$disabled = !IS_ADMIN && $id && $value && isset($cfg['validate']['isedit']) && $cfg['validate']['isedit'] ? 'disabled' : ''; 
		if ($cfg['option']['type']) {
            // 文件类型
            $file = WEBPATH.'cache/field/'.$cfg['option']['file'];
            if (is_file($file)) {
                require_once $file;
            } elseif (!$cfg['option']['file']) {
                $code = '<font color=red>没有选择文件，在字段属性中选择</font>';
            } else {
                $code = '<font color=red>文件（'.$file.'）不存在</font>';
            }
        } else {
            // 代码类型
            if ($cfg['option']['code']) {
                ob_start();
                $file = $this->ci->template->code2php($cfg['option']['code']);
                require_once $file;
                $code = ob_get_clean();
                if (strpos($code, '<!--FineCMS error-->')) {
                    $code = '<font color=red>代码解析出错，检查代码是否正确</font>';
                }
            } else {
                $code = '<font color=red>没有可用代码，在字段属性中添加</font>';
            }
        }
		return $this->input_format($name, $text, $code);
	}
	
}