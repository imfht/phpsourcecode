<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2017/1/14
 * Time: 9:53
 */

namespace naples\app\SysNaples\controller;


use naples\lib\base\Controller;
use naples\lib\Factory;

class Ueditor extends Controller
{
    const DIR_UE=PATH_EXTEND.'/ueditor';

    function __construct()
    {
        config('debug',false);
    }

    function index($id='default'){
        define('UEDITOR_PREFIX',\Yuri2::getHttpType().'://'.\Yuri2::getHost().'/');
        define('UEDITOR_UPLOAD_PATH',ltrim(Factory::getRoute()->getPathFix(),'/').'/html/ueditor/upload/'.$id);
        require self::DIR_UE.'/controller.php';
    }
}