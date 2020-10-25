<?php
/**
 * YanPHP
 * User: weilongjiang(江炜隆)<william@jwlchina.cn>
 * Date: 2017/10/3
 * Time: 14:50
 */

namespace TestNamespace;


use PHPUnit\Framework\BaseTestListener;
use PHPUnit\Framework\TestSuite;

class TestListener extends BaseTestListener
{
    public function startTestSuite(TestSuite $suite)
    {
        if (strpos($suite->getName(),"Function") !== false ) {
            // Bootstrap integration tests
            $dirname = dirname(__FILE__);
            require_once $dirname."/../vendor/autoload.php";

            require_once $dirname."/../System/Yan/Common/Functions.php";
        }
    }
}