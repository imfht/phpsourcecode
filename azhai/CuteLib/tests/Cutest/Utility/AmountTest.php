<?php
namespace Cutest\Utility;

use \PHPUnit_Framework_TestCase as TestCase;
use \Cute\Contrib\Shop\Amount;


class AmountTest extends TestCase
{
    protected $rmb = null;
    protected $usd = null;

    public function setUp()
    {
        $this->rmb = new Amount(120015.30);
        $this->usd = $this->rmb->toCurrency('USD');
    }

    public function test01Code()
    {
        $this->assertEquals('CNY', $this->rmb->getCurrencyCode());
        $this->assertEquals('156', $this->rmb->getCurrencyNum());
        $this->assertEquals('USD', $this->usd->getCurrencyCode());
        $this->assertEquals('840', $this->usd->getCurrencyNum());
    }

    public function test02Capital()
    {
        $output = '拾贰萬零壹拾伍圆叁角';
        $this->assertEquals($output, $this->rmb->toCapital());
    }
}
