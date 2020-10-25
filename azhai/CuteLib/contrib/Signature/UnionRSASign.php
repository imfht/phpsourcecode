<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Contrib\Signature;

use \Cute\Contrib\Signature\ISignature;


/**
 * RSA加密变形、手机银联V3专用
 */
class UnionRSASign extends RSASign
{
    public $digest = 'sha1';        // 摘要算法函数
    protected $pri_cert_pass = '';  // 私钥密码
    protected $pub_cert_no = '';    // 公钥ID

    public function __construct($my_prikey_path, $its_pubkey_path = false, $pri_cert_pass = null)
    {
        parent::__construct($my_prikey_path, $its_pubkey_path);
        $this->pri_cert_pass = $pri_cert_pass;
    }

    public function setPubCertNO($pub_cert_no)
    {
        $this->pub_cert_no = $pub_cert_no;
        return $this;
    }

    public function addFields(&$payment)
    {
        $payment->setField('cert_pass', $this->getCertID());
        $payment->setField('sign_method', $this->getName());
    }

    public function getCertID()
    {
        $pkcs12 = file_get_contents($this->my_prikey_path);
        openssl_pkcs12_read($pkcs12, $pem, $this->pri_cert_pass);
        return $this->readX509SN($pem['cert']);
    }

    protected function readX509SN($content)
    {
        $cert = openssl_x509_parse($content);
        return $cert['serialNumber'];
    }

    public function getName()
    {
        return '01'; //手机银联V3使用01
    }

    public function getPrivateKey()
    {
        $pem = $this->readPkcs12($this->my_prikey_path, $this->pri_cert_pass);
        return $pem['pkey'];
    }

    protected function readPkcs12($key_path, $cert_pass)
    {
        $content = file_get_contents($key_path);
        openssl_pkcs12_read($content, $pem, $cert_pass);
        return $pem;
    }

    public function getPublicKey()
    {
        $cert_files = glob($this->its_pubkey_path);
        foreach ($cert_files as $file) {
            $content = file_get_contents($file);
            $cert_no = $this->readX509SN($content);
            if ($cert_no === $this->pub_cert_no) { //如果证书的ID相同
                return $content;
            }
        }
    }
}
