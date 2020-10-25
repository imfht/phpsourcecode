<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Contrib\Shop;

use \Cute\Utility\Word;
use \Cute\Contrib\Shop\Currency;


/**
 * 金额（人民币）
 */
class Amount
{
    protected $currency = null;
    protected $integral = 0;    //元 x1.0
    protected $millesimal = 0;  //厘 x0.001

    public function __construct($value, Currency $currency = null)
    {
        $this->setValue($value);
        if (is_null($currency)) {
            $currency = Currency::getInstance();
        }
        $this->currency = $currency;
    }

    public function setValue($value)
    {
        $this->integral = intval($value);
        $millesimal = floatval($value) * 1000 % 1000;
        $this->millesimal = intval(round($millesimal));
        if ($this->millesimal === 1000) { //可能四舍五入后刚好进位
            $this->integral += 1;
            $this->millesimal = 0;
        }
    }

    public function format($pattern = null)
    {
        $value = $this->getValue();
        $decimals = $this->getCurrencyDec();
        if (strtoupper($pattern) === '%L') {
            return intval($value * pow(10, $decimals));
        } else {
            if (is_null($pattern)) {
                $pattern = '%.' . $decimals . 'f';
            }
            return sprintf($pattern, $value);
        }
    }

    public function getValue()
    {
        return $this->integral + $this->millesimal / 1000;
    }

    public function getCurrencyDec()
    {
        return $this->currency->getDecimals();
    }

    /**
     * 货币转换
     */
    public function toCurrency($code = 'CNY')
    {
        if ($this->currency->getCode() === $code) {
            return $this;
        }
        $currency = Currency::getInstance($code);
        $rate = $this->currency->toRate($currency);
        $value = $this->getValue() * $rate;
        return new self($value, $currency);
    }

    /**
     * 大写金额
     */
    public function toCapital()
    {
        if ($this->currency->getCode() !== 'CNY') {
            return;
        }
        $result = Word::spell($this->integral, true);
        $result .= '圆';
        $percent = intval($this->millesimal / 10);
        if ($percent > 0) {
            $dime = intval($percent / 10);
            $result .= Word::num2char($dime, true) . '角';
            if ($cent = intval($percent % 10)) {
                $result .= Word::num2char($caps, true) . '分';
            }
        } else {
            $result .= '整';
        }
        return $result;
    }

    public function getCurrencyCode()
    {
        return $this->currency->getCode();
    }

    public function getCurrencyNum()
    {
        return $this->currency->getNumeric();
    }
}
