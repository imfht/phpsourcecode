<?php
// +----------------------------------------------------------------------
// | Input.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace App\Core\Registry;

use App\Core\Registry\Exceptions\RegistryException;
use App\Core\Validation\Registry\InputValidator;
use App\Utils\Registry\Sign;
use JsonSerializable;

class Input implements JsonSerializable
{
    public $service;

    public $port;

    public $ip;

    public $register;

    public function __construct($input)
    {
        $validator = new InputValidator();
        if ($validator->validate($input)->valid()) {
            throw new RegistryException($validator->getErrorMessage());
        }

        if (!Sign::verify($input, $input['sign'])) {
            throw new RegistryException('The sign is invalid!');
        }

        $this->service = $input['service'];
        $this->port = $input['port'];
        $this->ip = $input['ip'];
        $this->register = $input['register'];
    }

    public function toArray()
    {
        return [
            'service' => $this->service,
            'ip' => $this->ip,
            'port' => $this->port,
            'weight' => 100,
            'time' => time(),
        ];
    }

    public function jsonSerialize()
    {
        return json_encode([
            'service' => $this->service,
            'ip' => $this->ip,
            'port' => $this->port,
            'weight' => 100,
            'time' => time(),
        ]);
    }


}