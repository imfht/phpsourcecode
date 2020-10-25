<?php
if (!class_exists('AES')) {
    /**
     * AES加密/解密
     */
    class AES
    {
        /**
         * @var string $cipher 算法
         */
        protected $cipher;

        /**
         * @var string $mode 模式
         */
        protected $mode;

        /**
         * 构造函数
         */
        function __construct()
        {
            $this->cipher = MCRYPT_RIJNDAEL_128;
            $this->mode = MCRYPT_MODE_ECB;
        }

        /**
         * 设置算法
         *
         * @param string $cipher 算法
         */
        function setCipher($cipher)
        {
            $this->cipher = $cipher;
        }

        /**
         * 设置模式
         *
         * @param string $mode 模式
         */
        function setMode($mode)
        {
            $this->mode = $mode;
        }

        /**
         * 加密数据
         *
         * @param string $data 数据
         * @param string $key 密钥
         *
         * @return string
         */
        function encrypt($data, $key = '')
        {
            $ivSize = mcrypt_get_iv_size($this->cipher, $this->mode);
            $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
            return base64_encode(mcrypt_encrypt($this->cipher, $key, trim($data), $this->mode, $iv));
        }

        /**
         * 解密数据
         *
         * @param string $data 数据
         * @param string $key 密钥
         *
         * @return string
         */
        function decrypt($data, $key = '')
        {
            $ivSize = mcrypt_get_iv_size($this->cipher, $this->mode);
            $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
            return trim(mcrypt_decrypt($this->cipher, $key, base64_decode($data), $this->mode, $iv));
        }
    }
}
