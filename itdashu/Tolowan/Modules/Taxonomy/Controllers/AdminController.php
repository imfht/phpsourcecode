<?php
namespace Modules\Taxonomy\Controllers;

use Core\Mvc\Controller;
use Modules\Taxonomy\Library\Form;
use Modules\Taxonomy\Models\Term;

class AdminController extends Controller
{

    /**
     * @param $type
     * @param $page
     */
    public function indexAction()
    {
        extract($this->variables['router_params']);
        $content = array();
        $entity = $this->entityManager->get('term');
        $typeList = $entity->getContentModelList();
        if (!isset($typeList[$contentModel])) {
            return $this->notFount();
        }
        $entity->contentModel = $contentModel;
        $filterForm = $entity->filterForm();
        $entityAddForm = $entity->addForm($contentModel);
        $query = array(
            'andWhere' => array(
                array(
                    'conditions' => 'contentModel = :contentModel:',
                    'bind' => array('contentModel' => $contentModel),
                ),
            ),
            'limit' => 30,
            'page' => $page,
            'paginator' => true,
        );
        $data = $entity->find($query);
        $typeInfo = $entity->getContentModelInfo($contentModel);
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

        if ($this->request->isPost() && $this->request->hasPost('rh')) {
            $rh = json_decode($this->request->getPost('rh'));
            $rh = jsonToHierarchy($rh);
            Form::saveTermSort($rh);
            $this->flash->success('菜单排序成功');
        }

        $this->variables += array(
            'type' => $contentModel,
            'typeInfo' => $typeList[$contentModel],
            'description' => $typeInfo['description'],
            '#templates' => 'page',
            'title' => $typeInfo['modelName'] . '列表',
            'description' => '',
            'breadcrumb' => array(
                'admin' => array(
                    'href' => array(
                        'for' => 'adminIndex',
                    ),
                    'name' => '控制台',
                ),
                'admin' => array(
                    'href' => array(
                        'for' => 'adminIndex',
                    ),
                    'name' => '列表',
                ),
            ),
            'content' => array(),
        );
        // 添加编辑菜单
        $content['termList'] = array(
            '#templates' => 'box',
            'title' => $typeInfo['modelName'] . '列表',
            'max' => false,
            'wrapper' => true,
            'color' => 'primary',
            'size' => '6',
            'content' => array(
                '#templates' => 'adminTermList',
                'id' => 'menuLinkHierarchy',
                'title_display' => false,
                'data' => $data,
            ),
        );
        $content['termHandle'] = array(
            '#templates' => 'box',
            'title' => '添加' . $typeInfo['modelName'],
            'max' => false,
            'id' => 'right_handle',
            'wrapper' => true,
            'color' => 'success',
            'size' => '6',
            'content' => array(
                'termForm' => $entityAddForm->renderForm(),
            ),
        );

        $this->variables['content'] += $content;
    }

    /**
     * @return mixed
     */
    public function editorAction()
    {
        extract($this->variables['router_params']);
        $term = Term::findFirst($id);
        if (!$term) {
            $this->flash->error('术语不存在');
            return false;
        }
        $entity = $this->entityManager->get('term');
        $termEditorForm = $entity->editForm(null, $id);
        if ($entity->isSaveSuccess() === true) {
            return $this->moved(array('for' => 'adminTermList', 'contentModel' => $term->contentModel, 'page' => 1));
        }
        $this->variables += array(
            '#templates' => 'pageNoWrapper',
            'content' => array(
                'termEditorForm' => array(
                    '#templates' => 'box',
                    'wrapper' => false,
                    'title' => '术语编辑-' . $term->name,
                    'max' => false,
                    'color' => 'success',
                    'size' => '12',
                    'content' => array(
                        'termEditorForm' => $termEditorForm->renderForm(),
                    ),
                ),
            ),
        );
    }

    /**
     * @param $id
     */
    public function deleteAction()
    {
        extract($this->variables['router_params']);
        //获取下属term并更新
        $term = Term::findFirst($id);
        if (!$term) {
            return false;
        }
        if ($term->delete()) {
            $this->flash->success('删除成功');
        } else {
            $this->flash->error('删除失败');
        }
        return $this->moved(array(
            'for' => 'adminTermList',
            'contentModel' => $term->contentModel,
            'page' => 1,
        ));
    }

    /**
     * @param $term
     * @param $parent
     * @return mixed
     */
    private function jsonToTerm($term, $parent = null)
    {
        $output = array();
        foreach ($term as $t) {
            if ($parent != null) {
                $output[$t->id] = array('parent' => $parent);
            } else {
                $output[$t->id] = array();
            }
            if (isset($t->children)) {
                $output += $this->jsonToTerm($t->children, $t->id);
            }
        }
        return $output;
    }
}
