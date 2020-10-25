<?php



class F_Group extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = IS_ADMIN ? fc_lang('分组字段') : '';	// 字段名称
		$this->fieldtype = ''; // TRUE表全部可用字段类型,自定义格式为 array('可用字段类型名称' => '默认长度', ... )
		$this->defaulttype = ''; // 当用户没有选择字段类型时的缺省值
    }
	
	/**
	 * 字段相关属性参数
	 *
	 * @param	array	$value	值
	 * @param	array	$field	字段集合
	 * @return  string
	 */
	public function option($option, $field = NULL) {
		$group = array();
		$option['value'] = isset($option['value']) ? $option['value'] : '';
		if ($field) {
			foreach ($field as $t) {
				if ($t['fieldtype'] == 'Group') {
					$t['setting'] = dr_string2array($t['setting']);
					if (preg_match_all('/\{(.+)\}/U', $t['setting']['option']['value'], $value)) {
						foreach ($value[1] as $v) {
							$group[] = $v;
						}
					}
				}
			}
			$_field = array();
			$_field[] = '<option value=""> -- </option>';
			foreach ($field as $t) {
				if ($t['fieldtype'] != 'Group' && !@in_array($t['fieldname'], $group)) {
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
				<script type="text/javascript">
				$(function() {
					$("#fxx").change(function(){
						var value = $(this).val();
						var fvalue = $("#fvalue").val();
						var text = $("#fxx").find("option:selected").text();
						$("#fxx option[value=\'"+value+"\']").remove();
						$("#fvalue").val(fvalue+"  "+text+": {"+value+"}");
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
		$text = (isset($cfg['validate']['required']) && $cfg['validate']['required'] == 1 ? '<font color="red">*</font>' : '').'&nbsp;'.$cname.'：';
		// 字段提示信息
		$tips = isset($cfg['validate']['tips']) && $cfg['validate']['tips'] ? '<div class="onShow" id="dr_'.$name.'_tips">'.$cfg['validate']['tips'].'</div>' : '';
		// 字段默认值
		$value = $cfg['option']['value'];
		// 当字段必填时，加入html5验证标签
		if (isset($cfg['validate']['required'])
            && $cfg['validate']['required'] == 1) {
            $attr.= ' required="required"';
        }
		return $this->input_format($name, $text, $value.$tips);
	}
	
}