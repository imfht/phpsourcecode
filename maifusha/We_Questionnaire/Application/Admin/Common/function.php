<?php 
/**
 * 账号登录后，对session和cookie的处理
 * @param array $user  账号信息关联数组
 */
function loginAccount($user)
{
	/* sesion处理 */
	session('authName', $user['account']);
	session('last_login_date', $user['last_login_date']);
	session('last_login_ip', $user['last_login_ip']);

	/* cookie处理 */
	cookie('account', $user['account']);
	cookie('password', $user['password']);
}

/**
 * 检查字段是否填充完整
 * @param array $fieldList  待检查的字段
 * @param array $container  存储了各字段值的容器数组
 * @return array  填充完整则返回验证过的数组， 否则返回false
 */
function checkFilled($fieldList, $container)
{
	foreach ($fieldList as $field) {
		if( empty($container[$field]) ){
			return false;
		}else{
			$list[$field] = $container[$field];
		}
	}

	return $list;
}

/**
 * 解码被JS中escape函数进行过unicode编码的数据
 */
function unicodeDecode($data)
{  
	$mapper = function ($match) {
	  return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
	};

	$rs = preg_replace_callback('/%u([0-9a-f]{4})/i', $mapper, $data);

	return $rs;
}

/**
 * 构造微信发送的消息体
 * @param string $msgType  消息类型
 * @param string $content  消息内容  除去文本消息为消息文本，其他消息类型为mediaID
 */
function createMsg($msgType, $content)
{
    switch( $msgType ){
        case 'mpnews':
        case 'image':
        case 'voice':
            $msgTag = 'media_id';
            break;

        case 'mpvideo':
                    $msgTag = 'mpvideo';
                    break;

        case 'text':
            $msgTag = 'content';
            break;
    }

    $msgTpl = <<<str
            {
                "touser":"%s",
               "$msgType":{
                    "$msgTag":"$content"
                },
               "msgtype":"$msgType"
            }
str;

    return $msgTpl;
}