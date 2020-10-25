<?php
/**
 * Author: liasica
 * CreateTime: 16/1/28 11:10
 * Filename: Radom.php
 * PhpStorm: LiasicaAPI
 */
namespace liasica\helpers;
class Radom
{
  /**
   * 生成随机字符串只包含大小写字母以及数字
   *
   * @param string $length 长度
   * @param bool   $upper  包含大写字母
   * @param bool   $lower  包含小写字母
   * @param null   $symbol 其他符号
   * @param bool   $repeat 是否重复
   *
   * @return null|string
   */
  public function RadomChars($length, $upper = true, $lower = true, $symbol = null, $repeat = true)
  {
    $str     = null;
    $strPol  = '0123456789';
    $letters = 'abcdefghijklmnopqrstuvwxyz';
    $upper == true && $strPol .= strtoupper($letters);
    $lower == true && $strPol .= $letters;
    $symbol != null && $strPol .= $symbol;
    $str_arr = str_split($strPol);
    $max     = count($str_arr) - 1;
    $str     = null;
    for ($i = 0; $i < $length; $i++)
    {
      $radom = mt_rand(0, $max);
      $str .= $str_arr[$radom];
      if (!$repeat)
      {
        unset($str_arr[$radom]);
      }
    }

    return $str;
  }

  /**
   * 抽奖概率算法
   *
   * @param $proArr
   * @eg    $proArr (奖项ID => 数量) [1 => 1, 2 => 10, 3 => 40] ...
   *
   * @link  http://blog.csdn.net/leeyisoft/article/details/8226036
   * @return bool|int|string
   */
  public function lottery($proArr)
  {
    $result = false;
    // 概率数组的总概率精度
    $proSum = array_sum($proArr);
    // 当总数大于0的时候, 当等于0的时候始终未中奖
    if ($proSum > 0)
    {
      //概率数组循环
      foreach ($proArr as $key => $proCur)
      {
        $randNum = mt_rand(1, $proSum);
        if ($randNum <= $proCur)
        {
          $result = $key;
          break;
        }
        else
        {
          $proSum -= $proCur;
        }
      }
      unset ($proArr);
    }

    return $result;
  }
}