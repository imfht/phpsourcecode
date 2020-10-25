<?php


namespace Kernel\Core\IComponent;


interface IConnection
{
    public function hashCode() : string ;

    public function free();
}