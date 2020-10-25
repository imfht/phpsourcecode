<?php
namespace Modules\Node\Controllers;

use Core\Config;
use Core\Mvc\Controller;
use Modules\Node\Entity\Node;
use Modules\Core\Models\Meta;
use Modules\Node\Library\NodePagination;

class IndexController extends Controller
{

    /**
     * @param $type
     * @param $id
     * @return mixed
     */
    public function indexAction()
    {
        extract($this->variables['router_params']);
        $nodeEntity = $this->entityManager->get('node');
        $nodeContentModelList = $nodeEntity->getContentModelList();
        $data = Node::findFirst($id);
        if (!$data || $data->created > time()) {
            return $this->notFount();
        }

        $nodePagination = [];
        $nodeConfig = Config::get('m.node.config');
        if($nodeConfig['pagination'] != 0 && array_search($data->contentModel,$nodeConfig['open_pagination'])){
            if(isset($page) && $page < 2){
                return $this->notFount();
            }
            $nodePaginationOb = new NodePagination($data->body->value);
            $nodePagination = $nodePaginationOb->cut_str();
        }
        if(!isset($page)){
            $page = 1;
        }

        $this->variables['title'] = $data->getTitle();
        $this->variables += array(
            '#templates' => array(
                'pageNode',
                'pageNode-' . $data->contentModel,
                'pageNode-' . $id,
            ),
            'page' => $page,
            'contentModelList' => $nodeContentModelList,
            'contentModelInfo' => $nodeContentModelList[$data->contentModel],
            'contentModel' => $data->contentModel,
            'nodePagination' => $nodePagination,
            'currentPageIndex' => $page -1,
            'data' => $data,
        );
    }

    public function browseAction()
    {
        extract($this->variables['router_params']);
        $data = $this->request->getPost();
        if(!isset($data['id']) || !$data['id'] || empty($data['id'])){
            $data['id'] = array();
        }
        $data = $data['id'];

        $put = array();
        $output = array();
        foreach ($data as $key){
            $key = intval($key);
            if((is_string($key) || is_numeric($key)) && $key){
                $put[] = 'node_browse_'.$key;
            }
        }
        $id = intval($id);
        $node = false;
        if($id) {
            $node = Node::findFirst();
        }
        if($node && $id){
            $key = 'node_browse_'.$key;
            $browse = Meta::findFirstById($key);
            if(!$browse){
                $browse = new Meta();
                $browse->id = $key;
                $browse->data = 0;
            }else{
                $browse->data = intval($browse->data);
            }
            $browse->data = $browse->data+1;
            $browse->save();
        }
        if(!empty($put)){
            $data = Meta::find(array(
                'conditions' => 'id IN ({id:array})',
                'bind' => array('id' => $put)
            ));

            foreach ($data as $item){
                $info = explode('_',$item->id);
                $idkey = end($info);
                $output[$idkey] = $item->data;
            }
        }
        $this->variables['#templates'] = 'json';
        $this->variables['data'] = $output;
    }

    public function loveAction()
    {
        extract($this->variables['router_params']);
        $data = array(
            'state' => false,
            'notice' => '执行失败',
        );
        $this->variables['#templates'] = 'json';
        $this->variables['data'] = &$data;
        $node = Node::findFirst($id);
        if ($node) {
            $node->love = $node->love + 1;
            if ($node->save()) {
                $data = array(
                    'state' => true,
                    'notice' => '执行成功',
                );
            }
        }
    }

