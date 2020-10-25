<?php


namespace  Bluehouseapp\Bundle\CoreBundle\DependencyInjection\Driver;


interface DatabaseDriverInterface
{
    public function load(array $classes);


}
