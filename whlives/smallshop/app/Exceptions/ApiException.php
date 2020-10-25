<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/22
 * Time: 2:22 PM
 */

namespace App\Exceptions;

class ApiException extends \Exception
{
    function _construct($msg = '')
    {
        parent::_construct($msg);
    }
}
