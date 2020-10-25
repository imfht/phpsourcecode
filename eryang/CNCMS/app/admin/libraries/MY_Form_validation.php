<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * 扩展 CI Form Validation Class
 *
 * @package CodeIgniter
 * @subpackage Libraries
 * @category Validation
 * @author 　二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class MY_Form_validation extends CI_Form_validation {
	
	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct ();
	}
	// --------------------------------------------------------------------
	
	/**
	 * Set Rules
	 *
	 * This function takes an array of field names and validation
	 * rules as input, validates the info, and stores it
	 *
	 * @access public
	 * @param
	 *        	mixed
	 * @param
	 *        	string
	 * @return void
	 */
	public function set_rules($field, $label = '', $rules = '') {
		// No reason to set rules if we have no POST data
		if (count ( $_POST ) == 0) {
			return $this;
		}
		
		// If an array was passed via the first parameter instead of indidual string
		// values we cycle through it and recursively call this function.
		if (is_array ( $field )) {
			foreach ( $field as $row ) {
				// Houston, we have a problem...
				if (! isset ( $row ['field'] ) or ! isset ( $row ['rules'] )) {
					continue;
				}
				
				// If the field label wasn't passed we use the field name
				$label = (! isset ( $row ['label'] )) ? $row ['field'] : $row ['label'];
				
				// Here we go!
				$this->set_rules ( $row ['field'], $label, $row ['rules'] );
			}
			return $this;
		}
		
		// No fields? Nothing to do...
		if (! is_string ( $field ) or ! is_string ( $rules ) or $field == '') {
			return $this;
		}
		
		// If the field label wasn't passed we use the field name
		$label = ($label == '') ? $field : $label;
		
		// Is the field name an array? We test for the existence of a bracket "[" in
		// the field name to determine this. If it is an array, we break it apart
		// into its components so that we can fetch the corresponding POST data later
		if (strpos ( $field, '[' ) !== FALSE and preg_match_all ( '/\[(.*?)\]/', $field, $matches )) {
			// Note: Due to a bug in current() that affects some versions
			// of PHP we can not pass function call directly into it
			$indexes = array ();
			
			if (stripos ( $field, '[]' ) !== FALSE) {
				$x = explode ( '[', $field );
				$indexes [] = current ( $x );
				
				for($i = 0; $i < count ( $matches ['0'] ); $i ++) {
					if ($matches ['1'] [$i] != '') {
						$indexes [] = $matches ['1'] [$i];
					}
				}
			} else {
				$indexes [] = str_replace ( '[', '.', rtrim ( $field, ']' ) );
			}
			
			$is_array = TRUE;
		} else {
			$indexes = array ();
			$is_array = FALSE;
		}
		
		// Build our master array
		$this->_field_data [$field] = array (
				'field' => $field,
				'label' => $label,
				'rules' => $rules,
				'is_array' => $is_array,
				'keys' => $indexes,
				'postdata' => NULL,
				'error' => '' 
		);
		
		return $this;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Traverse a multidimensional $_POST array index until the data is found
	 *
	 * @access private
	 * @param
	 *        	array
	 * @param
	 *        	array
	 * @param
	 *        	integer
	 * @return mixed
	 */
	protected function _reduce_array($array, $keys, $i = 0) {
		if (is_array ( $array )) {
			if (isset ( $keys [$i] )) {
				if (stripos ( $keys [$i], '.' )) {
					list ( $parent, $children ) = explode ( '.', $keys [$i] );
					$array = (! empty ( $array [$parent] [$children] )) ? $array [$parent] [$children] : NULL;
				} elseif (isset ( $array [$keys [$i]] )) {
					foreach ( $array [$keys [$i]] as $k => $v ) {
						$array [$keys [$i]] [$k] = (! empty ( $v )) ? $v : NULL;
					}
					
					$array = $this->_reduce_array ( $array [$keys [$i]], $keys, ($i + 1) );
				} else {
					return NULL;
				}
			} else {
				return $array;
			}
		}
		
		return $array;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * 只能包含英文字母、数字、下划线、斜线、破折号
	 *
	 * @access public
	 * @param
	 *        	string
	 * @return bool
	 */
	function alpha_dash_bias($str) {
		return (! preg_match ( "/^([-a-z0-9_\/\\\-])+$/i", $str )) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * 只能包含正整数
	 *
	 * @access public
	 * @param
	 *        	string
	 * @return bool
	 */
	function number_positive_integer($str) {
		return (! preg_match ( "/^[0-9]*[1-9][0-9]*$/i", $str )) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * 只能包含字母、数字、下划线的文件名
	 *
	 * @access public
	 * @param
	 *        	string
	 * @return bool
	 */
	function alpha_dash_bias_filename($str) {
		return (! preg_match ( "/^([-a-z0-9_\.-])+$/i", $str )) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * 只能包含英文字母、数字、下划线、斜线
	 *
	 * @access public
	 * @param
	 *        	string
	 * @return bool
	 */
	function alpha_dash_bias_url($str) {
		return (! preg_match ( "/^([-a-z0-9_\/])+$/i", $str )) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * 只能包含英文字母、数字、下划线、破折号
	 *
	 * @access public
	 * @param
	 *        	string
	 * @return bool
	 */
	function alpha_dash_bias_icon($str) {
		return (! preg_match ( "/^([-a-z0-9_-])+$/i", $str )) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * 只能包含中文、英文字母、数字、下划线、破折号
	 *
	 * @access public
	 * @param
	 *        	string
	 * @return bool /^([-a-z0-9_-][\u4e00-\u9fa5])+$/
	 *         /^([-a-z0-9_-])|([\x80-\xff])+$/i
	 *        
	 */
	function alpha_chinese_dash_bias($str) {
		return (! preg_match ( "/^[\x80-\xff-a-z0-9_-]+$/i", $str )) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	/**
	 * Max Length checkbox
	 *
	 * @access public
	 * @param
	 *        	string
	 * @param
	 *        	value
	 * @return bool
	 */
	public function max_length_checkbox($str, $val) {
		if (preg_match ( "/[^0-9]/", $val )) {
			return FALSE;
		}
		return (sizeof ( $str ) > $val) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * 检查手机号码
	 *
	 * @access public
	 * @param
	 *        	string
	 * @param
	 *        	value
	 * @return bool
	 */
	public function numeric_phone($str) {
		return (! preg_match ( "/^(((13[0-9]{1})|(15[0-9]{1}))+\d{8})$/i", $str )) ? FALSE : TRUE;
	}
	
    // --------------------------------------------------------------------
}

// --------------------------------------------------------------------

/* End of file MY_Form_validation.php */
/* Location: ./app/admin/libraries/MY_Form_validation.php */
