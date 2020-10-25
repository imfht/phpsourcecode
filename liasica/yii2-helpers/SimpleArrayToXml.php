<?php
/**
 * Author: liasica
 * CreateTime: 16/1/5 13:00
 * Filename: SimpleArrayToXml.php
 * PhpStorm: yii2-helpers
 */
namespace liasica\helpers;
class SimpleArrayToXml
{
  private $arr;

  /**
   * 数组转为简单XML
   * array convert to simple xml
   * SimpleArrayToXml constructor.
   *
   * @param $arr
   */
  public function __construct($arr)
  {
    $this->arr = $arr;
  }

  /**
   * @return string
   */
  public function buildXML()
  {
    $arr = $this->arr;
    $xml = "<xml>";
    foreach ($arr as $key => $val)
    {
      if (is_numeric($val))
      {
        $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
      }
      else
      {
        $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
      }
      //$xml .= "<" . $key . ">" . $val . "</" . $key . ">";
    }
    $xml .= "</xml>";

    return $xml;
  }
}