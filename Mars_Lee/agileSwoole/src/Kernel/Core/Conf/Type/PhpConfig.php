<?php

namespace Kernel\Core\Conf\Type;


use Kernel\Core\Conf\IConfig;

class PhpConfig implements IConfig
{
        public function load(string $filename) : array
        {
                $config = require $filename;
                $config = (1 === $config) ? [] : $config;
                return $config ?: [];
        }

        public function supports(string $filename) : bool
        {
                return (bool) preg_match('#\.php(\.dist)?$#', $filename);
        }
}
