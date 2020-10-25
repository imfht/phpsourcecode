<?php
namespace Modules\Core\Controllers;

use Core\Config;
use Core\Mvc\Controller;

class IndexController extends Controller
{

    public function indexAction()
    {
        extract($this->variables['router_params']);
        $this->variables += array(
            '#templates' => 'index',
            'title' => $this->variables['coreConfig']->indexTitle,
        );
        $translate = Config::get('translate');
        if ($translate['translate'] && $this->translate->getLanguage() == false) {
            //$this->response->redirect('/'.$this->translate->getBestLanguage());
        }
    }

    public function notFoundAction()
    {
        $this->response->setStatusCode(404, "Not Found");
        $this->variables += array(
            'title' => '404页面',
            'description' => '404页面',
            '#templates' => 'notFound',
        );
    }
}