    public function termAction()
    {
        extract($this->variables['router_params']);
        $nodeConfig = Config::get('m.node.config');
        $termEntity = $this->entityManager->get('term');
        $term = $termEntity->findFirst($id, true);
        if (!$term) {
            return $this->notFount();
        }
        $nodeEntity = $this->entityManager->get('node');
        $nodeFields = $nodeEntity->getFields();

        $termFieldList = array();
        foreach ($nodeFields as $fieldName => $field) {
            if (isset($field['field']) && $field['field'] == 'term') {
                $termFieldList[$fieldName] = $fieldName;
            }
        }

        $conditions = array();
        $bind = array();
        foreach ($termFieldList as $field) {
            $conditions[] = '%' . $field . '% = :' . $field . ':';
            $bind[$field] = $id;
        }
        $conditions = implode(' OR ', $conditions);
        $query = array(
            'where' => array(
                array(
                    'conditions' => $conditions,
                    'bind' => $bind,
                ),
            ),
            'group' => '%id%',
            'order' => '%changed% DESC',
            'limit' => isset($nodeConfig['term_number']) && $nodeConfig['term_number'] ? $nodeConfig['term_number'] : 15,
            'page' => $page,
            'paginator' => true,
        );
        $data = $nodeEntity->find($query);
        $this->variables['title'] = $term->name;
        $this->variables['description'] = $term->description;
        $this->variables['keywords'] = $term->name;
        $this->variables += array(
            '#templates' => array(
                'pageNodeTerm',
                'pageNodeTerm-' . $term->contentModel,
                'pageNodeTerm-' . $id,
            ),
            'contentModel' => $term->contentModel,
            'id' => $id,
            'page' => $page,
            'term' => $term,
            'data' => $data,
        );
    }

    public function typeAction()
    {
        extract($this->variables['router_params']);
        $nodeConfig = Config::get('m.node.config');
        $nodeEntity = $this->entityManager->get('node');
        $nodeType = $nodeEntity->getContentModelList();
        if (!isset($nodeType[$contentModel])) {
            return $this->notFount();
        }
        $query = array(
            'andWhere' => array(
                array(
                    'conditions' => '%contentModel% = :contentModel:',
                    'bind' => array('contentModel' => $contentModel),
                ),
            ),
            'order' => '%changed% DESC',
            'limit' => isset($nodeConfig['number']) && $nodeConfig['number'] ? $nodeConfig['number'] : 15,
            'paginator' => true,
            'page' => $page,
        );
        $data = $nodeEntity->find($query);
        $this->variables['title'] = $nodeType[$contentModel]['modelName'];
        $this->variables['description'] = $nodeType[$contentModel]['description'];
        $this->variables += array(
            '#templates' => array(
                'pageNodeType',
                'pageNodeType-' . $contentModel,
            ),
            'data' => $data,
            'nodeType' => $nodeType[$contentModel],
            'contentModel' => $contentModel,
            'page' => $page,
        );
    }

    public function mateAction()
    {
        extract($this->variables['router_params']);
        $nodeExist = Node::findFirst($nid);
        if ($nodeExist) {
            Common::nodeMateHandle($nid, $type);
        } else {
            return $this->getNotFount();
        }
    }

    public function chosenSourceAction()
    {
        extract($this->variables['router_params']);
        if (!$this->request->isAjax()) {
            return $this->notFount();
        }
        $params = $this->request->getQuery();
        $word = $params['q'];
        $nodeEntity = $this->entityManager->get('node');
        $query = array(
            'match' => array(
                array(
                    'conditions' => 'MATCH(%title%) AGAINST(:title:)',
                    'bind' => array('title' => json_encode(urldecode($word)))
                ),
            ),
            'limit' => 20,
            'page' => 1,
        );
        if (isset($params['nt'])) {
            $query['andWhere'][] = array(
                'conditions' => '%contentModel% = :contentModel:',
                'bind' => array(
                    'contentModel' => $params['nt']
                )
            );
        }
        if ($userLock && $this->user->isLogin()) {
            $query['andWhere'][] = array(
                'conditions' => '%uid% = :uid:',
                'bind' => array(
                    'contentModel' => $this->user->id
                )
            );
        }
        $data = $nodeEntity->find($query);
        $output = array(
            'total_count' => count($data),
            'incomplete_results' => false,
            'items' => array()
        );
        foreach ($data as $item) {
            $output['items'][] = array(
                'id' => $item->id,
                'name' => $item->title->value
            );
        }
        $this->variables['#templates'] = 'json';
        $this->variables['data'] = $output;
    }
}
