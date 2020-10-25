<?php
namespace Modules\Node\Controllers;

use Core\Config;
use Core\EntityManager;

use Core\Mvc\Controller;
use Modules\Node\Library\Common;
use Modules\Node\Library\Model as Nmodel;
use Modules\Node\Models\Node;

class AdminController extends Controller
{

    public function indexAction($page)
    {
        //测试钩子
        $entityType = $this->entityManager->getEntityType($type);
        $label = $entityType->getLabel();
        $filterForm = $entityType->filterForm();
        $name = $entityType->getName();
        $data = $entityType->find(array(
            'limit' => 30,
            'paginator' => true,
        ));
        $nodeType = $this;

        $content = array();
        $this->variables += array(
            'title' => $name . '列表',
            'description' => '',
            'breadcrumb' => array(
                'admin' => array(
                    'href' => array(
                        'for' => 'adminIndex',
                    ),
                    'name' => '控制台',
                ),
                'nodeList' => array(
                    'name' => $name . '列表',
                ),
            ),
            'content' => array(),
        );

        $content['filter'] = array(
            '#templates' => 'box',
            'max' => false,
            'color' => 'widget',
            'hiddenTitle' => false,
            'size' => '12',
            'wrapper' => true,
            'content' => array(
                'filterForm' => $filterForm->renderForm(),
            ),
        );
        $actionMenu = array();
        foreach ($nodeType as $key => $value) {
            $value['access'] = (string) $value['access'];
            if (isset($value['access']) && $value['access'][0] == 1) {
                $actionMenu[$key] = array(
                    'href' => $this->url->get(array('for' => 'adminNodeAdd', 'type' => $key)),
                    'name' => $value['name'],
                );
            }
        }
        $handleForm = new Form(Config::get('node.handleForm'));
        $content['nodeList'] = array(
            '#templates' => 'box',
            'wrapper' => true,
            'title' => $typeInfo['name'] . '列表',
            'max' => false,
            'color' => 'success',
            'size' => '12',
            'content' => array(
                'actionMenu' => array(
                    '#templates' => 'menuGroup',
                    'title' => '添加',
                    'data' => $actionMenu,
                ),
                'list' => array(
                    '#templates' => 'adminNodeList',
                    'handle' => $handleForm,
                    'data' => $data,
                ),
            ),
        );
        $this->variables['content'] += $content;
    }

    protected function _filterForm($query)
    {
        $params = $this->request->getQuery();
        foreach (array('top', 'hot', 'essence') as $value) {
            if (isset($params[$value])) {
                $query[$value] = 1;
            }
        }
        if (isset($params['state']) && $params['state']) {
            $query['state'] = intval($params['state']);
        }
        if (isset($params['type'])) {
            $nodeType = Config::get('m.node.type');
            if (isset($nodeType[$params['type']])) {
                $query['type'] = $params['type'];
            }
        }
        return $query;
    }

    public function addAction()
    {
        extract($this->variables['router_params']);
        $config = Config::get('config');
        //File::mkdir('public/aa/bb/cc/dd/aa');
        //config::printCode(File::ll($config['dir']['pubDir']));
        $content = array();
        $nodeType = Config::get('m.node.type');
        if (!isset($nodeType[$type])) {
            $this->flash->error('内容类型不存在。');
            return $this->moved(array('for' => 'adminNodeList', 'page' => 1));
        }
        $typeInfo = $nodeType[$type];
        $nodeAdd = Config::get('m.node.node_' . $type);
        $nodeData = array();
        if (isset($id) && $id) {
            $nodeAdd['settings']['id'] = $id;
            $nodeData = Nmodel::findFirst($id, array(
                'entity' => false,
                'type' => $type,
                'now' => false,
            ));
            if ($nodeData) {
                $nodeData = $nodeData->toArray();
                //Config::printCode($nodeData);
            } else {
                $this->flash->error('需要编辑的文章不存在，自动跳转到新建内容页面');
                return $this->moved(array('for' => 'adminNodeAdd', 'type' => $type));
            }
        }
        //Config::printCode($nodeData);
        $nodeAdd = new Form($nodeAdd, $nodeData);
        if ($nodeAdd->isValid()) {
            $state = $nodeAdd->save();
            if ($state != false) {
                return $this->moved(array(
                    'for' => 'adminNodeList',
                    'page' => 1,
                ));
            }
        }
        $this->variables = array(
            'title' => '添加' . $typeInfo['name'],
            'description' => '',
            'breadcrumb' => array(
                'admin' => array(
                    'href' => array(
                        'for' => 'adminIndex',
                    ),
                    'name' => '控制台',
                ),
                'adminAddNode' => array(
                    'name' => '添加' . $typeInfo['name'],
                ),
            ),
            'content' => array(),
        );
        $content['nav'] = array(
            '#templates' => 'adminNodeTypeMenu',
            'active' => $type,
            'data' => $nodeType,
            'size' => 1,
        );
        $content['addNode'] = array(
            '#templates' => 'box',
            'title' => '添加' . $typeInfo['name'],
            'max' => false,
            'color' => 'primary',
            'wrapper' => true,
            'size' => '11',
            'content' => array(
                'nodeAdd' => $nodeAdd->renderForm(),
            ),
        );
        $this->variables['content'] += $content;
    }

    public function handleAction()
    {
        extract($this->variables['router_params']);
        $post = $this->request->getPost();
        foreach ($post['checkAll'] as $key => $value) {
            $key = intval($key);
            switch ($post['action']) {
                case 'delete':
                    Common::delete($key);
                    break;
                default:
                    Common::changeState($key, $post['action']);
                    break;
            }
        }
        return $this->moved(array('for' => 'adminNodeList', 'page' => 1));
    }

    public function deleteAction($id)
    {
        extract($this->variables['router_params']);
        Common::delete($id);
        return $this->moved(array(
            'for' => 'adminNodeList',
            'page' => 1,
        ));
    }
}
