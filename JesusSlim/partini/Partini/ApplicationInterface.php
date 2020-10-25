<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 16/7/26
 * Time: 下午4:32
 */

namespace Partini;


use Inject\InjectorInterface;

interface ApplicationInterface extends InjectorInterface
{

    public function version();

    public function getConfig($key = null);

}