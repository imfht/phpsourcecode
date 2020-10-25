<?php
namespace App\Controller;

use Swoole;

class Index extends Swoole\Controller
{
    public function __construct($swoole)
    {
        parent::__construct($swoole);
        /*Swoole::$php->session->start();
        Swoole\Auth::loginRequire();*/
    }

    public function index()
    {
        echo microtime(true);
    }

    public function test()
    {
        $data = model('User')->get(1)->get();

        return json_encode($data);
    }

    /**
     * 网站favicon
     */
    public function favicon()
    {
        $favicon = file_get_contents(WEBPATH . '/public/favicon.ico');
        $this->response->setHeader('Content-Type', 'image/jpeg');
        echo $favicon;
    }
}
