<?php
/**
 * Created by PhpStorm.
 * User: man0sions
 * Date: 16/7/26
 * Time: 上午10:49
 */
namespace LuciferP\TinyMvc\app;

use LuciferP\Base\Instance;


/**
 * Class Application
 * @package LuciferP\TinyMvc\app
 * @author Luficer.p <81434146@qq.com>
 */
class Application extends Instance
{
    protected static $instance;

    private $application_helper;



    public function run()
    {
        $this->init();

        $this->application_helper->runRouter();

    }
    private function init()
    {
        $this->application_helper = ApplicationHelper::instance();
        $this->application_helper->init();
    }



}
