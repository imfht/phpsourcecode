<?php

/*
 * This file is part of the trendsoft/capital.
 * (c) jabber <2898117012@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tests\Feature;

use Capital\Money;
use Tests\TestCase;

class IntTest extends TestCase
{
    public function testInt()
    {
        $this->assertEquals(( new Money(0) )->toCapital(), '零元');
        $this->assertEquals(( new Money(1) )->toCapital(), '壹元');
        $this->assertEquals(( new Money(10) )->toCapital(), '壹拾元');
        $this->assertEquals(( new Money(20) )->toCapital(), '贰拾元');
        $this->assertEquals(( new Money(99) )->toCapital(), '玖拾玖元');
        $this->assertEquals(( new Money(100) )->toCapital(), '壹佰元');
        $this->assertEquals(( new Money(101) )->toCapital(), '壹佰零壹元');
        $this->assertEquals(( new Money(110) )->toCapital(), '壹佰壹拾元');
        $this->assertEquals(( new Money(120) )->toCapital(), '壹佰贰拾元');
        $this->assertEquals(( new Money(210) )->toCapital(), '贰佰壹拾元');
        $this->assertEquals(( new Money(200) )->toCapital(), '贰佰元');
        $this->assertEquals(( new Money(999) )->toCapital(), '玖佰玖拾玖元');
        $this->assertEquals(( new Money(1000) )->toCapital(), '壹仟元');
        $this->assertEquals(( new Money(1001) )->toCapital(), '壹仟零壹元');
        $this->assertEquals(( new Money(1010) )->toCapital(), '壹仟零壹拾元');
        $this->assertEquals(( new Money(1101) )->toCapital(), '壹仟壹佰零壹元');
        $this->assertEquals(( new Money(1110) )->toCapital(), '壹仟壹佰壹拾元');
        $this->assertEquals(( new Money(2000) )->toCapital(), '贰仟元');
        $this->assertEquals(( new Money(10001) )->toCapital(), '壹万零壹元');
        $this->assertEquals(( new Money(100010) )->toCapital(), '壹拾万零壹拾元');
        $this->assertEquals(( new Money(1000100) )->toCapital(), '壹佰万零壹佰元');
        $this->assertEquals(( new Money(10001000) )->toCapital(), '壹仟万零壹仟元');
        $this->assertEquals(( new Money(1000100100) )->toCapital(), '壹拾亿零壹拾万零壹佰元');
        $this->assertEquals(( new Money(1010000100) )->toCapital(), '壹拾亿零壹仟万零壹佰元');
        $this->assertEquals(( new Money(1234567890) )->toCapital(), '壹拾贰亿叁仟肆佰伍拾陆万柒仟捌佰玖拾元');
        $this->assertEquals(( new Money('0123456789') )->toCapital(), '壹亿贰仟叁佰肆拾伍万陆仟柒佰捌拾玖元');
        $this->assertEquals(( new Money(99999999999) )->toCapital(), '玖佰玖拾玖亿玖仟玖佰玖拾玖万玖仟玖佰玖拾玖元');
    }
}
