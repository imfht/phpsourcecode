<?php

namespace Home\Service;

require __DIR__ . '/../Common/Money/Money.php';

/**
 * 金额数字转中文大写Service
 *
 * @author 李静波
 */
class MoneyCaptialService
{

  public function toCaptial($m)
  {
    return (new \Capital\Money($m))->toCapital();
  }
}
