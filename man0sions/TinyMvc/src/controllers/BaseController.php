<?php
/**
 * Created by PhpStorm.
 * @author Luficer.p <81434146@qq.com>
 * Date: 16/11/3
 * Time: 下午1:04
 */

namespace LuciferP\TinyMvc\controllers;


use LuciferP\Controller\Controller;
use LuciferP\TinyMvc\base\Decorators;

abstract class BaseController extends Controller implements Decorators
{
    protected $base_dir = BASE_DIR;
    protected $toast = [];

    public $page_title = 'Tiny Mvc';

    public function beforeAction($action)
    {
        // TODO: Implement beforeAction() method.
    }

    public function afterAction($action)
    {
        // TODO: Implement afterAction() method.
    }

    protected function setToast($message='系统提示',$url=''){
        $this->toast = [
            'message' => $message,
            'url' => $url
        ];
    }

    /**
     * @param int $code
     * @param string $message
     * @throws \Exception
     */
    protected function renderError($code=404,$message=''){
        $error_page = $this->base_dir . "/views/error/error.php";
        $this->response->status($code)->type('text/html')->render($error_page, ['message' => $message, 'code' => $code]);

    }
}