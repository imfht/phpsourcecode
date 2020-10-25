<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-6
 * Time: 下午6:28.
 */

namespace MiotApi\Exception;

use Exception;

class SpecificationErrorException extends Exception
{
    public function __toString()
    {
        return __CLASS__.": [{$this->code}]: {$this->message}\n";
    }
}
