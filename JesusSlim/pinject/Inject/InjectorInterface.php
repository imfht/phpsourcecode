<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 16/7/25
 * Time: 下午5:42
 */

namespace Inject;


interface InjectorInterface
{

    public function map($k,$v,$c);

    public function get($k);

    public function produce($k,$params = array());

    public function call(\Closure $c,$p = array());

    public function callInClass($c,$a,$p = array());
}