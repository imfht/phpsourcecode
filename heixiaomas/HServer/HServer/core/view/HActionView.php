<?php
/**
 * Created by PhpStorm.
 * User: hxm
 * Date: 2019/2/20
 * Time: 17:10
 */

namespace HServer\core\view;

use HServer\core\db\HServerDB;

use HServer\core\http\Request;
use HServer\core\http\Response;

require_once __DIR__ . '/../../../vendor/smarty/Smarty.class.php';


class HActionView extends HServerDB
{

    /**
     * @var Response
     */
    protected $Response;

    /**
     * @var Request
     */
    protected $Request;


    protected $view;

    /**
     * HActionView constructor.
     * @param $view
     */
    public function __construct()
    {
        parent::__construct();
        $this->view = new \Smarty();
    }


    /**
     * @param mixed $Response
     */
    public function setResponse($Response)
    {
        $this->Response = $Response;
    }

    /**
     * @param mixed $Request
     */
    public function setRequest(Request $Request)
    {
        $this->Request = $Request;
    }


    protected function assign($key, $value)
    {
        $this->view->assign($key, $value);
    }

    protected function fetch($tpl, $path = "app/view")
    {
        if ($path != "app/view") {
            $path = "app/view" . $path;
        }
        $this->view->setTemplateDir($path);
        return $this->view->fetch($tpl);

    }


}