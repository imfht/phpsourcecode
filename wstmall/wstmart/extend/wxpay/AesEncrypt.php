<?php
/**
 * 微信加密算法
 */
class AesEncrypt {
    private $_key;
    private $_blockSize = 32;

    /**
     * PrpCrypt constructor.
     *
     * @param string $k 长度固定为43个字符，从a-z, A-Z, 0-9共62个字符中选取
     */
    public function __construct($k) {
        $this->_key = base64_decode($k . "=");
    }

    /**
     *  对需要加密的明文进行填充补位
     *
     * @param string $text 需要进行填充补位操作的明文
     * @return string
     */
    private function pkcs7Pad($text) {
        $textLength = strlen($text);
        //计算需要填充的位数
        $amountToPad = $this->_blockSize - ($textLength % $this->_blockSize);
        if ($amountToPad == 0) {
            $amountToPad = $this->_blockSize;
        }
        //获得补位所用的字符
        $padChr = chr($amountToPad);
        $tmp = "";
        for ($index = 0; $index < $amountToPad; $index++) {
            $tmp .= $padChr;
        }

        return $text . $tmp;
    }

    /**
     * 对解密后的明文进行补位删除
     *
     * @param string $text 解密后的明文
     * @return string
     *
     */
    private function pkcs7Unpad($text) {
        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > 32) {
            $pad = 0;
        }

        return substr($text, 0, (strlen($text) - $pad));
    }

    /**
     * 对明文进行加密
     *
     * @param string $text 需要加密的明文
     * @return string 加密后的密文
     */
    public function encrypt($text) {
        //获得16位随机字符串，填充到明文之前
        $random = $this->getRandomStr();
        $text = $random . pack("N", strlen($text)) . $text;
        $iv = substr($this->_key, 0, 16);
        $text = $this->pkcs7Pad($text);

        return openssl_encrypt($text, 'AES-256-CBC', $this->_key, OPENSSL_ZERO_PADDING, $iv);
    }

    /**
     * 对密文进行解密
     *
     * @param string $encrypted 需要解密的密文
     * @return string 解密得到的明文
     */
    public function decrypt($encrypted) {
        $iv = substr($this->_key, 0, 16);
        $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $this->_key, OPENSSL_ZERO_PADDING, $iv);
        //去除补位字符
        $result = $this->pkcs7Unpad($decrypted);
        //去除16位随机字符串 加密时添加16为随机字符串
        if (strlen($result) < 16) {
            return "";
        }
        $content = substr($result, 16, strlen($result));
        $lenList = unpack("N", substr($content, 0, 4));
        $contentLen = $lenList[1];

        return substr($content, 4, $contentLen);
    }

    /**
     * 随机生成16位字符串
     *
     * @return string 生成的字符串
     */
    private function getRandomStr() {
        $str = "";
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }

        return $str;
    }
}