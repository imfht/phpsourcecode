<?php



/**
 * 自定义字段
 */

class Dfield {

	public $module = NULL;
	public $objects = array();

	/**
	 * 自定义字段
	 */
	public function __construct($module) {
		$this->module = isset($module[0]) ? $module[0] : NULL;
	}

	/**
	 * 获取字段类别对象
	 *
	 * @param   string $name    字段类别名称
	 * @param   string	$module	模块名称
	 * @return  object
	 */
	public function get($name) {

		if (!$name || strpos($name, '.') !== FALSE) {
			return NULL;
		}

		$name = ucfirst(strtolower($name));
		$class = 'F_'.$name;
		$file = FCPATH.'dayrui/libraries/Field/'.$name.'.php';

		if (!is_file($file) && $this->module) {
			if (is_file(FCPATH.'module/'.$this->module.'/libraries/Field/'.$name.'.php')) {
				$file = FCPATH.'module/'.$this->module.'/libraries/Field/'.$name.'.php';
			} else {
				if (IS_ADMIN) {
					show_error('字段【'.$name.'】文件（'.str_replace(FCPATH, '', $file).'）不存在！');
				}
				return NULL;
			}
		}

		if (isset($this->objects[$class])) {
			return $this->objects[$class];
		}

		require $file;

		return $this->objects[$class] = new $class();
	}

	/**
	 * 自定义字段选项信息
	 *
	 * @param   string	$name	字段类别名称
	 * @param   array 	$option	选项值
	 * @param	array	$field	字段集合
	 * @return  string
	 */
	public function option($name, $option = NULL, $field = NULL) {
		return $name ? $this->get(ucfirst(strtolower($name)))->option($option, $field) : NULL;
	}

	/**
	 * 获取可用字段类别
	 *
	 * @return  array
	 */
	public function type() {

		$dir = FCPATH.'dayrui/libraries/Field/';
		$data = array();

		if ($fp = @opendir($dir)) {
			while (FALSE !== ($file = readdir($fp))) {
				if (is_file($dir.$file) && strpos($file, '.php') !== FALSE) {
					$name = substr($file, 0, -4);
					$obj = $this->get($name);
					$data[] = array('id' => $name, 'name' => $obj->name);
				}
			}
			closedir($fp);
		}

		if ($this->module) {
			$dir = FCPATH.'module/'.$this->module.'/libraries/Field/';
			if ($fp = @opendir($dir)) {
				while (FALSE !== ($file = readdir($fp))) {
					if (is_file($dir.$file) && strpos($file, '.php') !== FALSE) {
						$name = substr($file, 0, -4);
						$obj = $this->get($name);
						$data[] = array('id' => $name, 'name' => $obj->name);
					}
				}
				closedir($fp);
			}
		}

		return $data;
	}
}


/**
 * 自定义字段抽象类
 */

abstract class A_Field  {

	public $ci; // ci控制器对象
	public $name; // 字段类别的名字
	public $remove_div; // 去掉div盒模块
	protected $fieldtype; // 可用字段类型
	protected $defaulttype;	// 默认字段类型
	protected $fields = array(
		'INT' => 10,
		'TINYINT' => 3,
		'SMALLINT' => 5,
		'MEDIUMINT' => 8,
		'DECIMAL' => '10,2',
		'FLOAT' => '8,2',
		'CHAR' => 100,
		'VARCHAR' => 255,
		'TEXT' => '',
		'MEDIUMTEXT' => ''

	);	// 内置可用字段及默认长度
	static public $format = '
<div class="form-group" id="dr_row_{name}">
    <label class="control-label col-md-2">{text}</label>
    <div class="col-md-9">{value}</div>
</div>'; // 格式化字段输入表单

	/**
	 * 构造函数
	 */
	public function __construct() {
		$this->ci = &get_instance();
	}

	static function set_input_format($value) {
		self::$format = $value;
	}

	/**
	 * 字段相关属性参数
	 *
	 * @param	array	$option
	 * @return  string
	 */
	abstract public function option($option);

	/**
	 * 字段表单输入
	 *
	 * @param	string	$cname	字段别名
	 * @param	string	$name	字段名称
	 * @param	array	$cfg	字段配置
	 * @param	array	$value	值
	 * @param	array	$id		当前内容表的id
	 * @return  string
	 */
	abstract function input($cname, $name, $cfg, $value = NULL, $id = 0);

