<?php
namespace Cutest\Utility;

use \PHPUnit_Framework_TestCase as TestCase;
use \Cute\Utility\Inflect;


class InflectTest extends TestCase
{
    protected $samples = [
        'simpleTest'      => 'simple_test',
        'easy'            => 'easy',
        'HTML'            => 'html',
        'simpleXML'       => 'simple_xml',
        'PDFLoad'         => 'pdf_load',
        'startMIDDLELast' => 'start_middle_last',
        'AString'         => 'a_string',
        'Some4Numbers234' => 'some4_numbers234',
        'TEST123String'   => 'test123_string',
    ];

    public function setUp()
    {
    }

    public function test01Flatten()
    {
        foreach ($this->samples as $key => $value) {
            $this->assertEquals($value, Inflect::flatten($key));
        }
    }
}
