<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace util;

/**
 * JWT鉴权
 * @author zongjl
 * @date 2019/6/18
 * Class Jwt
 * @package App\Helpers
 */
class Jwt
{
    // 头部
    private static $header = [
        'alg' => 'HS256', //生成signature的算法
        'typ' => 'JWT'    //JWT 令牌统一写为JWT
    ];

    // 使用HMAC生成信息摘要时所使用的密钥
    private static $key = '34c68cdccb5b6feb4f7f0a5ede9f5932';

    /**
     * alg属性表示签名的算法（algorithm），默认是 HMAC SHA256（写成 HS256）；typ属性表示这个令牌（token）的类型（type），JWT 令牌统一写为JWT
     * @return string
     * @author zongjl
     * @date 2019/6/18
     */
    public function getHeader()
    {
        return self::base64UrlEncode(json_encode(self::$header, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Payload 部分也是一个 JSON 对象，用来存放实际需要传递的数据
     * JWT 规定了7个官方字段，供选用，这里可以存放私有信息，比如uid
     * @param $uid
     * @return string
     * @author zongjl
     * @date 2019/6/18
     */
    public function getPayload($uid)
    {
        $payload = [
            'iss' => 'jwt_yh', //签发人
            'exp' => time() + 60 * 60 * 24 * 7, //过期时间
            'sub' => 'YH', //主题
            'aud' => 'every', //受众
            'nbf' => time(), //生效时间,该时间之前不接收处理该Token
            'iat' => time(), //签发时间
            'jti' => 10001, //编号(JWT ID用于标识该JWT)
            'uid' => $uid, //私有信息，uid
        ];
        return self::base64UrlEncode(json_encode($payload, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取Token
     * @param array $payload jwt载荷
     * 格式如下非必须：
     * [
     *  'iss'=>'jwt_admin',  //该JWT的签发者
     *  'iat'=>time(),  //签发时间
     *  'exp'=>time()+7200,  //过期时间
     *  'nbf'=>time()+60,  //该时间之前不接收处理该Token
     *  'sub'=>'www.admin.com',  //面向的用户
     *  'jti'=>md5(uniqid('JWT').time())  //该Token唯一标识
     * ]
     * @return bool|string 返回结果
     * @author zongjl
     * @date 2019/6/18
     */
    public function getToken($uid)
    {
        // 获取JWT头
        $header = self::getHeader();
        // 获取JWT有效载荷
        $payload = self::getPayload($uid);
        // JWT头拼接JWT有效载荷
        $raw = $header . '.' . $payload;
        // Token字符串
        $token = $raw . '.' . self::signature($raw, self::$key, self::$header['alg']);
        // 返回Token
        return $token;
    }

    /**
     * 验证token是否有效,默认验证exp,nbf,iat时间
     * @param string $token token字符串
     * @return bool|mixed 返回结果
     * @author zongjl
     * @date 2019/6/18
     */
    public function verifyToken(string $token)
    {
        if (!$token) {
            return false;
        }
        $tokens = explode('.', $token);
        if (count($tokens) != 3) {
            return false;
        }

        list($base64header, $base64payload, $sign) = $tokens;

        // 获取jwt算法
        $base64decodeheader = json_decode(self::base64UrlDecode($base64header), JSON_OBJECT_AS_ARRAY);
        if (empty($base64decodeheader['alg'])) {
            return false;
        }

        //签名验证
        if (self::signature($base64header . '.' . $base64payload, self::$key, $base64decodeheader['alg']) !== $sign) {
            return false;
        }

        $payload = json_decode(self::base64UrlDecode($base64payload), JSON_OBJECT_AS_ARRAY);

        //签发时间大于当前服务器时间验证失败
        if (isset($payload['iat']) && $payload['iat'] > time()) {
            return false;
        }

        //过期时间小宇当前服务器时间验证失败
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }

        //该nbf时间之前不接收处理该Token
        if (isset($payload['nbf']) && $payload['nbf'] > time()) {
            return false;
        }

        return $payload['uid'];
    }

    /**
     * base64UrlEncode编码实现
     * @param string $str 需要编码的字符串
     * @return mixed 返回结果
     * @author zongjl
     * @date 2019/6/18
     */
    private static function base64UrlEncode(string $str)
    {
        return str_replace('=', '', strtr(base64_encode($str), '+/', '-_'));
    }

    /**
     * base64UrlDecode解码实现
     * @param string $str 需要解码的字符串
     * @return bool|string 返回结果
     * @author zongjl
     * @date 2019/6/18
     */
    private static function base64UrlDecode(string $str)
    {
        $remainder = strlen($str) % 4;
        if ($remainder) {
            $addlen = 4 - $remainder;
            $str .= str_repeat('=', $addlen);
        }
        return base64_decode(strtr($str, '-_', '+/'));
    }

    /**
     * HMACSHA256签名实现
     * @param string $str 待签名字符串
     * @param string $key 加密密钥
     * @param string $alg 算法方式
     * @return mixed 返回结果
     * @author zongjl
     * @date 2019/6/18
     */
    private static function signature(string $str, string $key, string $alg = 'HS256')
    {
        $alg_config = array(
            'HS256' => 'sha256'
        );
        return self::base64UrlEncode(hash_hmac($alg_config[$alg], $str, $key, true));
    }
}
