<?php

namespace CIPlus;

abstract class Jwt {
    protected $expire_time;
    protected $refresh_time;

    protected $header = array('typ' => null, 'rap' => null);
    protected $payload = array('id' => null, 'iat' => null, 'exp' => null);

    protected $sheet = array();

    public function __construct(array $config = array()) {
        if (is_array($config)) {
            foreach ($config as $key => $val) {
                if (property_exists($this, $key)) {
                    $this->$key = $val;
                }
            }
        }
    }

    /**
     * 生成 Token
     * @param array $header
     * @param array $payload
     * @return string
     */
    public function generator(array $header, array $payload) {
        if ($this->verifyHeader($header) && key_exists('id', $payload)) {

            $header = $this->normHeader($header);
            $payload = $this->normPayload($payload);
            $rap = $header['rap'];

            $header = $this->encode(json_encode($header));
            $payload = $this->encode(json_encode($payload));

            $cipher = $header . "." . $payload;
            $sign = $this->encrypt($cipher, $rap);
            return $cipher . "." . $this->encode($sign);

        } else {

            return null;

        }
    }

    /**
     * 验证 Token
     * @param $token
     * @return array
     */
    public function validator($token) {
        $tkr = explode('.', $token);
        if (count($tkr) === 3) {
            $header = $tkr[0];
            $payload = $tkr[1];
            $sign = $this->decode($tkr[2], false);

            $cipher = $header . '.' . $payload;

            $header = $this->decode($header);
            $rap = $header['rap'];
            $payload = $this->decode($payload);

            if ($this->decrypt($sign, $rap) === $cipher) {
                return array(
                    'header' => $header,
                    'payload' => $payload,
                );
            }
        }

        return null;
    }

    /**
     * 设置 Header
     * @param $header
     * @return mixed
     */
    protected function normHeader($header) {
        if (!in_array($header['typ'], array('jwt'))) {
            $header['typ'] = 'jwt';
        }
        if (!key_exists($header['rap'], $this->sheet)) {
            $header['rap'] = 'norm';
        }
        return $header;
    }

    /**
     * 设置 Payload
     * @param $payload
     * @return mixed
     */
    protected function normPayload($payload) {
        $payload['iat'] = time();
        $payload['exp'] = time() + $this->expire_time;
        return $payload;
    }

    /**
     * 验证 Header
     * @param $header
     * @return bool
     */
    protected function verifyHeader($header) {
        return $this->header == array_intersect_key($this->header, $header);
    }

    /**
     * 验证 Payload
     * @param $payload
     * @return bool
     */
    protected function verifyPayload($payload) {
        return $this->payload == array_intersect_key($this->payload, $payload)
            && $payload['exp'] > time()
            && $payload['iat'] < time();
    }

    /**
     * 安全编码
     * @param $data
     * @return mixed|string
     */
    protected function encode($data) {
        if (is_array($data)) $data = json_encode($data);
        $data = base64_encode($data);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

    /**
     * 安全解码
     * @param $string
     * @param $toArray
     * @return bool|string
     */
    protected function decode($string, $toArray = true) {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        $data = base64_decode($data);
        if ($toArray) $data = json_decode($data, true);
        return $data;
    }

    /**
     * 加密
     * @param $cipher
     * @param $rap
     * @return mixed
     */
    abstract function encrypt($cipher, $rap);

    /**
     * 解密
     * @param $sign
     * @param $rap
     * @return mixed
     */
    abstract function decrypt($sign, $rap);
}