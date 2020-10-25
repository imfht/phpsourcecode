<?php
namespace Modules\Search\Controllers;

use Core\Config;
use Core\Mvc\Controller;
use Library\Scsw\Scsw;

class AdminController extends Controller
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
            'for' => 'adminSearch',
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

            if($page > 1){
                $title = $word.'搜索结果 第'.$page.'页';
            }else{
                $title = $word.'搜索结果';
            }
            $this->variables += array(
                'title' => $title,
                'description' => '',
                'breadcrumb' => array(
                    'admin' => array(
                        'href' => array(
                            'for' => 'adminIndex',
                        ),
                        'name' => '控制台',
                    ),
                    'nodeList' => array(
                        'name' => '搜索',
                    ),
                ),
                'word' => $word,
                'type' => $type,
                'page' => $page,
                'content' => array(
                    '#templates' => array(
                        'adminSearch',
                        'adminSearch-'.$type
                    ),
                    '#module' => $searchEngine[$type]['module'],
                    'data' => $data
                )
            );
        }else{
            return $this->notFount();
        }
    }
}
