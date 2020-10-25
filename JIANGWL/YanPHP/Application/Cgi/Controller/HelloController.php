<?php
/**
 * YanPHP
 * User: weilongjiang(江炜隆)<willliam@jwlchina.cn>
 * Date: 2017/9/4
 * Time: 19:26
 */

namespace App\Cgi\Controller;

use Yan\Core\Compo\ResultInterface;
use Yan\Core\Controller;

class HelloController extends Controller
{

    public function index(): ResultInterface
    {
        return $this->succ('hello world');
    }
}