<?php
/**
 * 数据过滤器
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */
namespace herosphp\filter;

class Filter {
    //数据类型
    const DFILTER_LATIN = 1;  //简单字符
    const DFILTER_URL = 2;    //url
    const DFILTER_EMAIL = 4;    //email
    const DFILTER_NUMERIC = 8;    //数字
    const DFILTER_STRING = 16;    //字符串
    const DFILTER_MOBILE = 32;    //手机号码
    const DFILTER_TEL = 64;    //电话号码
    const DFILTER_IDENTIRY = 128;    //身份证
    const DFILTER_REGEXP = 256;    //正则表达式
	const DFILTER_ZIP = 1024;    //邮编

    //数据的净化
    const DFILTER_SANITIZE_TRIM = 1;    //去空格
    const DFILTER_SANITIZE_SCRIPT = 2;    //去除javascript脚本
    const DFILTER_SANITIZE_HTML = 4;    //去除html标签
    const DFILTER_MAGIC_QUOTES = 8;    //去除sql注入
    const DFILTER_SANITIZE_INT = 16;    //转整数
    const DFILTER_SANITIZE_FLOAT = 32;    //转浮点数

    public static function init() {

        //do nothing here
    }

    /**
     * 判断是否拉丁字符
     * @param $value
     * @return bool
     */
    public static function isLatin(&$value) {
        return (preg_match('/^[a-z0-9_]+$/i', $value) == 1);
    }

    /**
     * 判断是否url
     * @param $value
     * @return bool
     */
    public static function isUrl(&$value) {
        return (filter_var($value, FILTER_VALIDATE_URL) != FALSE);
    }

    /**
     * 判断是否邮箱
     * @param $value
     * @return bool
     */
    public static function isEmail(&$value) {
        if ( $value == '' ) return true;
        return (filter_var($value, FILTER_VALIDATE_EMAIL) != FALSE);
    }

    /**
     * 是否字符串
     * @param $value
     * @return bool
     */
    public static function isString(&$value) {
		return is_string($value);
    }

    /**
     * 判断是否邮编
     * @param $value
     * @return bool
     */
    public static function isZip(&$value) {
        return (preg_match('/^[0-9]{6}$/', $value) == 1);
    }

    /**
     * 判断是否手机号码
     * @param $value
     * @return bool
     */
    public static function isMobile(&$value) {
        if ( $value == '' ) return true;
        return (preg_match('/^1[3|5|4|7|8][0-9]{9}$/', $value) == 1);
    }

    /**
     * 判断是否电话号码
     * @param $value
     * @return bool
     */
    public static function isTelephone(&$value) {
        return (preg_match('/^0[1-9][0-9]{1,2}-[0-9]{7,8}$/', $value) == 1);
    }

    /**
     * 检验身份证号码是否合格
     * @param $value
     * @return bool
     */
    public static function isIdentity(&$value) {
        if ( $value == '' ) return true;
        if ( strlen($value) != 15 && strlen($value) != 18 )
            return false;
        //如果是15位的身份证号码则转换位18位的身份证号码
        if ( strlen($value) == 15 ) $value = self::idcard_15to18($value);

        return self::idcard_checksum18($value);
    }

    /**
     * 正则验证
     * @param $value
     * @param $pattern
     * @return int
     */
    public static function pregCheck(&$value, $pattern) {

        if ( !is_string($pattern) ) return false;
        return preg_match($pattern, $value);
    }

