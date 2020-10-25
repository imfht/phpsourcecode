<?php



class F_Syn extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = '站点同步';	// 字段名称
		$this->fieldtype = array('TEXT' => '');; // TRUE表全部可用字段类型,自定义格式为 array('可用字段类型名称' => '默认长度', ... )
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

        $option['width'] = isset($option['width']) ? $option['width'] : '80%';
        return '<div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('宽度').'：</label>
                    <div class="col-md-9">
                        <label><input type="text" class="form-control" name="data[setting][option][width]" value="'.$option['width'].'"></label>
					    <span class="help-block">'.fc_lang('[整数]表示固定宽带；[整数%]表示百分比').'</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('使用说明').'：</label>
                    <div class="col-md-9">
                        <div class="form-control-static">'.fc_lang('此字段只能用于模块内容的发布操作，修改时不会出现（请不要用于含有附加字段的栏目）').'</div>
                    </div>
                </div>';
	}
	
	/**
	 * 字段入库值
	 */
	public function insert_value($field) {
        $data = $this->ci->post[$field['fieldname']];
        if (!$data['use']) {
            $data = array();
        }
        $this->ci->data[$field['ismain']][$field['fieldname']] = dr_array2string($data);
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

        // 编辑不出现
        if ($id) {
            return;
        }

		// 字段显示名称
		$text = (isset($cfg['validate']['required']) && $cfg['validate']['required'] == 1 ? '<font color="red">*</font>' : '').''.$cname.'：';

		// 字段提示信息
		$tips = isset($cfg['validate']['tips']) && $cfg['validate']['tips'] ? '<span class="help-block" id="dr_'.$name.'_tips">'.$cfg['validate']['tips'].'</span>' : '';

        // 显示框宽度设置
        $width = isset($cfg['option']['width']) && $cfg['option']['width'] ? $cfg['option']['width'] : '80%';

        // 默认值
        $value = IS_POST ? $_POST['data']['syn'] : dr_string2array($value);

        // 查询当前模块是否在其他站点中出现
        $site = $this->ci->get_cache('module');
        unset($site[SITE_ID]);

        //
        $str = '<input type="radio" name="data['.$name.'][use]" onclick="$(\'#dr_div_syn\').hide()" value="0" '.(!$value['use'] ? 'checked' : '').' />&nbsp;关闭&nbsp;&nbsp;&nbsp;&nbsp;';
        $str.= '<input type="radio" name="data['.$name.'][use]" onclick="$(\'#dr_div_syn\').show()" value="1" '.($value['use'] ? 'checked' : '').' />&nbsp;开启';
        $str.= '<div class="dr_format_wrap" id="dr_div_syn" style="margin-top:5px;'.($value['use'] ? '' : 'display:none').';width:'.$width.'">';
        $str.= '<table width="100%">';

        // 查询当前模块是否在其他站点中出现
        if ($site) {
            foreach ($site as $sid => $dirs) {
                if (in_array(APP_DIR, $dirs)) {
                    $str.= '<tr>';
                    $str.= '	<td width=150 align=right>'.dr_strcut($this->ci->SITE[$sid]['SITE_NAME'], 15).'：</td>';
                    $str.= '	<td>'.$this->ci->select_category($this->ci->get_cache('module-'.$sid.'-'.APP_DIR, 'category'), $value[$sid], ' name=\'data['.$name.']['.$sid.']\'', ' -- ', 1, 1).'</td>';
                    $str.= '</tr>';
                }
            }
        }

        $str.= '</table>';
        $str.= '</div>';

		return $this->input_format($name, $text, $str.$tips);
	}
	
}