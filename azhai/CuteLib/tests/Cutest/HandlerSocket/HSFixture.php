<?php
namespace Cutest\HandlerSocket;

use \PHPUnit_Framework_TestCase as TestCase;
use \Cute\ORM\HandlerSocket;


class HSFixture extends TestCase
{
    // 只实例化 hs 一次，供测试的清理和基境读取使用。
    protected static $hs = null;

    public static function setUpBeforeClass()
    {
        if (self::$hs == null) {
            $table_name = $GLOBALS['DB_TBLPRE'] . $GLOBALS['DB_TABLE'];
            self::$hs = new HandlerSocket();
            self::$hs->open($GLOBALS['DB_DBNAME'], $table_name, $GLOBALS['DB_FIELDS']);
        }
        self::clearRows();
    }

    public static function clearRows($count = 8)
    {
        self::$hs->truncate(range(1, $count));
    }

    public static function tearDownAfterClass()
    {
        self::clearRows();
        self::insertRows(1);
    }

    protected static function insertRows($count = 8)
    {
        $names = explode(',', $GLOBALS['DB_FIELDS']);
        $rows = self::provider();
        for ($i = 0; $i < $count; $i++) {
            self::$hs->insert(array_combine($names, $rows[ $i ]));
        }
    }

    public static function provider()
    {
        return [
            [1, '未分类', 'uncategorized', 0],
            [2, 'PHP语言', 'php', 1],
            [3, 'Python', 'python', 0],
            [4, 'MySQL', 'mysql', 1],
            [5, 'Nginx', 'nginx', 0],
            [6, 'Linux系统', 'linux', 0],
            [7, 'Android', 'android', 1],
            [8, 'iOS', 'ios', 1],
        ];
    }
}

