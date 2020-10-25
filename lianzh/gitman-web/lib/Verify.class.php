<?php

/**
 * 验证功能组件,支持批处理验证,支持外部扩展验证
 */
class Verify
{

    /**
     * @return Verify
     */
    static function getInstance()
    {
        static $inst = null;
        if (!$inst)
            $inst = new self();
        return $inst;
    }

    function not_null_string($value)
    {
        return !empty($value) && is_string($value);
    }

    function not_null_array($value)
    {
        return !empty($value) && is_array($value);
    }

    // 当有规则失败时，跳过余下的规则
    const SKIP_ON_FAILED = 'skip_on_failed';
    // 跳过其他规则
    const SKIP_OTHERS = 'skip_others';
    // 验证通过
    const PASSED = true;
    // 验证失败
    const FAILED = false;
    // 检查所有规则
    const CHECK_ALL = true;

    /**
     * 本地化变量
     * @var array
     */
    protected $_locale;

    private function __construct()
    {
        $this->_locale = localeconv();
    }

    /*
     * 用单个规则验证值
     * validate($value, 'max', 5)) <==> validateByArgs('max', array($value, 5));
     *
     * validate($value, 'between', 1, 5) <==> validateByArgs('between', array($value, 1,5));
     *
     * validate($value, 'custom_callback', $args) <==> validateByArgs('custom_callback', array($value, $args));
     */
    function validate($value, $validation)
    {
        $args = func_get_args();
        unset($args[1]);
        $result = $this->validateByArgs($validation, $args);
        return (bool)$result;
    }

    function validateByArgs($validation, array $args)
    {
        $method = null;
        if ($this->not_null_string($validation)) {
            if (method_exists($this, $validation))
                $method = array(& $this, $validation);
            elseif (strpos($validation, '::') && is_callable($validation))
                $method = explode('::', $validation);
            elseif (is_callable($validation))
                $method = $validation;
        } elseif ($this->not_null_array($validation) && is_callable($validation)) {
            $method = $validation;
        }
        return $method ? call_user_func_array($method, $args) : null;
    }

    /**
     * 用一组规则验证值
     *
     * validateBatch() 方法对一个值应用一组验证规则，并返回最终的结果。
     * 这一组验证规则中只要有一个验证失败，都会返回 false。
     * 只有当所有规则都通过时，validateBatch() 方法才会返回 true。
     *
     * 用法：
     * validateBatch($value, array(
     *         array('is_int'),
     *         array('between', 2, 6),
     * ));
     *
     * $validations 参数必须是一个数组，包含多个规则，及验证规则需要的参数。
     * 每个规则及参数都是一个单独的数组。
     *
     * 如果提供了 $failed 参数，则验证失败的规则会存储在 $failed 参数中：
     *
     * @param mixed $value 要验证的值
     * @param array $validations 由多个验证规则及参数组成的数组
     * @param boolean $check_all 是否检查所有规则
     * @param mixed $failed 保存验证失败的规则名
     *
     * @return boolean 验证结果
     */
    function validateBatch($value, array $validations, $check_all = false, &$failed = null)
    {
        $result = true;
        $failed = array();
        foreach ($validations as $validation) {
            $rule = $validation[0]; // eg. is_int
            $validation[0] = $value;
            $ret = $this->validateByArgs($rule, $validation);

            // 跳过余下的验证规则
            if ($ret === self::SKIP_OTHERS) {
                return $result;
            }

            if ($ret === self::SKIP_ON_FAILED) {
                $check_all = false;
                continue;
            }

            if ($ret) continue;

            $failed[] = $rule;
            $result = $result && $ret;

            if (!$result && !$check_all) return false;
        }
        return (bool)$result;
    }

    /**
     * 如果为空（空字符串或者 null），则跳过余下的验证
     */
    function skip_empty($value)
    {
        return (strlen($value) == 0) ? self::SKIP_OTHERS : true;
    }

    /**
     * 如果值为 NULL，则跳过余下的验证
     */
    function skip_null($value)
    {
        return (is_null($value)) ? self::SKIP_OTHERS : true;
    }

    /**
     * 如果接下来的验证规则出错，则跳过后续的验证
     */
    function skip_on_failed()
    {
        return self::SKIP_ON_FAILED;
    }


    /**
     * 使用正则表达式进行验证
     */
    function regex($value, $regxp)
    {
        return preg_match($regxp, $value) > 0;
    }

    /**
     * 等于指定值
     */
    function equal($value, $test)
    {
        return $value == $test && strlen($value) == strlen($test);
    }

