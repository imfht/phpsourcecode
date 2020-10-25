<?php
/**
 * Desc: 错误码服务类
 * Created by PhpStorm.
 * User: xuanskyer | <furthestworld@icloud.com>
 * Date: 2016-12-23 09:50:35
 */


namespace FurthestWorld\Validator\Src\Code;


class CodeService {

    //错误码列表
    const CODE_FAIL = [0, '验证失败'];
    const CODE_OK   = [1, '验证成功'];

    //1-1000保留为内部错误码使用
    const CODE_INVALID_PARAMS = [100, '非法参数：参数必须为数组！'];
    const CODE_INVALID_RULES  = [101, '非法验证规则：验证规则必须为数组！'];
    //1000-1999 内部错误码
    const CODE_PARAM_OK                = [1000, '参数验证通过！'];
    const CODE_NO_PARAM_NAME           = [1001, '参数名不能为空！'];
    const CODE_INVALID_CHECK_TYPE      = [1002, '参数验证方式非法！'];
    const CODE_NO_EXISTED_CHECK_METHOD = [1003, '参数验证方法不存在！'];
    const CODE_MUST_NOT_EMPTY          = [1004, '参数不能为空！'];
    const CODE_MUST_EMPTY              = [1005, '参数必须为空！'];
    const CODE_MUST_NUMBER             = [1006, '参数必须为数字！'];
    const CODE_MUST_STRING             = [1007, '参数必须为字符串'];
    const CODE_MUST_ARRAY              = [1008, '参数必须为数组！'];
    const CODE_MUST_NUMBER_GT0         = [1009, '参数必须为大于0的数字！'];
    const CODE_STRING_INVALID_MIN      = [1010, '参数长度小于最小长度%d'];
    const CODE_STRING_INVALID_MAX      = [1011, '参数长度大于最大长度%d'];
    const CODE_NOT_SAME                = [1012, '参数不相等'];
    const CODE_NOT_STRICT_SAME         = [1013, '参数不严格相等'];
    const CODE_INVALID_EMAIL           = [1014, '邮箱格式不合法！'];
    const CODE_INVALID_URL             = [1015, 'URL格式不合法！'];
    const CODE_INVALID_IP              = [1016, 'IP格式不合法！'];
    const CODE_INVALID_IPV6            = [1017, 'IPV6格式不合法！'];
    const CODE_INVALID_REGEX_MATCH     = [1018, '正则规则匹配失败！'];
    const CODE_NOT_IN_LIST             = [1019, '参数不在指定列表中！'];
    const CODE_INVALID_DATE            = [1020, '日期格式非法！'];
    const CODE_INVALID_JSON            = [1021, 'json格式非法！'];
    const CODE_INVALID_BOOL            = [1022, '非法布尔类型！'];
    const CODE_INVALID_ALPHA           = [1023, '参数必须为字母！'];
    const CODE_INVALID_ALPHA_UPPER     = [1024, '参数必须为大写字母！'];
    const CODE_INVALID_ALPHA_LOWER     = [1025, '参数必须为小写字母！'];
    const CODE_INVALID_ALPHA_NUM       = [1026, '参数必须为字母或数字！'];
    const CODE_INVALID_ALPHA_DASH      = [1027, '参数仅允许字母、数字、破折号（-）以及底线（_）！'];

    //2000-以后，用户自定义错误码


    public static function message($code = 0) {

    }

    public static function code($mess = '') {

    }
}