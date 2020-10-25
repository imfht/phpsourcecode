<?php
namespace Modules\Form\Controllers;

use Core\Config;
use Modules\Form\Library\ValidateCode;
use Core\Mvc\Controller;
use Core\Db\Query;
use Modules\Form\Forms\FormInit;

class IndexController extends Controller
{
    public function validateCodeAction()
    {
        $this->variables = array(
            '#templates' => 'validate',
        );
        $validate = new ValidateCode();
        $validate->doimg();
        $this->session->set('code', $validate->getCode());
    }

    public function cxselectAction(){
        extract($this->variables['router_params']);
        $data = FormInit::getFieldInfo();
        $this->variables['#templates'] = 'json';
        $this->variables['data'] = $data;
    }

    public function chosenSourceAction()
    {
        extract($this->variables['router_params']);
        $autoSourceList = Config::cache('autoSource');
        if(!$this->request->isAjax() || !isset($autuSourceList[$id])){
            //return $this->notFount();
        }
        $params = $this->request->getQuery();
        $word = $params['q'];
        $autoSource = $autoSourceList[$id];
        $query = array(
            'from' => array('id' => $autoSource['from']),
            'limit' => 15,
        );
        if(isset($autoSource['callable']) && function_exists($autoSource['callable'])){
            $query = array_merge($query,$autoSource['callable']($this->variables['router_params']));
        }else{
            $conditionsType = isset($autoSource['conditionsType']) ? $autoSource['conditionsType'] : 'andWhere';
            if(isset($autoSource['mergeQuery'])){
                $query = array_merge($query,$autoSource['mergeQuery']);
            }
            $query[$conditionsType][] = array(
                array(
                    'conditions' => $autoSource['query'],
                    'bind' => array('word' => $word)
                )
            );
        }
        if(isset($autoSource['order'])){
            $query['order'] = $autoSource['order'];
        }
        $data = Query::find($query);
        $output = array(
            'total_count' => count($data),
            'incomplete_results' => false,
            'items' => array()
        );
        foreach ($data as $item){
            $output['items'][] = array(
                'id' => $item->{$autoSource['id']},
                'name' => $item->{$autoSource['name']}
            );
        }
        $this->variables['#templates'] = 'json';
        $this->variables['data'] = $output;
    }
}
