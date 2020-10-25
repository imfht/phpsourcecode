<?php

namespace CigoAdminLib\Lib;

interface ErrorCode {
	//错误码
	const ERROR_CODE_NOT_EXIST = '1001';//数据不存在
	const ERROR_CODE_ARGS_WRONG = '1002'; //参数错误
	const ERROR_CODE_PHONE_ALREADY_EXIST = '1003'; //手机号码已存在
	const ERROR_CODE_PHONE_SEND_LIMITED = '1004';//发送短信超限，详情见提示消息msg
	const ERROR_CODE_PHONE_SEND_ERROR_UNKOWN = '1005';//发送短信未知错误
	const ERROR_CODE_PHONE_VERIFY_CODE_ERROR = '1006';//验证码验证失败
	const ERROR_CODE_REQUEST_TYPE_WRONG = '1007';//请求类型错误
	const ERROR_CODE_UNKOWN = '1008';//未知错误
	const ERROR_CODE_USER_NOT_EXIST = '1009';//用户不存在
	const ERROR_CODE_PWD_ERROR = '1010'; //密码错误
	const ERROR_CODE_UPLOAD_FILE_TOOBIG = '1011'; //上传图片过大
	const ERROR_CODE_UPLOAD_FILE_MIME_ERROR = '1012';//文件类型错误
	const ERROR_CODE_UPLOAD_FILE_EXT_ERROR = '1013';//文件类型错误
	const ERROR_CODE_UPLOAD_PATH_NO_WRITABLE = '1014';//上传目录不可写
	const ERROR_CODE_UPLOAD_PATH_MKDIR_ERROR = '1015';//上传目录创建失败
	const ERROR_CODE_UPLOAD_TMP_FILE_ERROR = '1016';//上传临时保存文件错误
	const ERROR_CODE_UPLOAD_FILE_SAVE_ERROR = '1017';//保存上传文件错误
	const ERROR_CODE_UPLOAD_DB_SAVE_ERROR = '1018';//保存上传文件数据库错误
	const ERROR_CODE_UPLOAD_SAVE_FILE_EXIST_ERROR = '1019';//保存上传文件重名
	const ERROR_CODE_DECRYPT_ERROR = '1020';//解密失败
	const ERROR_CODE_UNALLOWED_REQUEST = '1021';//非法访问
	const ERROR_CODE_NEED_RELOGIN = '1022';//需要重新登陆
	const ERROR_CODE_NEWPWD_EQ_OLDPWD = '1023';//新旧密码相同
	const ERROR_CODE_NICKNAME_ALREADY_INUSE = '1024';//昵称已被占用
	const ERROR_CODE_OTHER_ERROR = '10025';//其它错误
	const ERROR_CODE_RETRY = '10026';//请重新尝试
	const ERROR_CODE_NO_CHANGE = '10027';//数据无变化
	const ERROR_CODE_DATA_ALREADY_EXIST = '10028';//数据重复
	const ERROR_CODE_DATA_NUM_LIMIT = '10029';//数据数量超限
	const ERROR_CODE_WEIXIN_BIND_TRUE = '10030';//已绑定微信
	const ERROR_CODE_WEIXIN_BIND_FALSE = '10031';//未绑定微信
    const ERROR_CODE_CART_VERIFY_NOT_IDENT = '10040';//未实名认证
    const ERROR_CODE_CART_VERIFY_IN_AUDIT = '10041';//实名认证审核中
    const ERROR_CODE_CART_VERIFY_NOT_ADOPT = '10042';//实名认证审未通过
}