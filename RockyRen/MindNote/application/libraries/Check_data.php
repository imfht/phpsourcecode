<?php
/**
 * Created by PhpStorm.
 * User: jay
 * Date: 15-5-24
 * Time: 上午10:34
 */
class Check_data{
  /**
   * 检查必要的参数是否都有值
   * @param $need_data
   * need_data 为一个数组
   * @return bool
   */
  public function check_need_data($need_data)
  {
    foreach($need_data as $data)
    {
      if($data == false)
      {
        return false;
      }
    }
    return true;
  }

  /**
   * 检查可选的参数是否其中之一有值
   * @param $optional_data
   * $optional_data 为一个数组
   * @return bool
   */
  public function check_optional_data($optional_data)
  {
    foreach($optional_data as $data)
    {
      if($data != false)
      {
        return true;
      }
    }
    return false;
  }

}