	/**
	 * 字段输出
	 *
	 * @param	array	$value	数据库值
	 * @return  string
	 */
	public function output($value) {
		return $value;
	}

	/**
	 * 获取附件id
	 *
	 * @param	array	$value	数据库值
	 * @return  array
	 */
	public function get_attach_id($value) {

	}

	/**
	 * 字段入库后执行的脚本
	 *
	 * @param	array	$field	字段信息
	 * @return  void
	 */
	public function insert_last_value($id, $data, $field) {

	}

	/**
	 * 附件处理
	 *
	 * @param	$data	当前的附件数据
	 * @param	$_data	原来的附件数据
	 * @return  返回当前字段使用的附件id集合与待删除的id集合
	 */
	public function attach($data, $_data) {
		return NULL;
	}

	/**
	 * 字段入库值
	 *
	 * @param	array	$field	字段信息
	 * @return  void
	 */
	public function insert_value($field) {
		$this->ci->data[$field['ismain']][$field['fieldname']] = $this->ci->post[$field['fieldname']];
	}

	/**
	 * 字段值
	 *
	 * @param	string	$name	字段名称
	 * @param	array	$data	数据库中的值
	 * @return  value
	 */
	public function get_value($name, $data) {
		return isset($data[$name]) ? $data[$name] : '';
	}

	/**
	 * 创建字段的sql语句
	 *
	 * @param	string	$name
	 * @param	array	$option
	 * @return  string
	 */
	public function create_sql($name, $option) {
		$fieldtype = $this->fieldtype === TRUE ? $this->fields : $this->fieldtype; // 可用字段类型
		$_fieldtype	= isset($option['fieldtype']) && isset($fieldtype[$option['fieldtype']]) ? $option['fieldtype'] : $this->defaulttype; // 字段类型
		$_length = isset($option['fieldlength']) && $option['fieldlength'] ? $option['fieldlength'] : $fieldtype[$_fieldtype]; // 字段长度
		return 'ALTER TABLE `{tablename}` ADD `'.$name.'` '.$_fieldtype.($_length ? '('.$_length.')' : '').' NULL DEFAULT '.$this->_default_value($_fieldtype);
	}

	/**
	 * 修改字段的sql语句
	 *
	 * @param	string	$name
	 * @param	array	$option
	 * @return  string
	 */
	public function alter_sql($name, $option) {
		$fieldtype = $this->fieldtype === TRUE ? $this->fields : $this->fieldtype; // 可用字段类型
		$_fieldtype	= isset($option['fieldtype']) && isset($fieldtype[$option['fieldtype']]) ? $option['fieldtype'] : $this->defaulttype; // 字段类型
		$_length = isset($option['fieldlength']) && $option['fieldlength'] ? $option['fieldlength'] : $fieldtype[$_fieldtype]; // 字段长度
		return 'ALTER TABLE `{tablename}` CHANGE `'.$name.'` `'.$name.'` '.$_fieldtype.($_length ? '('.$_length.')' : '').' NULL DEFAULT '.$this->_default_value($_fieldtype);
	}

	/**
	 * 删除字段的sql语句
	 *
	 * @param	string	$name
	 * @return  string
	 */
	public function drop_sql($name) {
		//ALTER TABLE `{tablename}` DROP `{field}`
		$sql = 'ALTER TABLE `{tablename}` DROP `'.$name.'`';
		return $sql;
	}

	/**
	 * 会员字段选择（用于字段默认值设定）
	 *
	 * @return  string
	 */
	public function member_field_select() {
		$str = '<select  class="form-control" onchange="$(\'#field_default_value\').val(\'{\'+this.value+\'}\')" name="_member_field"><option value=""> -- </option>';
		$str.= '<option value="username"> '.fc_lang('会员名称').' </option>';
		$str.= '<option value="email"> '.fc_lang('会员邮箱').' </option>';
		$str.= '<option value="groupid"> '.fc_lang('会员组ID').' </option>';
		$str.= '<option value="levelid"> '.fc_lang('会员等级ID').' </option>';
		$str.= '<option value="name"> '.fc_lang('姓名').' </option>';
		$str.= '<option value="phone"> '.fc_lang('电话').' </option>';
		if ($this->ci->MEMBER['field']) {
			foreach ($this->ci->MEMBER['field'] as $field => $t) {
				$str.= '<option value="'.$field.'"> '.$t['name'].' </option>';
			}
		}
		$str.= '</select>';
		return $str;
	}

