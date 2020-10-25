<?php
/**
 * YanPHP
 * User: weilongjiang(江炜隆)<william@jwlchina.cn>
 * Date: 2017/10/6
 * Time: 14:05
 */

namespace TestNamespace;


use Yan\Core\Session;

class SessionTest extends BaseTestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        Session::initialize();
    }

    public function testSetAndGet()
    {
        Session::set('test_key', 'value');
        $this->assertEquals('value', Session::get('test_key'));
        $this->assertEquals('value',Session::get('not_exist_key','value'));
    }

}