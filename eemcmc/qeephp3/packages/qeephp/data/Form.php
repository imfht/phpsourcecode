<?php namespace qeephp\data;

/**
 * Form 类
 *
 * 实现表单的验证
 *
 * todo: 创建表单及 csrf 的实现
 */
class Form
{
	/*
     * 表单方法类型
     */
    const POST    = 'post';
    const GET     = 'get';
    const PUT     = 'put';
    const DELETE  = 'delete';

    /**
     * 表单编码类型
     */
    const ENCTYPE_URLENCODED = 'application/x-www-form-urlencoded';
    const ENCTYPE_MULTIPART  = 'multipart/form-data';

    /**
     * 表单属性
     *
     * @var array
     */
    private $attrs;

    /**
     * 验证规则
     *
     * @var array
     */
    protected $_validations = array();

    /**
     * 表单的数据
     *
     * @var string
     */
    protected $_form_datas = array();

    /**
     * 验证失败的信息
     *
     * @var string
     */
    protected $_error_msgs = array();

    /**
     * Security 实例对象
     *
     * @var string
     */
    protected $_security_instance = null;

    /**
     * 构造函数
     *
     * @param string $id 表单 ID
     * @param string $action 表单提交的目的地 URL
     * @param string $method 表单提交方法
     * @param arary $attrs 附加的属性
     */
    function __construct($id = 'form1', $method = self::POST, $action = '', array $attrs = null)
    {
    	if(isset($attrs['enctype']))
    	{
	    	$enctype = $attrs['enctype'];
	    	unset($attrs['enctype']);
    	}
    	else
    	{
	    	$enctype = self::ENCTYPE_URLENCODED;
    	}

        $this->attrs = array(
        	'id'		=> $id,
        	'action'	=> $action,
        	'method'	=> $method,
        	'enctype'	=> $enctype,
        	'others'	=> $attrs
        );
    }

    /**
	 * 表单的表码构建函数开始
	 *
     */
    function start()
    {
	    $html = '<form id="'.h($this->attrs['id'])
	    	  .'" name="'.h($this->attrs['id'])
	    	  .'" method="'.h($this->attrs['method'])
	    	  .'" action="'.h($this->attrs['action'])
	    	  .'" enctype="'.h($this->attrs['enctype']).'"';

	    if(! empty($this->attrs['others']))
	    {
	    	foreach($this->attrs['others'] AS $attr => $val)
	    	{
	    		$html .= $attr.'="'.h($val).'"';
	    	}
	    }
	    $html .= '>';

	    return $html;
    }

    /**
	 * 表单的表码构建函数结束
	 *
	 * todo: csrf 的实现的隐藏 input
     */
    function end()
    {
	    $html = '</form>';

	    return $html;
    }

    /**
	 * 设置表单的验证规则
	 *
	 * @param $validations, 一组表单验证规则
	 *
	 * 格式, 如下如下:
	 *	array(
	 *		'nickname' => array
     *       (
     *           array('not_empty', '昵称不能为空'),
     *           array('min_length', 3, '昵称不能少于 3 个字符'),
     *       ),
     *
     *       'email' => array
     *       (
     *           array('skip_on_failed'),
     *           array('is_email', '请输入正确的邮箱'),
     *       ),
	 *	);
     */
    function add_validations($validations)
    {
    	if(empty($this->_validations))
    	{
	    	$this->_validations = $validations;
    	}
	    else
	    {
		    foreach($validations AS $key => $one)
		    {
			    $this->_validations[$key] = $one;
		    }
	    }
    }

    /**
	 * 给指定的 表单控件 添加验证规则
	 *
	 * 格式一:
	 * array(
	 *		'nickname' => array
     *       (
     *           array('not_empty', '昵称不能为空'),
     *           array('min_length', 3, '昵称不能少于 3 个字符'),
     *       )
     * );
     *
	 * 格式二:
	 * array
     * (
     *     array('not_empty', '昵称不能为空'),
     *     array('min_length', 3, '昵称不能少于 3 个字符'),
     *  );
	 *
	 * @param string $field 表单控件名称
	 * @param array $validations 规则
     */
    function add_field_validations($field, $validations)
    {
		$tmp_validations = (array_key_exists($field, $validations))
						 ? $validations[$field]
						 : $validations;
		foreach($tmp_validations AS $one)
    	{
	    	$this->_validations[$field][] = $one;
	    }
    }

    /**
	 * 执行表单并进行验证, 返回验证结果, 只有一有条规则没通过则返回 false
	 *
	 * 1. 先执行过滤
	 *
	 * 2. 其次执行验证规则
	 *
	 * @return bool
     */
    function run()
    {
	    if( ! is_post()) return; // 如果不是提交动作

	    foreach($_POST AS $field => $data)
	    {
		    $this->_form_datas[$field] = $data;

		    if(isset($this->_validations[$field]))
		    {
			    $failed = null;
				$is_valid = (bool)Validator::validateBatch(post($field), $this->_validations[$field], Validator::CHECK_ALL, $failed);
				if( ! $is_valid)
				{
					foreach ($failed as $v)
	                {
	                    $this->_error_msgs[$field][] = array_pop($v);
	                }
				}
		    }
	    }

	    return empty($this->_error_msgs);
    }

    /**
	 * 获取表单验证成功后的值, 值已经被过滤
	 *
	 * @param $field 表单里的控件名称
	 * @param $xss_clean 是否进行 xss 攻击的清理
	 * @param $is_image 是否是图片
	 *
	 * @return string
     */
    function input($field, $xss_clean = false, $is_image = false)
    {
	    $this->_form_datas[$field] = $xss_clean
	    						   ? Security::xss_clean($this->_form_datas[$field], $is_image)
	    						   : $this->_form_datas[$field];

	    return $this->_form_datas[$field];
    }

    /**
	 * 获取 表单控件 对应的错误信息
	 *
	 * @param $field 表单里的控件名称
	 * @param $link 多条错误信息的连接字符
     */
    function get_error($field, $link = ', ')
    {
    	$result = array();
	    if(isset($this->_error_msgs[$field]))
	    {
		    foreach($this->_error_msgs[$field] AS $msg)
		    {
			    $result[] = $msg;
		    }
	    }
	    return implode($link, $result);
    }

    /**
	 * 对 input 值的设置, 包括提交错误后返回的值
	 *
	 * @param $field 表单里的控件名称
	 * @param $default 默认值, 如果是提交过的表单, 则从提交的值里读取
     */
    function set_value($field = '', $default = '')
    {
    	if ( !isset($this->_form_datas[$field]) )
		{
			$this->_form_datas[$field] = $default;
		}
    }

}