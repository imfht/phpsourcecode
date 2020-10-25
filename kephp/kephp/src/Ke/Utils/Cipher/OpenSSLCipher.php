<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Utils\Cipher;


class OpenSSLCipher
{

    /** @var bool|array */
    private static $cipherMethods = false;

    private static $instances = [];

    public static function getAllMethods(): array
    {
        if (static::$cipherMethods === false) {
            static::$cipherMethods = openssl_get_cipher_methods(true);
        }
        return static::$cipherMethods;
    }

    public static function isSupport(string $method)
    {
        if (array_search($method, static::getAllMethods()) === false) {
            return false;
        }
        return $method;
    }

    public static function getInstance(string $key, string $method = null): OpenSSLCipher
    {
        if (empty($key))
            throw new \Exception('Invalid cipher key. It cannot be empty!');
        if (!isset(static::$instances[$key])) {
            static::$instances[$key] = new static($key, $method);
        }
        return static::$instances[$key];
    }

    private $method = 'AES-256-CFB';

    private $key = '';

    protected $debug = false;

    public function __construct(string $key, string $method = null)
    {
        if (isset($method)) {
            if (!static::isSupport($method))
                throw new \Exception('Not support the cipher method "' . $method . '"!');
            $this->method = $method;
        }

        if (empty($key))
            throw new \Exception('The cipher key cannot be empty!');
        $this->key = $key;
    }

    public function setDebug(bool $isDebug)
    {
        $this->debug = $isDebug;
        return $this;
    }

    public function debug(string $key, string $content, bool $isEncode = false)
    {
        if ($this->debug) {
            $len = strlen($content);
            if ($isEncode)
                $content = base64_encode($content);
            var_dump("{$key}:{$content}[$len]");
        }
        return $this;
    }

    protected function generateIV(): string
    {
        $ivlen = openssl_cipher_iv_length($this->method);
        $iv = openssl_random_pseudo_bytes($ivlen);
        return $iv;
    }

    protected function generateKey(): string
    {
        $keyHash = hash('sha256', $this->key);
        $packHash = pack('H*', $keyHash);
        return $packHash;
    }

    protected function generateEncryptHash(string $raw, string $iv = null)
    {
        if (empty($iv))
            $iv = $this->generateIV();
        return hash_hmac('sha256', $raw, $iv, true);
    }

    protected function getEncryptHashSize()
    {
        $hash = $this->generateEncryptHash('any');
        return strlen($hash);
    }

    protected function garbleEncryptResult(string $raw, string $iv)
    {
        $hash = $this->generateEncryptHash($raw, $iv);
        $this->debug('encrypt-iv', $iv, true);
        $this->debug('encrypt-raw', $raw, true);
        $this->debug('encrypt-hash', $hash, true);
        return $iv . $raw . $hash;
    }

    protected function segmentDecryptText(string $text)
    {
        $ivSize = openssl_cipher_iv_length($this->method);
        $hashSize = $this->getEncryptHashSize();
        $iv = substr($text, 0, $ivSize);
        $raw = substr($text, $ivSize, -$hashSize);
        $hash = substr($text, -$hashSize);
        $this->debug('decrypt-iv', $iv, true);
        $this->debug('decrypt-raw', $raw, true);
        $this->debug('decrypt-hash', $hash, true);
        return [$iv, $raw, $hash];
    }

    protected function verifyDecrypt(string $raw, string $iv, string $hash)
    {
        if ($hash !== $this->generateEncryptHash($raw, $iv))
            return false;
        return true;
    }

    protected function encryptEncode(string $text)
    {
        return bin2hex($text);
    }

    protected function decryptDecode(string $text)
    {
        return hex2bin($text);
    }

    protected function onEncryptInput($data): string
    {
        $text = json_encode($data);
        $text = gzdeflate($text, 9);
        return $text;
    }

    protected function onEncryptOutput(string $raw, string $iv)
    {
        $text = $this->garbleEncryptResult($raw, $iv);
        return $this->encryptEncode($text);
    }


    public function encrypt($data): string
    {
        $text = $this->onEncryptInput($data);
        $key = $this->generateKey();
        $iv = $this->generateIV();
        $encryptRaw = openssl_encrypt($text, $this->method, $key, OPENSSL_RAW_DATA, $iv);
        return $this->onEncryptOutput($encryptRaw, $iv);
    }

    protected function onDecryptInput(string $text, string & $iv)
    {
        $text = $this->decryptDecode($text);
        list($iv, $raw, $hash) = $this->segmentDecryptText($text);
        if ($this->verifyDecrypt($raw, $iv, $hash)) {
            return $raw;
        }
        return false;
    }

    protected function onDecryptOutput(string $text)
    {
        $text = gzinflate($text);
        $text = json_decode($text, true);
        return $text;
    }

    public function decrypt(string $text)
    {
        $iv = '';
        $raw = $this->onDecryptInput($text, $iv);
        if ($raw === false)
            throw new \Exception('Cipher decrypt verify error!');
        $decryptText = openssl_decrypt($raw, $this->method, $this->generateKey(), OPENSSL_RAW_DATA, $iv);
        return $this->onDecryptOutput($decryptText);
    }
}