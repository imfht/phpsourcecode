<?php
/**
 * Author: liasica
 * CreateTime: 16/1/5 16:08
 * Filename: Unicode.php
 * PhpStorm: yii2-helpers
 */
namespace liasica\helpers;
class Unicode
{
  private $encodeStr; // 转义后的Unicode编码字符
  private $decodeStr; // 未转义的Unicode编码字符

  /**
   * Unicode constructor.
   *
   * @param null $encodeStr
   * @param null $decodeStr
   */
  public function __construct($encodeStr = null, $decodeStr = null)
  {
    $this->encodeStr = $encodeStr;
    $this->decodeStr = $decodeStr;
  }

  /**
   * 把用户输入的文本转义（主要针对特殊符号和emoji表情）
   *
   * @return $this|null|string
   */
  public function encode()
  {
    if (!is_string($this->decodeStr))
    {
      return $this->decodeStr;
    }
    if ($this->decodeStr == null)
    {
      return '';
    }
    $text = json_encode($this->decodeStr); //暴露出unicode
    //将emoji的unicode留下，其他不动，这里的正则比原答案增加了d，因为我发现我很多emoji实际上是\ud开头的，反而暂时没发现有\ue开头。
    $text            = preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i", function ($str)
    {
      return addslashes($str[0]);
    }, $text);
    $this->encodeStr = json_decode($text);

    return $this;
  }

  /**
   * 解码转义后的Unicode
   *
   * @return $this
   */
  public function decode()
  {
    $text            = json_encode($this->encodeStr); //暴露出unicode
    $text            = preg_replace_callback('/\\\\\\\\/i', function ($str)
    {
      return '\\';
    }, $text); //将两条斜杠变成一条，其他不动
    $this->decodeStr = json_decode($text);

    return $this;
  }
}