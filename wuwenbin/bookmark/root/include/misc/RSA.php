<?php
if (!class_exists('RSA')) {
    /**
     * RSA加密/解密
     */
    class RSA
    {
        /**
         * @var resource $privateKey 私钥
         */
        protected $privateKey;

        /**
         * @var int $privateKeyBits 私钥位数
         */
        protected $privateKeyBits;

        /**
         * @var resource $publicKey 公钥
         */
        protected $publicKey;

        /**
         * @var int $publicKeyBits 公钥位数
         */
        protected $publicKeyBits;

        /**
         * 设置私钥
         *
         * @param string $privateKey 私钥内容或文件
         */
        function setPrivateKey($privateKey)
        {
            if (is_file($privateKey)) {
                $privateKey = file_get_contents($privateKey);
            }

            $this->privateKey = openssl_pkey_get_private($privateKey);
            if ($this->privateKey) {
                $details = openssl_pkey_get_details($this->privateKey);
                $this->privateKeyBits = $details['bits'];
            }
        }

        /**
         * 设置公钥
         *
         * @param string $publicKey 公钥内容或文件
         */
        function setPublicKey($publicKey)
        {
            if (is_file($publicKey)) {
                $publicKey = file_get_contents($publicKey);
            }

            $this->publicKey = openssl_pkey_get_public($publicKey);
            if ($this->publicKey) {
                $details = openssl_pkey_get_details($this->publicKey);
                $this->publicKeyBits = $details['bits'];
            }
        }

        /**
         * 使用私钥加密数据
         *
         * @param string $data 数据
         *
         * @return mixed
         */
        function encryptWithPrivateKey($data)
        {
            if (!$this->privateKey) {
                return false;
            }

            $blocks = array();
            $blockLength = $this->privateKeyBits / 8 - 11;

            for ($i = 0; $i < strlen($data); $i += $blockLength) {
                $block = substr($data, $i, $blockLength);
                openssl_private_encrypt($block, $res, $this->privateKey);
                $blocks[] = $res;
            }

            return base64_encode(implode('', $blocks));
        }

        /**
         * 使用公钥加密数据
         *
         * @param string $data 数据
         *
         * @return mixed
         */
        function encryptWithPublicKey($data)
        {
            if (!$this->publicKey) {
                return false;
            }

            $blocks = array();
            $blockLength = $this->publicKeyBits / 8 - 11;

            for ($i = 0; $i < strlen($data); $i += $blockLength) {
                $block = substr($data, $i, $blockLength);
                openssl_public_encrypt($block, $res, $this->publicKey);
                $blocks[] = $res;
            }

            return base64_encode(implode('', $blocks));
        }

        /**
         * 使用私钥解密数据
         *
         * @param string $data 数据
         *
         * @return mixed
         */
        function decryptWithPrivateKey($data)
        {
            if (!$this->privateKey) {
                return false;
            }

            $blocks = array();
            $blockLength = $this->privateKeyBits / 8;

            $data = base64_decode($data);
            for ($i = 0; $i < strlen($data); $i += $blockLength) {
                $block = substr($data, $i, $blockLength);
                openssl_private_decrypt($block, $res, $this->privateKey);
                $blocks[] = $res;
            }

            return implode('', $blocks);
        }

        /**
         * 使用公钥解密数据
         *
         * @param string $data 数据
         *
         * @return mixed
         */
        function decryptWithPublicKey($data)
        {
            if (!$this->publicKey) {
                return false;
            }

            $blocks = array();
            $blockLength = $this->publicKeyBits / 8;

            $data = base64_decode($data);
            for ($i = 0; $i < strlen($data); $i += $blockLength) {
                $block = substr($data, $i, $blockLength);
                openssl_public_decrypt($block, $res, $this->publicKey);
                $blocks[] = $res;
            }

            return implode('', $blocks);
        }
    }
}
