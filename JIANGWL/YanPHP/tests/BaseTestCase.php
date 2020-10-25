<?php
/**
 * YanPHP
 * User: weilongjiang(江炜隆)<william@jwlchina.cn>
 * Date: 2017/10/2
 * Time: 13:13
 */

namespace TestNamespace;

use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $systemPath = '../System';

        $cachePath = '../Application/Cgi/cache';

        defined('SELF') or define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

        defined('SYS_PATH') or define('SYS_PATH', rtrim($systemPath, '/\\'));

        defined('BASE_PATH') or define('BASE_PATH', dirname(__FILE__).'/../Application/Cgi');

        defined('CACHE_PATH') or define('CACHE_PATH', $cachePath);

    }
}