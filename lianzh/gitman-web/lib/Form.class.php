<?php
require_once __DIR__ . '/Verify.class.php';
require_once __DIR__ . '/Security.class.php';

/**
 * Form 类
 *
 * 实现表单的验证
 */
class Form
{
    /*
     * 表单方法类型
     */
    const POST = 'post';
    const GET = 'get';
    const PUT = 'put';
    const DELETE = 'delete';

    /**
     * 表单编码类型
     */
    const ENCTYPE_URLENCODED = 'application/x-www-form-urlencoded';
    const ENCTYPE_MULTIPART = 'multipart/form-data';

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
     * 构造函数
     *
     * @param string $id 表单 ID
     * @param string $action 表单提交的目的地 URL
     * @param string $method 表单提交方法
     * @param arary $attrs 附加的属性
     */
    function __construct($id = 'form1', $method = self::POST, $action = '', array $attrs = null)
    {
        if (isset($attrs['enctype'])) {
            $enctype = $attrs['enctype'];
            unset($attrs['enctype']);
        } else {
            $enctype = self::ENCTYPE_URLENCODED;
        }

        $this->attrs = array(
            'id' => $id,
            'action' => $action,
            'method' => $method,
            'enctype' => $enctype,
            'others' => $attrs
        );
    }

    /**
     * 表单的表码构建函数开始
     *
     */
    public function start()
    {
        $html = '<form id="' . htmlspecialchars($this->attrs['id'])
            . '" name="' . htmlspecialchars($this->attrs['id'])
            . '" method="' . htmlspecialchars($this->attrs['method'])
            . '" action="' . htmlspecialchars($this->attrs['action'])
            . '" enctype="' . htmlspecialchars($this->attrs['enctype']) . '"';

        if (!empty($this->attrs['others'])) {
            foreach ($this->attrs['others'] AS $attr => $val) {
                $html .= $attr . '="' . htmlspecialchars($val) . '"';
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
    public function end()
    {
        $html = '</form>';

        return $html;
    }

    /**
     * 设置表单的验证规则
     *
     * @param $validations , 一组表单验证规则
     *
     * 格式, 如下如下:
     *    array(
     *        'nickname' => array
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
     *    );
     */
    public function add_validations($validations)
    {
        if (empty($this->_validations)) {
            $this->_validations = $validations;
        } else {
            foreach ($validations AS $key => $one) {
                $this->_validations[$key] = $one;
            }
        }
    }

    /**
     * 给指定的 表单控件 添加验证规则
     *
     * 格式一:
     * array(
     *        'nickname' => array
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
    public function add_field_validations($field, $validations)
    {
        $tmp_validations = (array_key_exists($field, $validations))
            ? $validations[$field]
            : $validations;
        foreach ($tmp_validations AS $one) {
            $this->_validations[$field][] = $one;
        }
    }

    /**
     * 执行表单验证, 返回验证结果, 只有一有条规则没通过则返回 false
     *
     * @return bool
     */
    public function validate()
    {
        return self::assert_arr($this->_form_datas, $this->_validations, $this->_error_msgs);
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
    public function input($field, $xss_clean = false, $is_image = false)
    {
        if (!isset($this->_form_datas[$field])) return '';

        $this->_form_datas[$field] = $xss_clean
            ? Security::xss_clean($this->_form_datas[$field], $is_image)
            : $this->_form_datas[$field];

        return $this->_form_datas[$field];
    }

    public function get_errors()
    {
        return $this->_error_msgs;
    }

    /**
     * 对 input 值的设置, 包括提交错误后返回的值
     *
     * @param $field 表单里的控件名称
     * @param $val 默认值, 如果是提交过的表单, 则从提交的值里读取
     */
    public function set_field_value($field = '', $val = '')
    {
        $this->_form_datas[$field] = $val;
    }

    public function set_form_datas(array $data)
    {
        $this->_form_datas = $data;
    }

    public static function request($name, $default = null)
    {
        return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
    }

    public static function get($name, $default = null)
    {
        return isset($_GET[$name]) ? $_GET[$name] : $default;
    }

    public static function post($name, $default = null)
    {
        return isset($_POST[$name]) ? $_POST[$name] : $default;
    }

    public static function cookie($name, $default = null)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
    }

    public static function session($name, $default = null)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
    }

    public static function server($name, $default = null)
    {
        return isset($_SERVER[$name]) ? $_SERVER[$name] : $default;
    }

    public static function env($name, $default = null)
    {
        return isset($_ENV[$name]) ? $_ENV[$name] : $default;
    }

    public static function is_post()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    public static function is_ajax()
    {
        return strtolower(self::get_http_header('X_REQUESTED_WITH')) == 'xmlhttprequest';
    }

    public static function is_flash()
    {
        return strtolower(self::get_http_header('USER_AGENT')) == 'shockwave flash';
    }

    public static function get_http_header($header)
    {
        $name = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
        return self::server($name, '');
    }

    public static function xss_clean($val, $is_image = false)
    {
        return Security::xss_clean($val, $is_image);
    }

    /**
     * 用一组规则断言值
     *
     * assert_val() 方法对一个值应用一组验证规则，并返回最终的结果.
     * 如果规则不是合法的数组,则当成无须验证而返回true
     *
     * 这一组验证规则中只要有一个验证失败，都会返回 false.
     * 只有当所有规则都通过时,才会返回 true.
     *
     * 用法：
     * assert_val(
     *      $value,
     *      array(
     *         array('is_int', '必须是整数'),
     *         array('between',18,50,false,'必须在[18,50]的之间'),
     *      ),
     *      $failed
     * );
     *
     * $rules 参数必须是一个数组，包含多个规则，及验证规则需要的参数。
     * 每个规则及参数都是一个单独的数组。
     *
     * 如果提供了 $failed 参数，则验证失败的错误信息的会存储在 $failed 参数中.
     *
     * 如果 $withFailedRule 设置为 true,则 $failed 的值为一个数组:
     *      array('fr'=>$failedRule,'ft'=>$failedTip)
     *
     *
     * @param mixed $value 要验证的值
     * @param array $rules 由多个验证规则及参数组成的数组
     * @param mixed $failed 保存验证失败的提示信息
     * @param boolean $withFailedRule 是否保存验证失败的规则信息
     *
     * @return boolean 验证结果
     */
    public static function assert_val($value, array $rules = null, &$failed = null, $withFailedRule = false)
    {
        if (empty($rules)) return true;

        $verify = Verify::getInstance();

        $errors = array(); // $fld -> errorInfo 

        foreach ($rules as $index => $rule) {
            // $rule => array(rule, validationParams, errorInfo)
            // 无效的规则,则视为true
            if (empty($rule[0])) {
                unset($rules[$index]);
                continue;
            }
            if (is_string($rule[0])) {
                if (method_exists($verify, $rule[0]))
                    $rule[0] = array(& $verify, $rule[0]);
            }

            $callbackToString = self::str_callback($rule[0]);
            if (empty($callbackToString)) {
                unset($rules[$index]);
                continue;
            }

            $rules[$index][0] = &$rule[0]; // 重新补全校验规则

            $errors[$callbackToString] = array_pop($rules[$index]); // 弹出错误信息
        }

        $rules_failed = null;

        // 校验成功 直接返回true
        if ($verify->validateBatch($value, $rules, false, $rules_failed)) return true;

        if ($withFailedRule) {
            $failedRule = self::str_callback(array_pop($rules_failed));
            $failed = array('fr' => $failedRule, 'ft' => $errors[$failedRule]);
        } else
            $failed = $errors[self::str_callback(array_pop($rules_failed))];

        return false;
    }

    /**
     * 用多组规则断言关联数组的值
     *
     * 每个关联数组的元素均对应一组验证规则,如果这组验证规则不存在,则当成此元素无须验证
     * 只有当关联数组的所有元素都通过其对应的组验证规则时，assert_arr() 方法才会返回 true。
     *
     *
     * 用法：
     * assert_arr(
     *      array(
     *          'name' => 'iamsese' ,
     *          'age' => 26
     *      ),
     *      array(
     *         'name' => array(
     *              array('not_empty', '用户名不能为空'),
     *              array('min_length', 5, '用户名不能少于 5 个字符'),
     *              array('max_length', 20, '用户名不能超过 20 个字符'),
     *         ) ,
     *
     *         'age' => array(
     *              array('not_null', '年龄不能为空'),
     *              array('is_int', '年龄必须是整数'),
     *              array('between',18,50,true,'年龄必须是[18,50]的整数'),
     *         ) ,
     *      ),
     *      $flds_failed
     * );
     *
     * * 如果提供了 $flds_failed 参数，则验证失败的规则会存储在 $failed 参数中
     *
     * @param array $row 要验证的关联数组
     * @param array $flds_rules 关联数组的键对应的多组规则
     * @param mixed $flds_failed 保存验证失败的 字段->错误信息 的关联数组
     * @param boolean $withFailedRule 是否保存验证失败的规则信息
     *
     * @return boolean 验证结果
     */
    public static function assert_arr(array $row, array $flds_rules = null, &$flds_failed = null, $withFailedRule = false)
    {
        if (empty($flds_rules)) return true;

        $verify = Verify::getInstance();

        if (empty($row)) $row = array();

        $flds_failed = array();

        foreach ($flds_rules as $fld => $rules) {
            if (empty($rules)) continue;

            if (!isset($row[$fld])) $row[$fld] = null;
            $value = $row[$fld];
            $errors = array(); // $fld -> errorInfo 

            foreach ($rules as $index => $rule) {
                // $rule => array(validation, validationParams, errorInfo)
                // 无效的规则,则视为true
                if (empty($rule[0])) {
                    unset($rules[$index]);
                    continue;
                }
                if (is_string($rule[0])) {
                    if (method_exists($verify, $rule[0]))
                        $rule[0] = array(& $verify, $rule[0]);
                }

                $callbackToString = self::str_callback($rule[0]);
                if (empty($callbackToString)) {
                    unset($rules[$index]);
                    continue;
                }

                $rules[$index][0] = &$rule[0]; // 重新补全校验规则

                $errors[$callbackToString] = array_pop($rules[$index]); // 弹出错误信息
            }

            $rules_failed = null;

            // 校验成功 继续下一步
            if ($verify->validateBatch($value, $rules, false, $rules_failed)) continue;

            if ($withFailedRule) {
                $failedRule = self::str_callback(array_pop($rules_failed));
                $flds_failed[$fld] = array('fr' => $failedRule, 'ft' => $errors[$failedRule]);
            } else
                $flds_failed[$fld] = $errors[self::str_callback(array_pop($rules_failed))];
        }
        return empty($flds_failed);
    }

    /**
     * 将回调函数 名称转储成字符串样式
     *
     * @param mixed $callback
     * @return string
     */
    public static function str_callback($callback)
    {
        if (!is_callable($callback)) return null;

        // fun | Class::func
        if (is_string($callback)) return $callback;
        // & $obj , func
        else if (is_array($callback)) {
            return get_class(array_shift($callback)) . '::' . array_shift($callback);
        }
        return null;
    }

}