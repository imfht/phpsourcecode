<?php
namespace Modules\Search\Controllers;

use Core\Config;
use Core\Mvc\Controller;
use Library\Scsw\Scsw;

class IndexController extends Controller
{

    public function indexAction()
    {
        extract($this->variables['router_params']);
        $searchConfig = Config::get('m.search.config');
        $query = $this->request->getPost();
        if (!isset($query['type'])) {
            $query['type'] = isset($searchConfig['default']) ? $searchConfig['default'] : 'node';
        }
        if (!isset($query['word'])) {
            return $this->notFount();
        }
        return $this->moved(array(
            'for' => 'search',
            'type' => $query['type'],
            'word' => urlencode($query['word']),
            'page' => 1
        ));
    }

    public function searchAction()
    {
        extract($this->variables['router_params']);
        $searchEngine = Config::get('m.search.engine');
        if(isset($searchEngine[$type])){
            $data = call_user_func($searchEngine[$type]['callable'],$this->variables['router_params']);
            $this->variables += array(
                '#templates' => array(
                    'search',
                    'search-'.$type
                ),
                '#module' => $searchEngine[$type]['module'],
                'data' => $data,
                'title' => $word.' 的搜索结果',
                'description' => '',
                'breadcrumb' => array(
                    'admin' => array(
                        'href' => array(
                            'for' => 'index',
                        ),
                        'name' => '首页',
                    ),
                    'nodeList' => array(
                        'name' => '搜索',
                    ),
                ),
                'type' => $type,
                'page' => $page,
            );
        }else{
            return $this->notFount();
        }
    }
}
