<?php
namespace Home\Service;

require __DIR__ . '/../Common/Pinyin/pinyin.php';

/**
 * 拼音Service
 *
 * @author 李静波
 */
class PinyinService
{
  public function toPY($s)
  {
    return strtoupper(pinyin($s, "first", ""));
  }
}
