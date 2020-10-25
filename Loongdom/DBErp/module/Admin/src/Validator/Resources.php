<?php
/**
 * DBERP 进销存系统
 *
 * ==========================================================================
 * @link      http://www.dberp.net/
 * @copyright 北京珑大钜商科技有限公司，并保留所有权利。
 * @license   http://www.dberp.net/license.html License
 * ==========================================================================
 *
 * @author    静静的风 <baron@loongdom.cn>
 *
 */

namespace Admin\Validator;

final class Resources
{

    private function __construct()
    {
    }

    public static function getBasePath()
    {
        return __DIR__ . '/languages/';
    }

    public static function getPatternForCaptcha()
    {
        return '%s/Zend_Captcha.php';
    }

    public static function getPatternForValidator()
    {
        return '%s/Zend_Validate.php';
    }
}
