<?php
/**
 * YanPHP
 * User: weilongjiang(江炜隆)<william@jwlchina.cn>
 * Date: 2017/10/3
 * Time: 15:16
 */

namespace TestNamespace;


use Yan\Core\Validator;

class ValidatorTest extends BaseTestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        Validator::initialize();
    }


    public function testRequired()
    {
        $this->assertFalse(Validator::validate('testKey', '', 'required', $retMsg));
        $this->assertTrue(Validator::validate('testKey', 'a', 'required', $retMsg));
    }

    public function testInteger()
    {
        $this->assertTrue(Validator::validate('testKey', 123, 'integer', $retMsg));
        $this->assertFalse(Validator::validate('testKey', '123', 'integer', $retMsg));
    }

    public function testNumeric()
    {
        $this->assertTrue(Validator::validate('testKey', '123', 'numeric', $retMsg));
        $this->assertFalse(Validator::validate('testKey', 'abc12', 'numeric', $retMsg));
    }

    public function testFloat()
    {
        $this->assertTrue(Validator::validate('testKey', 123.11, 'float', $retMsg));
    }

    public function testString()
    {
        $this->assertTrue(Validator::validate('testKey', 'string', 'string', $retMsg));
        $this->assertFalse(Validator::validate('testKey', 123, 'string', $retMsg));
    }

    public function testArray()
    {
        $this->assertTrue(Validator::validate('testKey', [1,2,3,'a'=>'b'], 'array', $retMsg));
        $this->assertFalse(Validator::validate('testKey', 'abc', 'array', $retMsg));
    }

    public function testValidIp()
    {
        $this->assertTrue(Validator::validate('testKey', '1.1.1.1', 'ip', $retMsg));
        $this->assertTrue(Validator::validate('testKey', 'FE80::E0:F726:4E58', 'ip', $retMsg));
    }

    public function testJson()
    {
        $this->assertTrue(Validator::validate('testKey', '{"code": 0,"message": "","data": []}', 'json', $retMsg));
        $this->assertFalse(Validator::validate('testKey', '{"code": 0,"message: "","data": []}', 'json', $retMsg));
    }

    public function testEmail()
    {
        $this->assertTrue(Validator::validate('testKey', 'william@jwlchina.cn', 'email', $retMsg));
        $this->assertFalse(Validator::validate('testKey', 'williamjwlchina.cn', 'email', $retMsg));
    }

    public function testDomain()
    {
        $this->assertTrue(Validator::validate('testKey', 'www.jwlchina.cn', 'domain', $retMsg));
        $this->assertTrue(Validator::validate('testKey', 'jwlchina.cn', 'domain', $retMsg));
    }

    public function testStartsWith()
    {
        $this->assertTrue(Validator::validate('testKey', '12345', 'starts_with[12]', $retMsg));
        $this->assertTrue(Validator::validate('testKey', 'abcde', 'starts_with[ab]', $retMsg));
    }

    public function testEndsWith()
    {
        $this->assertTrue(Validator::validate('testKey', '12345', 'ends_with[45]', $retMsg));
        $this->assertTrue(Validator::validate('testKey', 'abcde', 'ends_with[de]', $retMsg));
    }

    public function testBetween()
    {
        $this->assertTrue(Validator::validate('testKey', 5, 'between[1,10]', $retMsg));
        $this->assertTrue(Validator::validate('testKey', 100, 'between[1,100]', $retMsg));
        $this->assertTrue(Validator::validate('testKey', -1, 'between[-1,100]', $retMsg));
        $this->assertFalse(Validator::validate('testKey', -2, 'between[-1,100]', $retMsg));
    }

    public function testMin()
    {
        $this->assertTrue(Validator::validate('testKey', 1, 'min[1]', $retMsg));
        $this->assertTrue(Validator::validate('testKey', 4, 'min[1]', $retMsg));
        $this->assertFalse(Validator::validate('testKey', 0, 'min[1]', $retMsg));
    }

    public function testMax()
    {
        $this->assertTrue(Validator::validate('testKey', 1, 'max[1]', $retMsg));
        $this->assertFalse(Validator::validate('testKey', 4, 'max[1]', $retMsg));
        $this->assertTrue(Validator::validate('testKey', 0, 'max[1]', $retMsg));
    }

    public function testLength()
    {
        $this->assertTrue(Validator::validate('testKey', 1, 'length[1,5]', $retMsg));
        $this->assertTrue(Validator::validate('testKey', '123', 'length[1,5]', $retMsg));
        $this->assertTrue(Validator::validate('testKey', '12345', 'length[1,5]', $retMsg));
        $this->assertFalse(Validator::validate('testKey', '123456', 'length[1,5]', $retMsg));
    }

    public function testEqual()
    {
        $this->assertTrue(Validator::validate('testKey', 1, 'equal[1]', $retMsg));
        $this->assertFalse(Validator::validate('testKey', 4, 'equal[1]', $retMsg));
        $this->assertTrue(Validator::validate('testKey', '1', 'equal[1]', $retMsg));
    }

    public function testRegex()
    {
        $this->assertTrue(Validator::validate('testKey', '1a', 'regex[/[1-9]+[a-z]+/]', $retMsg));
        $this->assertFalse(Validator::validate('testKey', 4, 'regex[/[1-9]+[a-z]+/]', $retMsg));
        $this->assertTrue(Validator::validate('testKey', '112312adshfk', 'regex[/[1-9]+[a-z]+/]', $retMsg));
    }
}