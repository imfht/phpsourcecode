<?php
/**
 * Author: liasica
 * CreateTime: 16/1/28 15:52
 * Filename: Time.php
 * PhpStorm: yii2-helpers
 */
namespace liasica\helpers;

use Yii;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;

class Time
{
  /**
   * 格式化时间戳，精确到微秒，x代表微秒
   *
   * @param      $tag
   * @param null $time
   *
   * @return mixed
   * @throws \yii\base\ErrorException
   */
  public function microtime_format($tag, $time = null)
  {
    $time == null && $time = $this->microtime_float();
    try
    {
      list($usec, $sec) = explode('.', $time);
    }
    catch (\Exception $e)
    {
      throw new ErrorException($e->getMessage(), $e->getCode());
    }
    $date = date($tag, $usec);

    return str_replace('x', $sec, $date);
  }

  /**
   * 获取当前时间戳，精确到毫秒
   *
   * @return string|float
   */
  public function microtime_float()
  {
    list($usec, $sec) = explode(" ", microtime());
    if (!in_array('bcmath', get_loaded_extensions()))
    {
      if ($sec < 1)
      {
        $str = explode('.', $sec)[1];
      }
      else
      {
        $str = $sec;
      }

      return $usec . $str;
    }
    else
    {
      return bcadd($usec, $sec, 6);
    }
  }
}