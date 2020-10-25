<?php
/**
* @package phpBB-WAP
* @copyright (c) phpBB Group
* @Оптимизация под WAP: Гутник Игорь ( чел ).
* @简体中文：中文phpBB-WAP团队
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

/**
* 模版类
* 用于处理tpl文件
**/
class Template
{
	var $_tpldata 			= array();
	var $files 				= array();
	var $root 				= '';
	var $compiled_code 		= array();
	var $uncompiled_code 	= array();
	
	/**
	* 	初始化根目录
	*	@参数 字符串 $root 模版的存放路径
	**/
	function __construct($root)
	{
		$this->root = $root;
	}

	/**
	*	方法：reset
	*	重置模版路径
	*	@参数 字符串 $root 模版的存放路径	
	*/
	function reset_root($root)
	{
		$this->root = $root;
	}

	/**
	* 	把 $_tpldata 重新初始化
	**/
	function destroy()
	{
		$this->_tpldata = array();
	}

	/**
	*	方法：set_filenames()
	*	选择 tpl 文件
	* 	@参数 数组 $filename_array handle和文件名
	**/
	function set_filenames($filename_array)
	{
		if (!is_array($filename_array))
		{
			return;
		}

		// 把指针指向第一个模版文件
		reset($filename_array);
		
		foreach($filename_array as $handle => $filename)
		{
			$this->files[$handle] = $this->make_filename($filename);
		}
	}

	/**
	*	方法：pparse()
	* 	解析模版内容
	*	@参数 $handle 模版句柄
	**/
	function pparse($handle)
	{
		// 首先加载代表的模版，无法加载则进行报错
		if (!$this->loadfile($handle))
		{
			trigger_error("Template->pparse(): 无法加载模版文件句柄 $handle", E_USER_WARNING);
		}

		// 检测模版是否编译，如果没有编译则进行编译
		if (!isset($this->compiled_code[$handle]) || empty($this->compiled_code[$handle])) 
		{
			$this->compiled_code[$handle] = $this->compile($this->uncompiled_code[$handle]);
		}
		
		// 最后使用 eval 进行解析
		eval($this->compiled_code[$handle]);
	}

	/**
	* 	方法：assign_var_from_handle
	*	将待处理内容锁定在模版的标签中
	*	@参数 字符串 $varname 标签名称
	*	@参数 $handle 待处理内容
	*/
	function assign_var_from_handle($varname, $handle)
	{
		if (!$this->loadfile($handle))
		{
			trigger_error("Template->assign_var_from_handle(): 无法加载模版文件句柄 $handle", E_USER_WARNING);
		}
		$_str = '';
		$code = $this->compile($this->uncompiled_code[$handle], true, '_str');
		eval($code);
		$this->assign_var($varname, $_str);

		return true;
	}

	/**
	*	方法：assign_block_vars
	* 	指定区块内的文本值
	* 	@参数 字符串 $blockname 区块名称
	* 	@参数 数组 $vararray 标签和值
	*/
	function assign_block_vars($blockname, $vararray)
	{
		if (strstr($blockname, '.'))
		{
			$blocks = explode('.', $blockname);
			$blockcount = count($blocks) - 1;
			$str = '$this->_tpldata';
			for ($i = 0; $i < $blockcount; $i++)
			{
				$str .= '[\'' . $blocks[$i] . '.\']';
				eval('$lastiteration = count(' . $str . ') - 1;');
				$str .= '[' . $lastiteration . ']';
			}

			$str .= '[\'' . $blocks[$blockcount] . '.\'][] = $vararray;';

			eval($str);
		}
		else
		{

			$this->_tpldata[$blockname . '.'][] = $vararray;
		}
	}

	/*
	*	方法：assign_vars()
	* 	指定若干个标签中的值 
	* 	@参数 数组 $vararray 标签和值
	*/
	function assign_vars($vararray)
	{
		reset($vararray);
		foreach($vararray as $key => $val)
		{
			$this->_tpldata['.'][0][$key] = $val;
		}
	}
	
