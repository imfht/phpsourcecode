<?php
/**
 * This file is a part of FormCrypt.
 * Published under MIT License.
 */

namespace IXNetwork\FormCrypt;

/**
 * Class Decryptor
 * @package IXNetwork\FormCrypt
 */
class Decryptor
{
    /**
     * @var Decryptor
     */
    protected static $instance;
    
    /**
     * @var string
     */
    protected $privateKey;

    /**
     * Decryptor actual constructor.
     */
    protected function __construct()
    {
        @session_start();
        $private = $_SESSION['FormCrypt-privateKey'];
        $this->privateKey = openssl_pkey_get_private($private, "Codes change the world");
    }

    /**
     * Disable cloning
     */
    protected function __clone()
    {
    }

    /**
     * Construction function, enforce single construction
     *
     * @return Decryptor
     */
    public static function construct()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Decrypt encrypted form content
     *
     * @param string $content
     * @return bool|string
     */
    public function decrypt($content)
    {
        if (openssl_private_decrypt(base64_decode($content), $decryptedContent, $this->privateKey)) {
            return false;
        } else {
            return $decryptedContent;
        }
    }
}
