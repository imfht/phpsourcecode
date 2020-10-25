<?php
/**
 * User: Gao
 * Date: 2020/1/7
 * Time: 23:35
 */

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class JsonDecodeExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('json_decode', [$this, 'doJsonDecode']),
        ];
    }

    public function doJsonDecode($json)
    {
        return json_decode($json, true);
    }
}