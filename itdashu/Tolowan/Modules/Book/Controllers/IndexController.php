<?php
namespace Modules\Book\Controllers;

use Core\Mvc\Controller;
use Library\Scsw\Scsw;
use Core\Config;

class IndexController extends Controller
{
    public function chosenSourceAction()
    {
        extract($this->variables['router_params']);
        if(!$this->request->isAjax()){
            //return $this->notFount();
        }
        $params = $this->request->getQuery();
        $word = trim($params['q']);
        $nodeEntity = $this->entityManager->get('node');
        $data = $nodeEntity->find(array(
            'match' => array(
                array(
                    'conditions' => 'MATCH(%title%) AGAINST(:title:)',
                    'bind' => array('title' => Scsw::toString('phalcon'))
                ),
            ),
            'all' => true,
            'andWhere' => array(
                array(
                    'conditions' => '%contentModel% = :contentModel:',
                    'bind' => array(
                        'contentModel' => 'book'
                    )
                )
            ),
        ));
        $output = array(
            'total_count' => count($data),
            'incomplete_results' => false,
            'items' => array()
        );
        foreach ($data as $item){
            $output['items'][] = array(
                'id' => $item->id,
                'name' => $item->title->value
            );
        }
        $this->variables['#templates'] = 'json';
        $this->variables['data'] = $output;
    }
}