    /**
     * 计算身份证号码中的检校码
     * @param string $idcard_base 身份证号码的前十七位
     * @return string 检校码
     */
    private static function idcard_verify_number($idcard_base) {

        if ( strlen($idcard_base) != 17 ) return false;

        //加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码对应值
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ( $i = 0; $i < strlen($idcard_base); $i++ ) {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;
    }

    /**
     * 将15位身份证升级到18位
     * @param string $idcard 十五位身份证号码
     * @return bool|string
     */
    private static function idcard_15to18($idcard) {

        if ( strlen($idcard) != 15 ) return false;

        // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
        if ( array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false ) {
            $idcard = substr($idcard, 0, 6) . '18'. substr($idcard, 6, 15);
        } else {
            $idcard = substr($idcard, 0, 6) . '19'. substr($idcard, 6, 15);
        }

        $idcard = $idcard . self::idcard_verify_number($idcard);
        return $idcard;
    }

    /**
     * 18位身份证校验码有效性检查
     * @param string $idcard 十八位身份证号码
     * @return bool
     */
    private static function idcard_checksum18($idcard) {

        if ( strlen($idcard) != 18 ) return false;

        $idcard_base = substr($idcard, 0, 17);
        if ( self::idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1)) ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 去除html标签
     * @param $value
     * @return mixed
     */
    public static function sanitizeHtml(&$value) {
        //sanitize regex rules
        $_rules = array( '/<[^>]*?\/?>/is' => '');
        return preg_replace(array_keys($_rules), $_rules, $value);
    }

    /**
     * 去除javascript标签
     * @param $value
     * @return mixed
     */
    public static function sanitizeScript(&$value) {
        //1. 去除javascript脚本.
        //2. 移除html节点js事件.
        $_rules = array(
            '/<script[^>]*?>.*?<\/script\s*>/i',
            '/<([^>]*?)on[a-zA-Z]+\s*=\s*".*?"([^>]*?)>/i',
            '/<([^>]*?)on[a-zA-Z]+\s*=\s*\'.*?\'([^>]*?)>/i'
        );

        return preg_replace($_rules, array('', '<$1$2>'), $value);
    }

    /**
     * 转义sql特殊字符，防止sql注入
     * @param $value
     * @return mixed
     */
    public static function &sanitizeSQL(&$value) {
        return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"',     '\\Z'), $value);
    }

    /**
     * 检验数据
     * @param $value 要检验的值
     * @param $model 检验规则数据模型
     * @param $error 错误信息
     * @return bool|int|mixed|string
     */
    public static function check(&$value, &$model, &$error)
    {
        //非空验证
        if ( trim($value) == '' ) {
            $error = $model[3]['require'];
            return false;
        }
        //1. 数据类型验证
        $error = $model[3];
        $success = true;
        if ( ($model[0] & self::DFILTER_LATIN) != 0 )
            if ( ! self::isLatin( $value ) ) $success = false;
        if ( ($model[0] & self::DFILTER_URL) != 0 )
            if ( ! self::isUrl( $value ) ) $success = false;
        if ( ($model[0] & self::DFILTER_EMAIL) != 0 )
            if ( ! self::isEmail( $value ) ) $success = false;
        if ( ($model[0] & self::DFILTER_NUMERIC) != 0 )
            if ( ! is_numeric( $value ) ) $success = false;
        if ( ($model[0] & self::DFILTER_STRING) != 0 )
            if ( ! self::isString( $value ) ) $success = false;
        if ( ($model[0] & self::DFILTER_ZIP) != 0 )
            if ( ! self::isZip( $value ) ) $success = false;
        if ( ($model[0] & self::DFILTER_MOBILE) != 0 )
            if ( ! self::isMobile( $value ) ) $success = false;
        if ( ($model[0] & self::DFILTER_TEL) != 0 )
            if ( ! self::isTelephone( $value ) ) $success = false;
        if ( ($model[0] & self::DFILTER_IDENTIRY) != 0 )
            if ( ! self::isIdentity( $value ) ) $success = false;
        if ( ($model[0] & self::DFILTER_REGEXP) != 0 )
            if ( ! self::pregCheck( $value, $model[1] ) ) $success = false;

        if ( $success == false ) {
            $error = $model[3]['type'];
            return false;
        }

        //2. 数据长度验证
        if ( $model[1] != null ) {
            if ( ($model[0] & self::DFILTER_NUMERIC) //如果是数字就对其进行大小验证
                && (($model[1][0] > 0 && $value < $model[1][0]) || $model[1][1] > 0 && $value > $model[1][1]) ) {
                $success = false;
            } else if( ($model[1][0] > 0 && mb_strlen($value, "UTF-8") < $model[1][0])  //非数字就进行长度验证
            || ($model[1][1] > 0 && mb_strlen($value, "UTF-8") > $model[1][1]) ) {
                $success = false;
            }
        }

        if ( $success == false ) {
            $error = $model[3]["length"];
            return false;
        }

        $error = null;
        //3. 数据净化
        if ( $model[2] == null ) return $value;
        if ( ( $model[2] & self::DFILTER_SANITIZE_TRIM ) != 0 )
            $value = trim($value);
        if ( ( $model[2] & self::DFILTER_SANITIZE_SCRIPT ) != 0 )
            $value = self::sanitizeScript($value);
        if ( ( $model[2] & self::DFILTER_SANITIZE_HTML ) != 0 )
            $value = self::sanitizeHtml($value);
        if ( ( $model[2] & self::DFILTER_SANITIZE_INT ) != 0 )
            $value = intval( $value );
        if ( ( $model[2] & self::DFILTER_SANITIZE_FLOAT ) != 0 )
            $value = floatval( $value );
        if ( ( $model[2] & self::DFILTER_MAGIC_QUOTES ) != 0 )
            $value = &self::sanitizeSQL( $value );

        return $value;
    }

    /**
     * 从数据模型中获取过滤后的数据
     * @param $src  表单的原始数据
     * @param $model 检验规则数据模型
     * @param $error 错误信息
     * @return array|bool
     */
    public static function loadFromModel(&$src, $model, &$error)
    {
        if ( !is_array($src) ) {
            $error = '数据模型格式错误';
            return false;
        }
        $data = array();
        foreach ( $src as $key => $value ) {
            if ( is_array($model[$key]) ) {
                //过滤数据
                $result = self::check($value, $model[$key], $error);

                if ( $result === false ) {
                    return false;
                }

                //存储过滤后的数据
                $data[$key] = $result;

            } else {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
