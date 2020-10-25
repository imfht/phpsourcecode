<?php

namespace Kernel\Core\Conf;


interface IConfig
{
        public function load(string $filename) : array ;
        public function supports(string $filename) : bool;
}