	/*
	*	方法：assign_var()
	* 	指定单个标签中的值 
	* 	@参数 字符串 $varname 标签
	* 	@参数 字符串 $varval 标签的值
	*/
	function assign_var($varname, $varval)
	{
		$this->_tpldata['.'][0][$varname] = $varval;
	}

	/**
	* 	生成标准的文件名
	* 	如果模版文件不存在则使用 default 下的模版文件，当 default 都无此文件，则生成一个报错信息
	* 	@参数 $filename 模版文件名
	*/
	function make_filename($filename)
	{
		//if (substr($filename, 0, 1) != '/')
		//{
       	//	$filename = ($rp_filename = ) ? $rp_filename : $filename;
		//}

		$tpl_filename = phpbb_realpath($this->root . $filename);

		if (!file_exists($tpl_filename))
		{
			trigger_error("Template->make_filename(): 模版文件 $filename 不存在", E_USER_WARNING);
		}

		return $tpl_filename;
	}

	/*
	* 	生成未编译的代码
	*/
	function loadfile($handle)
	{
		if (isset($this->uncompiled_code[$handle]) && !empty($this->uncompiled_code[$handle]))
		{
			return true;
		}
		
		if (!isset($this->files[$handle]))
		{
			trigger_error("Template->loadfile(): 没有指定文件句柄 $handle", E_USER_WARNING);
		}

		$filename = $this->files[$handle];

		$str = implode('', @file($filename));
		
		if (empty($str))
		{
			trigger_error("Template->loadfile(): 文件 $filename 句柄 $handle 为空", E_USER_WARNING);
		}

		$this->uncompiled_code[$handle] = $str;

		return true;
	}

