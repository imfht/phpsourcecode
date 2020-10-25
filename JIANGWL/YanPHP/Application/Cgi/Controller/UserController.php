<?php
/**
 * YanPHP
 * User: weilongjiang(江炜隆)<willliam@jwlchina.cn>
 * Date: 2017/9/4
 * Time: 19:26
 */

namespace App\Cgi\Controller;


use App\Cgi\Compo\Result;
use App\Cgi\Model\User;
use Yan\Core\Compo\ResultInterface;
use Yan\Core\Controller;
use Yan\Core\Input;
use Yan\Core\ReturnCode;
use Yan\Core\Session;

class UserController extends Controller
{

    public function index(): ResultInterface
    {
        return $this->succ('succ');
    }

    public function getUser(): ResultInterface
    {
        $userInfo = (new User())->getById(1);
        return new Result(ReturnCode::OK, '', $userInfo->toArray());
    }

    public function session()
    {
        //Session::destroy();
        Session::set('a','b');
        var_dump(Session::get('a'));exit;
    }

    public function csrf(){
        $token = Session::getCsrfToken();
        return $this->succ('',['token'=>$token->getValue()]);
    }
}