<?php
namespace Cutest\Widget;

use \PHPUnit_Framework_TestCase as TestCase;
use \Cute\Network\cURL;


class cURLTest extends TestCase
{
    protected static $client = null;

    public static function setUpBeforeClass()
    {
        $base_url = 'https://raw.githubusercontent.com/Mashape/unirest-php';
        self::$client = new cURL($base_url);
    }

    public function test01Post()
    {
        $data = ['x' => rand(0, 100), 'y' => 'yes'];
        $result = self::$client->post('/master/LICENSE', [], $data);
        $this->assertEquals(200, $result->code);
        $this->assertEquals(1099, strlen($result->body));
        $this->assertTrue(starts_with($result->body, 'The MIT License'));
    }
}

