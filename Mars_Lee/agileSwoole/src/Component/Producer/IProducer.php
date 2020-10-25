<?php

namespace Component\Producer;


interface IProducer
{
        public function addProducer($controller, string $method, array $args = []) : IProducer;
        public function addAfter(\Closure $closure): IProducer;
        public function addBefore(\Closure $closure):IProducer;
        public function run();
        public function getProcessId() : int;
}