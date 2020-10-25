<?php

/* v3.1.0  */

class F_Merge extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = '字段分组合并';	// 字段名称
		$this->fieldtype = array('TEXT'); // TRUE表全部可用字段类型,自定义格式为 array('可用字段类型名称' => '默认长度', ... )
		$this->defaulttype = 'TEXT'; // 当用户没有选择字段类型时的缺省值
    }
	
	/**
	 * 字段相关属性参数
	 *
	 * @param	array	$value	值
	 * @param	array	$field	字段集合
	 * @return  string
	 */
	public function option($option, $field = NULL) {
		$Merge = array();
		$option['value'] = isset($option['value']) ? $option['value'] : '';
		if ($field) {
			foreach ($field as $t) {
				if ($t['fieldtype'] == 'Merge') {
					$t['setting'] = dr_string2array($t['setting']);
					if (preg_match_all('/\{(.+)\}/U', $t['setting']['option']['value'], $value)) {
						foreach ($value[1] as $v) {
							$Merge[] = $v;
						}
					}
				}
			}
			$_field = array();
			$_field[] = '<option value=""> -- </option>';
			foreach ($field as $t) {
				if ($t['fieldtype'] != 'Merge' && !@in_array($t['fieldname'], $Merge)) {
					$_field[] = '<option value="'.$t['fieldname'].'">'.$t['name'].'</option>';
				}
			}
			$_field = @implode('', @array_unique($_field));
		}
		
		return '
				<div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('可用字段').'：</label>
                    <div class="col-md-9">
                    <label><select class="form-control" name="xx" id="fxx">'.$_field.'</select></label>
                    <span class="help-block">'.fc_lang('同一种随机颜色的字段表示在同一个分组').'</span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('分组规则').'：</label>
                    <div class="col-md-9">
                    <textarea name="data[setting][option][value]" id="fvalue" style="height:120px;" class="form-control">'.$option['value'].'</textarea>
					<span class="help-block">'.fc_lang('分组规则支持html标签，注意每个字段只能存在于一个分组中，否则会出错').'</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('适用范围').'：</label>
                    <div class="col-md-9">
                        <div class="form-control-static">'.fc_lang('此字段只能用于模块内容和模块扩展内容').'</div>
                    </div>
                </div>
				<script type="text/javascript">
				$(function() {
					$("#fxx").change(function(){
						var value = $(this).val();
						var fvalue = $("#fvalue").val();
						var text = $("#fxx").find("option:selected").text();
						$("#fxx option[value=\'"+value+"\']").remove();
						$("#fvalue").val(fvalue+"\n{"+value+"}");
					});
				}); 
				</script>
				';
	}
	
	/**
	 * create_sql
	 */
	public function create_sql($name, $value) {
		
	}
	
	/**
	 * alter_sql
	 */
	public function alter_sql($name, $value) {
		
	}
	
	/**
	 * drop_sql
	 */
	public function drop_sql($name) {
		
	}
	
	/**
	 * 字段入库值
	 */
	public function insert_value($field) {
		
	}
	
	/**
	 * 字段表单输入
	 *
	 * @param	string	$cname	字段别名
	 * @param	string	$name	字段名称
	 * @param	array	$cfg	字段配置
	 * @param	array	$data	值
	 * @return  string
	 */
	public function input($cname, $name, $cfg, $value = NULL, $id = 0) {
		// 字段显示名称
		$text = (isset($cfg['validate']['required']) && $cfg['validate']['required'] == 1 ? '<font color="red">*</font>' : '').''.$cname.'：';
		// 字段提示信息
		$tips = isset($cfg['validate']['tips']) && $cfg['validate']['tips'] ? '<span class="help-block" id="dr_'.$name.'_tips">'.$cfg['validate']['tips'].'</span>' : '';
		// 字段默认值
		return $this->input_format($name, $text, $cfg['option']['value'].$tips);
	}
	
}