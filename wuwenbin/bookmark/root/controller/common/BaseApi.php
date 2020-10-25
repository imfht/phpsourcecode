<?php
/**
 * API接口基类
 */
class ControllerBaseApi extends ControllerBase
{
    /**
     * @var object $rsaInstance RSA实例
     */
    protected $rsaInstance;

    /**
     * @var object $aesInstance AES实例
     */
    protected $aesInstance;

    /**
     * @var array $params 请求参数
     */
    protected $params;

    /**
     * @var array $token 会话数据
     */
    protected $token;

    /**
     * 构造函数
     */
    public function __construct()
    {
        if (!isPost()) {
            $this->send(0, '请求错误');
        }

        $this->params = $this->parseRequest();
        $this->token = $this->getTokenData(isset($this->params['token']) ? $this->params['token'] : '');
        $this->checkLogin(isset($this->token['uid']) ? $this->token['uid'] : 0);
    }

    /**
     * 获取RSA实例
     *
     * @return object
     */
    protected function getRsaInstance()
    {
        if ($this->rsaInstance) {
            return $this->rsaInstance;
        }

        $libFile = App::getRootPath() . '/include/misc/RSA.php';
        include_once $libFile;

        $rsa = new RSA();
        $rsa->setPrivateKey(Config::get('rsaPrivateKey'));
        $this->rsaInstance = $rsa;
        return $this->rsaInstance;
    }

    /**
     * RSA加密
     *
     * @param string $data 数据
     *
     * @return string
     */
    protected function rsaEncrypt($data)
    {
        return $this->getRsaInstance()->encryptWithPrivateKey($data);
    }

    /**
     * RSA解密
     *
     * @param string $data 数据
     *
     * @return string
     */
    protected function rsaDecrypt($data)
    {
        return $this->getRsaInstance()->decryptWithPrivateKey($data);
    }

    /**
     * 获取AES实例
     *
     * @return object
     */
    protected function getAesInstance()
    {
        if ($this->aesInstance) {
            return $this->aesInstance;
        }

        $libFile = App::getRootPath() . '/include/misc/AES.php';
        include_once $libFile;

        $aes = new AES();
        $this->aesInstance = $aes;
        return $this->aesInstance;
    }

    /**
     * AES加密
     *
     * @param string $data 数据
     * @param string $key 密钥
     *
     * @return string
     */
    protected function aesEncrypt($data, $key)
    {
        return $this->getAesInstance()->encrypt($data, $key);
    }

    /**
     * AES解密
     *
     * @param string $data 数据
     * @param string $key 密钥
     *
     * @return string
     */
    protected function aesDecrypt($data, $key)
    {
        return $this->getAesInstance()->decrypt($data, $key);
    }

    /**
     * 解析请求
     */
    protected function parseRequest()
    {
        $data = file_get_contents("php://input");
        $data = (array) json_decode($this->rsaDecrypt($data), true);
        return $data;
    }

    /**
     * 获取会话数据
     *
     * @param string $name 会话ID
     *
     * @return mixed
     */
    protected function getTokenData($id)
    {
        if (!$id) {
            return false;
        }

        $id = addslashes($id);
        $token = M('Token')->get($id);
        if (!$token) {
            return false;
        }

        if ($token['expire_time'] < time()) {
            return false;
        }

        return $token;
    }

    /**
     * 检查是否登录
     *
     * @param int $uid 用户ID
     *
     * @return bool
     */
    protected function checkLogin($uid)
    {
        $uid = (int) $uid;
        if (!$uid) {
            return false;
        }

        $user = M('User')->get($uid, 'uid');
        if (!$user) {
            return false;
        }

        $this->mid = $uid;
        $this->user = $user;
        return true;
    }

    /**
     * 发送响应数据
     *
     * @param int $status 状态
     * @param string $msg 提示信息
     * @param mixed $data 数据
     * @param bool $encrypt 是否加密数据
     */
    protected function send($status, $msg = '', $data = null, $encrypt = false)
    {
        if ($encrypt && !$this->token) {
            $status = 0;
            $msg = '无法加密数据';
            $data = null;
            $encrypt = false;
        }

        $res = array(
            "status" => (int) $status,
            "msg" => strval($msg),
            "data" => $data,
        );

        if ($encrypt) {
            echo $this->aesEncrypt(json_encode($res), $this->token['app_secret']);
        } else {
            echo json_encode($res);
        }

        exit;
    }

    /**
     * 发送加密响应数据
     *
     * @param int $status 状态
     * @param string $msg 提示信息
     * @param mixed $data 数据
     */
    protected function sendEncrypted($status, $msg = '', $data = null)
    {
        $this->send($status, $msg, $data, true);
    }

    /**
     * 获取请求参数
     *
     * @param string $name 参数名
     * @param mixed $default 默认值
     *
     * @return mixed
     */
    protected function getParam($name, $default = null)
    {
        return isset($this->params[$name]) ? $this->params[$name] : $default;
    }
}
