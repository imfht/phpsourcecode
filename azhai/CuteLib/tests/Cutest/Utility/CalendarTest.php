<?php
namespace Cutest\Utility;

use \PHPUnit_Framework_TestCase as TestCase;
use \Cute\Utility\Calendar;


class CalendarTest extends TestCase
{
    protected $cal = null;

    public function setUp()
    {
        $this->cal = new Calendar();
        $this->cal->setDate(2015, 2, 3);
    }

    public function test01Normal()
    {
        $time = date('H:i', $this->cal->getTimestamp());
        $output = '2015-02-03 星期二 ' . $time . ' +0800';
        $this->assertEquals($output, $this->cal->speak('%F 星期%v %R %z'));
        $this->assertEquals(6, $this->cal->getBirthAnimalIndex());
        $this->assertEquals(0, $this->cal->getHoroscopeIndex());
    }
}