	/**
	* 	执行标签与真实值的替换 
	*/
	function compile($code, $do_not_echo = false, $retvar = '')
	{
		$code = str_replace('\\', '\\\\', $code);
		$code = str_replace('\'', '\\\'', $code);

		$varrefs = array();
		preg_match_all('#\{(([a-z0-9\-_]+?\.)+?)([a-z0-9\-_]+?)\}#is', $code, $varrefs);
		$varcount = count($varrefs[1]);
		for ($i = 0; $i < $varcount; $i++)
		{
			$namespace = $varrefs[1][$i];
			$varname = $varrefs[3][$i];
			$new = $this->generate_block_varref($namespace, $varname);

			$code = str_replace($varrefs[0][$i], $new, $code);
		}

		$code = preg_replace('#\{([a-z0-9\-_]*?)\}#is', '\' . ( ( isset($this->_tpldata[\'.\'][0][\'\1\']) ) ? $this->_tpldata[\'.\'][0][\'\1\'] : \'\' ) . \'', $code);

		$code_lines = explode("\n", $code);

		$block_nesting_level = 0;
		$block_names = array();
		$block_names[0] = ".";

		$line_count = count($code_lines);
		for ($i = 0; $i < $line_count; $i++)
		{
			$code_lines[$i] = chop($code_lines[$i]);
			if (preg_match('#<!-- BEGIN (.*?) -->#', $code_lines[$i], $m))
			{

				$n[0] = $m[0];
				$n[1] = $m[1];

				if ( preg_match('#<!-- END (.*?) -->#', $code_lines[$i], $n) )
				{
					$block_nesting_level++;
					$block_names[$block_nesting_level] = $m[1];
					if ($block_nesting_level < 2)
					{
						$code_lines[$i] = '$_' . $n[1] . '_count = ( isset($this->_tpldata[\'' . $n[1] . '.\']) ) ?  count($this->_tpldata[\'' . $n[1] . '.\']) : 0;';
						$code_lines[$i] .= "\n" . 'for ($_' . $n[1] . '_i = 0; $_' . $n[1] . '_i < $_' . $n[1] . '_count; $_' . $n[1] . '_i++)';
						$code_lines[$i] .= "\n" . '{';
					}
					else
					{
						$namespace = implode('.', $block_names);
						$namespace = substr($namespace, 2);
						$varref = $this->generate_block_data_ref($namespace, false);
						$code_lines[$i] = '$_' . $n[1] . '_count = ( isset(' . $varref . ') ) ? count(' . $varref . ') : 0;';
						$code_lines[$i] .= "\n" . 'for ($_' . $n[1] . '_i = 0; $_' . $n[1] . '_i < $_' . $n[1] . '_count; $_' . $n[1] . '_i++)';
						$code_lines[$i] .= "\n" . '{';
					}
					unset($block_names[$block_nesting_level]);
					$block_nesting_level--;
					$code_lines[$i] .= '} // END ' . $n[1];
					$m[0] = $n[0];
					$m[1] = $n[1];
				}
				else
				{
					$block_nesting_level++;
					$block_names[$block_nesting_level] = $m[1];
					if ($block_nesting_level < 2)
					{
						$code_lines[$i] = '$_' . $m[1] . '_count = ( isset($this->_tpldata[\'' . $m[1] . '.\']) ) ? count($this->_tpldata[\'' . $m[1] . '.\']) : 0;';
						$code_lines[$i] .= "\n" . 'for ($_' . $m[1] . '_i = 0; $_' . $m[1] . '_i < $_' . $m[1] . '_count; $_' . $m[1] . '_i++)';
						$code_lines[$i] .= "\n" . '{';
					}
					else
					{
						$namespace = implode('.', $block_names);
						$namespace = substr($namespace, 2);
						$varref = $this->generate_block_data_ref($namespace, false);
						$code_lines[$i] = '$_' . $m[1] . '_count = ( isset(' . $varref . ') ) ? count(' . $varref . ') : 0;';
						$code_lines[$i] .= "\n" . 'for ($_' . $m[1] . '_i = 0; $_' . $m[1] . '_i < $_' . $m[1] . '_count; $_' . $m[1] . '_i++)';
						$code_lines[$i] .= "\n" . '{';
					}
				}
			}
			else if (preg_match('#<!-- END (.*?) -->#', $code_lines[$i], $m))
			{
				unset($block_names[$block_nesting_level]);
				$block_nesting_level--;
				$code_lines[$i] = '} // END ' . $m[1];
			}
			else
			{
				if (!$do_not_echo)
				{
					$code_lines[$i] = 'echo \'' . $code_lines[$i] . '\' . "\\n";';
					//$code_lines[$i] = 'echo \'' . $code_lines[$i] . '\';';
				}
				else
				{
					$code_lines[$i] = '$' . $retvar . '.= \'' . $code_lines[$i] . '\' . "\\n";'; 
					//$code_lines[$i] = '$' . $retvar . '.= \'' . $code_lines[$i] . '\';'; 
				}
			}
		}

		$code = implode("\n", $code_lines);
		return $code;

	}

	/**
	* 	生成块变量引用，编译时调用
	*/
	function generate_block_varref($namespace, $varname)
	{
		$namespace = substr($namespace, 0, strlen($namespace) - 1);
		$varref = $this->generate_block_data_ref($namespace, true);
		$varref .= '[\'' . $varname . '\']';
		$varref = '\' . ( ( isset(' . $varref . ') ) ? ' . $varref . ' : \'\' ) . \'';

		return $varref;
	}
	
	/*
	* 生成块数据引用，编译时调用
	*/
	function generate_block_data_ref($blockname, $include_last_iterator)
	{
		$blocks = explode(".", $blockname);
		$blockcount = count($blocks) - 1;
		$varref = '$this->_tpldata';
		for ($i = 0; $i < $blockcount; $i++)
		{
			$varref .= '[\'' . $blocks[$i] . '.\'][$_' . $blocks[$i] . '_i]';
		}
		$varref .= '[\'' . $blocks[$blockcount] . '.\']';
		if ($include_last_iterator)
		{
			$varref .= '[$_' . $blocks[$blockcount] . '_i]';
		}

		return $varref;
	}

}

?>