    /**
     * 不等于指定值
     */
    function not_equal($value, $test)
    {
        return $value != $test || strlen($value) != strlen($test);
    }

    /**
     * 是否与指定值完全一致
     */
    function same($value, $test)
    {
        return $value === $test;
    }

    /**
     * 是否与指定值不完全一致
     */
    function not_same($value, $test)
    {
        return $value !== $test;
    }

    /**
     * 验证字符串长度
     */
    function strlen($value, $len)
    {
        return strlen($value) == (int)$len;
    }

    /**
     * 最小长度
     */
    function min_length($value, $len)
    {
        return strlen($value) >= $len;
    }

    /**
     * 最大长度
     */
    function max_length($value, $len)
    {
        return strlen($value) <= $len;
    }

    /**
     * 使用 mb_strlen 判断 字符串的最大长度
     */
    function max_length_mbchar($mbchar, $length, $charset = 'utf-8')
    {
        if (is_string($mbchar)) {
            return mb_strlen($mbchar, $charset) <= (int)$length;
        }
        return false;
    }

    /**
     * 使用 mb_strlen 判断 字符串的最小长度
     */
    function min_length_mbchar($mbchar, $length, $charset = 'utf-8')
    {
        if (is_string($mbchar)) {
            return mb_strlen($mbchar, $charset) >= (int)$length;
        }
        return false;
    }

    /**
     * 最小值
     */
    function min($value, $min)
    {
        return $value >= $min;
    }

    /**
     * 最大值
     */
    function max($value, $max)
    {
        return $value <= $max;
    }

    /**
     * 在两个值之间
     *
     * @param mixed $value
     * @param int|float $min
     * @param int|float $max
     * @param boolean $inclusive 是否包含 min/max 在内
     *
     * @return boolean
     */
    function between($value, $min, $max, $inclusive = true)
    {
        if ($inclusive) {
            return $value >= $min && $value <= $max;
        } else {
            return $value > $min && $value < $max;
        }
    }

    function strlen_between($value, $min, $max)
    {
        return $this->min_length($value, $min) && $this->max_length($value, $max);
    }

    /**
     * >指定值
     */
    function greater_than($value, $test)
    {
        return $value > $test;
    }

    /**
     * >=指定值
     */
    function greater_or_equal($value, $test)
    {
        return $value >= $test;
    }

    /**
     * <指定值
     */
    function less_than($value, $test)
    {
        return $value < $test;
    }

    /**
     * <=指定值
     */
    function less_or_equal($value, $test)
    {
        return $value <= $test;
    }

    /**
     * 不为 null
     */
    function not_null($value)
    {
        return !is_null($value);
    }

    /**
     * 不为空
     */
    function not_empty($value, $skipZeroString = false)
    {
        if ($skipZeroString && $value === '0') return true;
        return !empty($value);
    }

    /**
     * 是否是特定类型
     */
    function is_type($value, $type)
    {
        return gettype($value) == $type;
    }

    /**
     * 是否是字母加数字
     */
    function is_alnum($value)
    {
        return ctype_alnum($value);
    }

    /**
     * 是否是字母
     */
    function is_alpha($value)
    {
        return ctype_alpha($value);
    }

    /**
     * 是否是字母、数字加下划线
     */
    function is_alnumu($value)
    {
        return preg_match('/[^a-zA-Z0-9_]/', $value) == 0;
    }

    function is_chinese($value)
    {
        return preg_match("/^[\x80-\xff]+/", $value, $match) && ($match[0] == $value);
    }

    /**
     * 是否是控制字符
     */
    function is_cntrl($value)
    {
        return ctype_cntrl($value);
    }

    /**
     * 是否是数字字符
     */
    function is_digits($value)
    {
        return ctype_digit($value);
    }

    /**
     * 是否是数字(包括小数点的浮点型数字)
     */
    function is_numeric($value)
    {
        return is_numeric($value);
    }

    /**
     * 是否是可见的字符
     */
    function is_graph($value)
    {
        return ctype_graph($value);
    }

    /**
     * 是否是全小写
     */
    function is_lower($value)
    {
        return ctype_lower($value);
    }

    /**
     * 是否是可打印的字符
     */
    function is_print($value)
    {
        return ctype_print($value);
    }

    /**
     * 是否是标点符号
     */
    function is_punct($value)
    {
        return ctype_punct($value);
    }

    /**
     * 是否是空白字符
     */
    function is_whitespace($value)
    {
        return ctype_space($value);
    }

    /**
     * 是否是全大写
     */
    function is_upper($value)
    {
        return ctype_upper($value);
    }

    /**
     * 是否是十六进制数
     */
    function is_xdigits($value)
    {
        return ctype_xdigit($value);
    }

