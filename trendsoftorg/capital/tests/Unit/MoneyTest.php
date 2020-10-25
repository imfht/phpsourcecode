<?php

/*
 * This file is part of the trendsoft/capital.
 * (c) jabber <2898117012@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tests\Unit;

use Capital\Money;
use Tests\TestCase;

class MoneyTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testStringMoney()
    {
        new Money('bs');
    }

    public function testFloatMoney()
    {
        $money = new Money(999.02);
        $this->assertInstanceOf(Money::class, $money);
    }

    public function testFloatMoney2()
    {
        $money = new Money('999.02');
        $this->assertInstanceOf(Money::class, $money);
    }

    public function testIntMoney()
    {
        $money = new Money(999);
        $this->assertInstanceOf(Money::class, $money);
    }

    public function testIntMoney2()
    {
        $money = new Money('999');
        $this->assertInstanceOf(Money::class, $money);
    }
}
