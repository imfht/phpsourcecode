<?php
namespace Modules\Node\Controllers;

use Core\Mvc\Controller;
use Modules\Node\Models\Node;
use Modules\User\Models\UserLog;

class UserController extends Controller
{
    public function loveAction()
    {
        extract($this->variables['router_params']);
        $data = array();
        $this->variables += array(
            '#templates' => 'json',
            'data' => &$data,
        );
        if (!$this->request->isAjax()) {
            $data = array(
                'state' => 'fail',
                'notice' => '呵呵了',
            );
            return false;
        }
        $nodeEntity = $this->entityManager->get('node');
        $node = $nodeEntity->findFirst($id, true);
        if (!$node) {
            $data = array(
                'state' => 'fail',
                'number' => '0',
                'notice' => '文章不存在了',
            );
        }
        $log = UserLog::findFirst(array(
            'conditions' => 'uid = :uid: AND type = :type:',
            'bind' => array(
                'uid' => $this->user->id,
                'type' => 'node-love-' . $id,
            ),
        ));

        if ($log && $node->love >= 1) {
            $node->love -= 1;
        } elseif (!$log) {
            $node->love += 1;
        }
        if ($node->save()) {
            $data = array(
                'state' => 'success',
                'notice' => '赞过',
                'number' => $node->love,
            );
        } else {
            $data = array(
                'state' => 'fail',
                'number' => $node->love,
                'notice' => '保存失败',
            );
        }
    }

    public function addAction()
    {
        extract($this->variables['router_params']);
        $nodeEntity = $this->entityManager->get('node');
        $nodeFields = $nodeEntity->getFields();
        $contentModelLists = $nodeEntity->getContentModelList();
        if (!isset($nodeFields['settings']['nodeAdd']) || $nodeFields['settings']['nodeAdd'] !== true) {
            //return $this->notFount();
        }
        $nodeForm = $nodeEntity->addForm($contentModel);
        if ($nodeEntity->isSaveSuccess() === true) {
            return $this->moved(array('for' => 'node', 'id' => $nodeEntity->entityModel->id));
        }
        $this->variables += array(
            '#templates' => array(
                'pageNodeAdd',
                'pageNodeAdd-' . $contentModel,
            ),
            'contentModelInfo' => $contentModelLists[$contentModel],
            'title' => '添加' . $contentModelLists[$contentModel]['modelName'],
            'data' => $nodeForm->renderForm(),
        );
    }

    public function editorAction()
    {
        extract($this->variables['router_params']);
        $nodeEntity = $this->entityManager->get('node');
        $nodeFields = $nodeEntity->getFields();
        $contentModelLists = $nodeEntity->getContentModelList();
        $nodeForm = $nodeEntity->editorForm($contentModel, $id);
        if ($nodeEntity->isSaveSuccess() === true) {
            return $this->moved(array('for' => 'node', 'id' => $id));
        }
        $this->variables += array(
            '#templates' => array(
                'nodeAdd',
                'nodeAdd-' . $contentModel,
            ),
            'title' => '添加' . $contentModelLists[$contentModel]['modelName'],
            'description' => '添加' . $contentModelLists[$contentModel]['modelName'],
            'nodeForm' => $nodeForm->renderForm(),
        );
    }

    public function deleteAction()
    {
        extract($this->variables['router_params']);
        $nodeEntity = $this->entityManager->get('node');
        $node = $nodeEntity->findFirst($id);
        $node->delete();
    }

}