    /**
     * 是否是 ASCII 字符
     */
    function is_ascii($value)
    {
        return preg_match('/[^\x20-\x7f]/', $value) == 0;
    }

    /**
     * 是否是电子邮件地址
     */
    function is_email($value)
    {
        return preg_match('/^[a-z0-9]+[\._\-\+]?([a-z0-9])+@([a-z0-9]+[-a-z0-9]*\.)+[a-z0-9]+$/i', $value);
    }

    /**
     * 是否是日期（yyyy/mm/dd、yyyy-mm-dd）
     */
    function is_date($value)
    {
        if (strpos($value, '-') !== false)
            $p = '-';
        elseif (strpos($value, '/') !== false)
            $p = '\/';
        else
            return false;

        if (preg_match('/^\d{4}' . $p . '\d{1,2}' . $p . '\d{1,2}$/', $value)) {
            $arr = explode($p, $value);
            if (count($arr) < 3) return false;

            list($year, $month, $day) = $arr;
            return checkdate($month, $day, $year);
        } else
            return false;
    }

    /**
     * 是否是时间（hh:mm:ss）
     */
    function is_time($value)
    {
        $parts = explode(':', $value);
        $count = count($parts);
        if ($count != 2 || $count != 3) return false;
        if ($count == 2) $parts[2] = '00';
        $test = @strtotime($parts[0] . ':' . $parts[1] . ':' . $parts[2]);
        if ($test === -1 || $test === false || date('H:i:s') != $value)
            return false;
        return true;
    }

    /**
     * 是否是 日期 + 时间
     */
    function is_datetime($value)
    {
        $test = @strtotime($value);
        if ($test === false || $test === -1)
            return false;
        return true;
    }

    /**
     * 是否是 整数
     */
    function is_int($value)
    {
        $value = str_replace($this->_locale['decimal_point'], '.', $value);
        $value = str_replace($this->_locale['thousands_sep'], '', $value);
        return strval(intval($value)) == $value;
    }

    /**
     * 是否是 浮点数
     */
    function is_float($value)
    {
        $value = str_replace($this->_locale['decimal_point'], '.', $value);
        $value = str_replace($this->_locale['thousands_sep'], '', $value);

        return strval(floatval($value)) == $value;
    }

    /**
     * 是否是 IPv4 地址（格式为 a.b.c.h）
     */
    function is_ipv4($value)
    {
        $test = @ip2long($value);
        return $test !== -1 && $test !== false;
    }

    // 是否是八进制数值
    function is_octal($value)
    {
        return preg_match('/0[0-7]+/', $value);
    }

    /**
     * 是否是二进制数值
     */
    function is_binary($value)
    {
        return preg_match('/[01]+/', $value);
    }

    /**
     * 是否是 Internet 域名
     */
    function is_domain($value)
    {
        return preg_match('/[a-z0-9\.]+/i', $value);
    }

    function is_url($value)
    {
        // SCHEME
        $urlregex = "^(https?|ftp)\:\/\/";

        // USER AND PASS (optional)
        $urlregex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?";

        // HOSTNAME OR IP
        $urlregex .= "[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*";
        // PORT (optional)
        $urlregex .= "(\:[0-9]{2,5})?";
        // PATH (optional)
        $urlregex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?";
        // GET Query (optional)
        $urlregex .= "(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?";
        // ANCHOR (optional)
        $urlregex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?\$";

        // 因为 eregi 在5.3被废弃了
        return preg_match("~{$urlregex}~i", $value);
    }

    /**
     * 验证是否 不是被注入攻击的值
     * the hacker defense for php
     */
    function notHackerDefense($value)
    {
        $noexps = array(
            '/<[^>]*script.*\"?[^>]*>/', '/<[^>]*style.*\"?[^>]*>/',
            '/<[^>]*object.*\"?[^>]*>/', '/<[^>]*iframe.*\"?[^>]*>/',
            '/<[^>]*applet.*\"?[^>]*>/', '/<[^>]*window.*\"?[^>]*>/',
            '/<[^>]*docuemnt.*\"?[^>]*>/', '/<[^>]*cookie.*\"?[^>]*>/',
            '/<[^>]*meta.*\"?[^>]*>/', '/<[^>]*alert.*\"?[^>]*>/',
            '/<[^>]*form.*\"?[^>]*>/', '/<[^>]*php.*\"?[^>]*>/', '/<[^>]*img.*\"?[^>]*>/'
        );//not allowed in the system
        foreach ($noexps as $exp) { //checking there's no matches
            if (preg_match($exp, $value)) return false;
        }
        return true;
    }
}