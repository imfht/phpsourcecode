<?php


namespace Kernel\Core\Di;


use Psr\Container\ContainerInterface;

Interface IContainer extends ContainerInterface
{
        public function get($id);

        public function has($id);

        public function build($className);

        public function alias(string $key, $class);

        public function singleton(string $name, string $class, string $alisa = '');
}