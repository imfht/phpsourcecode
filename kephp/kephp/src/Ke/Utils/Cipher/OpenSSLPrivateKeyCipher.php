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


class OpenSSLPrivateKeyCipher
{
    private $inputKey = null;

    private $passphrase = '';

    private $isLoad = false;

    private $isLoadDetails = null;

    private $resource = null;

    private $keyError = false;

    private $keyDetails = ['key' => ''];

    public function __construct($privateKey, string $passphrase = '')
    {
        if (empty($privateKey)) {
            throw new \Exception('The input key cannot be empty!');
        }
        $this->inputKey = $privateKey;
        $this->passphrase = $passphrase;
    }

    public function __destruct()
    {
        if (is_resource($this->resource)) {
            openssl_free_key($this->resource);
        }
    }

    private function loadKey()
    {
        if (!$this->isLoad) {
            $this->resource = openssl_pkey_get_private($this->inputKey, $this->passphrase);
            if ($this->resource === false)
                $this->keyError = true;
            $this->isLoad = true;
        }
        return $this->resource;
    }

    public function isValid()
    {
        if (!$this->isLoad)
            $this->loadKey();
        return !$this->keyError;
    }

    private function loadDetails()
    {
        if (!$this->isLoadDetails) {
            if ($this->isValid()) {
                $this->keyDetails = openssl_pkey_get_details($this->loadKey());
            }
        }
        return $this->keyDetails;
    }

    public function getPublicKey(): string
    {
        return $this->loadDetails()['key'] ?? '';
    }

    public function encrypt($data)
    {
        $data = json_encode($data);
        openssl_private_encrypt($data, $crypted, $this->loadKey());
        return $crypted;
    }
}