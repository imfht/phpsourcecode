<?php

declare(strict_types=1);

namespace TencentAI\Tests;

use PHPUnit\Framework\TestCase;
use TencentAI\TencentAI;

class TencentAITestCase extends TestCase
{
    /**
     * @var TencentAI
     */
    private static $ai;

    public static function ai()
    {
        if (!(self::$ai instanceof TencentAI)) {
            $app_id = 1106560031;
            $app_key = 'ZbRY9cf72TbDO0xb';
            self::$ai = TencentAI::getInstance($app_id,
                $app_key, false, 10, 3, true);
        }

        return self::$ai;
    }
}
