<?php
/*
 * YanPHP
 * User: weilongjiang(江炜隆)<william@jwlchina.cn>
 * Date: 2017/8/27
 * Time: 18:22
 */

namespace Yan\Core\Compo;


interface ResultInterface extends \JsonSerializable
{
    function getCode();

    function getMessage();
}