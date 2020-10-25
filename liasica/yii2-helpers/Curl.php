<?php
/**
 * Author: liasica
 * CreateTime: 16/1/5 13:18
 * Filename: Curl.php
 * PhpStorm: yii2-helpers
 */
namespace liasica\helpers;
class Curl
{
  private $url;
  private $data;
  private $certs;
  private $aHeader;
  private $second = 30;

  /**
   * Curl constructor.
   *
   * @param $url
   */
  public function __construct($url)
  {
    $this->url = $url;
  }

  /**
   * @param $data
   *
   * @return $this
   */
  public function setData($data)
  {
    $this->data = $data;

    return $this;
  }

  /**
   * @param $certs
   *
   * @return $this
   */
  public function setCerts($certs)
  {
    $this->certs = $certs;

    return $this;
  }

  /**
   * @param $aHeader
   *
   * @return $this
   */
  public function setaHeader($aHeader)
  {
    $this->aHeader = $aHeader;

    return $this;
  }

  /**
   * @param $second
   *
   * @return $this
   */
  public function setSecond($second)
  {
    $this->second = $second;

    return $this;
  }

  /**
   * 证书post
   *
   * @return array|bool
   */
  public function postSSL()
  {
    $ch = curl_init();
    //超时时间
    curl_setopt($ch, CURLOPT_TIMEOUT, $this->second);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //这里设置代理，如果有的话
    //curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
    //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
    curl_setopt($ch, CURLOPT_URL, $this->url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    // 判断证书是否存在
    if (count($this->certs) < 1 || !isset($this->certs[CURLOPT_SSLCERT]) || !is_file($this->certs[CURLOPT_SSLCERT]))
    {
      return false;
    }
    //以下两种方式需选择一种
    //第一种方法，cert 与 key 分别属于两个.pem文件
    if (count($this->certs) > 1 && is_file($this->certs[CURLOPT_SSLKEY]))
    {
      //默认格式为PEM，可以注释
      curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
      curl_setopt($ch, CURLOPT_SSLCERT, $this->certs[CURLOPT_SSLCERT]);
      //默认格式为PEM，可以注释
      curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
      curl_setopt($ch, CURLOPT_SSLKEY, $this->certs[CURLOPT_SSLKEY]);
    }
    elseif (count($this->certs) == 1)
    {
      //第二种方式，两个文件合成一个.pem文件
      curl_setopt($ch, CURLOPT_SSLCERT, $this->certs[CURLOPT_SSLCERT]);
    }
    else
    {
      return false;
    }
    if (count($this->aHeader) >= 1)
    {
      curl_setopt($ch, CURLOPT_HTTPHEADER, $this->aHeader);
    }
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
    $data   = curl_exec($ch);
    $output = [
      'data'  => $data,
      'error' => null,
    ];
    if ($data)
    {
      curl_close($ch);
      $output['data'] = (array) simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);

      return $output;
    }
    else
    {
      $error           = curl_errno($ch);
      $output['error'] = $error;
      curl_close($ch);

      return $output;
    }
  }

  /**
   * get请求
   *
   * @return mixed
   */
  public function Get()
  {
    // curl 初始化
    $ch = curl_init();
    // 设置选项
    curl_setopt($ch, CURLOPT_URL, $this->url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    // header
    if (count($this->aHeader) >= 1)
    {
      curl_setopt($ch, CURLOPT_HTTPHEADER, $this->aHeader);
    }
    //超时时间
    curl_setopt($ch, CURLOPT_TIMEOUT, $this->second);
    //执行并获取HTML文档内容
    $output = curl_exec($ch);
    //释放curl句柄
    curl_close($ch);

    return $output;
  }

  /**
   * POST
   *
   * @return mixed
   */
  public function Post()
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
    // header
    if (count($this->aHeader) >= 1)
    {
      curl_setopt($ch, CURLOPT_HTTPHEADER, $this->aHeader);
    }
    //超时时间
    curl_setopt($ch, CURLOPT_TIMEOUT, $this->second);
    $return = curl_exec($ch);
    curl_close($ch);

    return $return;
  }

}