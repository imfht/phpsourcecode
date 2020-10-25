<?php
/**
 * YanPHP
 * User: weilongjiang(江炜隆)<willliam@jwlchina.cn>
 * Date: 2017/9/4
 * Time: 19:26
 */

namespace App\Cgi\Controller;


use App\Cgi\Compo\Result;
use Yan\Core\Compo\ResultInterface;
use Yan\Core\Controller;
use Yan\Core\ReturnCode;

class TestController extends Controller
{

    public function index(): ResultInterface
    {
        return $this->succ('succ');
    }

    public function getUser(): ResultInterface
    {
        return new Result(ReturnCode::OK, '', []);
    }
}