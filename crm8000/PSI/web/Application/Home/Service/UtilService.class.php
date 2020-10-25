<?php

namespace Home\Service;

require_once __DIR__ . '/../Common/Money/Money.php';

use Capital\Money;

/**
 * 助手Service
 *
 * @author 李静波
 */
class UtilService
{
  /**
   * 金额转换成中文大写汉字
   */
  public function moneyToCap($m)
  {
    return (new Money($m))->toCapital();
  }

  public function downloadFile($fileNameWithPath, $fileChineseName)
  {
    if (!file_exists($fileNameWithPath)) {
      die("文件不存在");
    }
    //设置头信息
    //声明浏览器输出的是字节流
    header("Content-Type: application/octet-stream");
    //声明浏览器返回大小是按字节进行计算
    header("Accept-Ranges:bytes");
    //告诉浏览器文件的总大小
    $fileSize = filesize($fileNameWithPath);
    header("Content-Length:" . $fileSize);
    //声明下载文件的名称
    $cn = urlencode($fileChineseName);
    header("Content-Disposition:attachment;filename={$cn}"); //声明作为附件处理和下载后文件的名称
    //获取文件内容
    $handle = fopen($fileNameWithPath, "rb"); //二进制文件用‘rb’模式读取
    $readBuffer = 1024;
    while (!feof($handle)) { //循环到文件末尾 规定每次读取（向浏览器输出为$readBuffer设置的字节数）
      echo fread($handle, $readBuffer);
    }
    fclose($handle); //关闭文件句柄
  }
}
