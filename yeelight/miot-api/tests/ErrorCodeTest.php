<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-14
 * Time: 下午5:28.
 */
use MiotApi\Api\ErrorCode;

class ErrorCodeTest extends PHPUnit_Framework_TestCase
{
    public function testGetMiotErrorMessage()
    {
        $this->assertEquals(' HttpCodeMessage (404): Not Found ErrorMessage : Unknow Error ErrorMessage : Property不存在', ErrorCode::getMiotErrorMessage('-704040003'));
    }
}
