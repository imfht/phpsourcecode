<?php
/**
 * DBERP 进销存系统
 *
 * ==========================================================================
 * @link      http://www.dberp.net/
 * @copyright 北京珑大钜商科技有限公司，并保留所有权利。
 * @license   http://www.dberp.net/license.html License
 * ==========================================================================
 *
 * @author    静静的风 <baron@loongdom.cn>
 *
 */

namespace Admin\View\Helper;

use Zend\I18n\View\Helper\CurrencyFormat;

class ErpCurrencyFormatHelper extends CurrencyFormat
{
    public function __invoke(
        $number,
        $pattern = null,
        $currencyCode = null,
        $showDecimals = null,
        $locale = null

    )
    {
        if (null === $locale) {
            $locale = 'zh_CN';
        }
        if (null === $currencyCode) {
            $currencyCode = 'CNY';
        }
        if (null === $showDecimals) {
            $showDecimals = 2;
        }
        if (null === $pattern) {
            $pattern = $this->getCurrencyPattern();
        }

        if($number == 0.0000) return 0;

        return parent::__invoke($number, $currencyCode, $showDecimals, $locale, $pattern);
    }
}