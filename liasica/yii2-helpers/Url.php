<?php
/**
 * Author: liasica
 * CreateTime: 16/1/28 11:52
 * Filename: Url.php
 * PhpStorm: LiasicaAPI
 */
namespace liasica\helpers;
class Url
{
  private $uri;

  /**
   * Url constructor.
   *
   * @param null $uri
   */
  public function __construct($uri = null)
  {
    if ($uri != null)
    {
      $this->uri = $uri;
    }
  }

  /**
   * @param $uri
   *
   * @return Url
   */
  public static function setUri($uri)
  {
    $model      = new self();
    $model->uri = $uri;

    return $model;
  }

  /**
   * Get real url
   * 获取真实链接
   *
   * @return mixed
   */
  public function realurl()
  {
    $header = get_headers($this->uri, 1);
    if (strpos($header[0], '301') || strpos($header[0], '302'))
    {
      if (is_array($header['Location']))
      {
        return $header['Location'][count($header['Location']) - 1];
      }
      else
      {
        return $header['Location'];
      }
    }
    else
    {
      return $this->uri;
    }
  }
}