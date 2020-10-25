<?php
/**
 * Created by PhpStorm.
 * @author Luficer.p <81434146@qq.com>
 * Date: 16/11/3
 * Time: 下午12:53
 */

namespace LuciferP\TinyMvc\controllers;


use LuciferP\TinyMvc\base\TinyMvc;
use LuciferP\TinyMvc\models\Users;

class Home extends BaseController
{
    public $page_title = '系统主页';

    public function index()
    {
        TinyMvc::cache()->save('name',['name'=>'zhangsan']);
        TinyMvc::log()->debug("this is home controller".microtime());

        return $this->render(['cahce'=>TinyMvc::cache()->fetch('name')]);
    }
}