	/**
	 * 获取会员默认值
	 *
	 * @param	string	$name
	 * @return  string
	 */
	public function get_default_value($value) {
		if (preg_match('/\{(\w+)\}/', $value, $match)) {
			if (IS_ADMIN) {
				return;
			}
			return isset($this->ci->member[$match[1]]) ? $this->ci->member[$match[1]] : '';
		}
		return $value;
	}

	// 数字默认值
	public function _default_value($type) {
		if (in_array($type, array('INT', 'TINYINT', 'SMALLINT', 'MEDIUMINT'))) {
			return '0';
		} else {
			return 'NULL';
		}
	}

	/**
	 * 字段类型选择
	 *
	 * @param	string	$name
	 * @param	string	$length
	 * @return  string
	 */
	public function field_type($name = NULL, $length = NULL) {
		if ($this->fieldtype === TRUE) {
			$select	= '<option value="">-</option>
				<option value="INT" '.($name == 'INT' ? 'selected' : '').'>INT</option>
				<option value="TINYINT" '.($name == 'TINYINT' ? 'selected' : '').'>TINYINT</option>
				<option value="SMALLINT" '.($name == 'SMALLINT' ? 'selected' : '').'>SMALLINT</option>
				<option value="MEDIUMINT" '.($name == 'MEDIUMINT' ? 'selected' : '').'>MEDIUMINT</option>
				<option value="">-</option>
				<option value="DECIMAL" '.($name == 'DECIMAL' ? 'selected' : '').'>DECIMAL</option>
				<option value="FLOAT" '.($name == 'FLOAT' ? 'selected' : '').'>FLOAT</option>
				<option value="">-</option>
				<option value="CHAR" '.($name == 'CHAR' ? 'selected' : '').'>CHAR</option>
				<option value="VARCHAR" '.($name == 'VARCHAR' ? 'selected' : '').'>VARCHAR</option>
				<option value="TEXT" '.($name == 'TEXT' ? 'selected' : '').'>TEXT</option>
				<option value="MEDIUMTEXT" '.($name == 'MEDIUMTEXT' ? 'selected' : '').'>MEDIUMTEXT</option>';
		} elseif (count($this->fieldtype) > 1) {
			$select	= '<option value="">-</option>';
			foreach ($this->fieldtype as $t) {
				$select	= "<option value=\"{$t}\" ".($name == $t ? "selected" : "").">{$t}</option>";
			}
		} else {
			return NULL;
		}

		$str = '
		<div class="form-group">
			<label class="col-md-2 control-label">'.fc_lang('类型').'： </label>
			<div class="col-md-9">
				<label><select class="form-control" name="data[setting][option][fieldtype]" onChange="setlength()" id="type">
					'.$select.'
				</select></label>
				<span class="help-block">'.fc_lang('根据你的实际情况选择字段类型，如果你不懂MySQL数据库知识就不要填写此项').'</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-2 control-label">'.fc_lang('长度/值').'： </label>
			<div class="col-md-9">
				<label><input type="text" class="form-control" size="10" value="'.$length.'" name="data[setting][option][fieldlength]"></label>
				<span class="help-block">'.fc_lang('如果你不懂MySQL数据库知识就不要填写此项').'</span>
			</div>
		</div>';

		return $str;
	}

	/**
	 * 表单输入格式
	 *
	 * @param	string	$name	字段名称
	 * @param	string	$text	字段别名
	 * @param	string	$value	表单输入内容
	 * @return  string
	 */
	public function input_format($name, $text, $value) {

		if ($this->remove_div) {
			return $value;
		}
		return str_replace(array('{name}', '{text}', '{value}'), array($name, $text, $value), self::$format);
	}

	/**
	 * 上传路径
	 *
	 * @param	string	$path	路径字串
	 * @return  string
	 */
	public function get_upload_path($path) {

		if (!$path) {
			return '';
		}

		$path = str_replace(
			array('{siteid}', '{module}', '{y}', '{m}', '{d}'),
			array(SITE_ID, APP_DIR, date('Y', SYS_TIME), date('m', SYS_TIME), date('d', SYS_TIME)),
			$path
		);

		$path = str_replace('//', '/', $path);
		$path = trim($path, '/');

		return $path;
	}
}