<?php
namespace Modules\Form\Controllers;

use Core\Config;
use Core\Mvc\Controller;

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
        $userFormAdd = $this->form->create('form.userFormEdit');
        if ($userFormAdd->isValid()) {
            $userFormAdd->save();
        }

        $data = Config::get('m.form.userFormList');

        $this->variables += array(
            '#templates' => 'page',
            'title' => '自定义表单列表',
            'description' => '',
            'breadcrumb' => array(
                'admin' => array(
                    'href' => array(
                        'for' => 'adminIndex',
                    ),
                    'name' => '控制台',
                ),
                'admin' => array(
                    'href' => '#',
                    'name' => '列表',
                ),
            ),
            'content' => array(),
        );
        // 添加编辑菜单
        $content['formList'] = array(
            '#templates' => 'box',
            'title' => '列表',
            'max' => false,
            'wrapper' => true,
            'color' => 'primary',
            'size' => '6',
            'content' => array(
                '#templates' => 'adminFormList',
                'data' => $data,
            ),
        );
        $content['formHandle'] = array(
            '#templates' => 'box',
            'title' => '添加自定义表单',
            'max' => false,
            'id' => 'right_handle',
            'wrapper' => true,
            'color' => 'success',
            'size' => '6',
            'content' => array(
                'formForm' => $userFormAdd->renderForm(),
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
        $formList = Config::get('m.form.userFormList');
        if (!$formList[$id]) {
            $this->flash->error('术语不存在');
            return $this->notFount();
        }
        $formEditorArr = Config::get('form.userFormEdit');
        $formEditorArr['settings']['id'] = $id;
        unset($formEditorArr['id']);
        $formEditorForm = $this->form->create($formEditorArr, $formList[$id]);
        if ($formEditorForm->isValid()) {
            $formEditorForm->save();
        }

        $this->variables += array(
            '#templates' => 'pageNoWrapper',
            'content' => array(
                'formEditorForm' => array(
                    '#templates' => 'box',
                    'wrapper' => false,
                    'title' => '自定义表单编辑-' . $formList[$id]['title'],
                    'max' => false,
                    'color' => 'success',
                    'size' => '12',
                    'content' => array(
                        'formEditorForm' => $formEditorForm->renderForm(),
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

        $formList = Config::get('m.form.userFormList');
        if (!isset($formList[$id])) {
            $this->flash->error('自定义表单不存在');
        } else {
            $fields = $formList[$id]['fields'];
            if (is_string($fields)) {
                $fieldsDeleteState = Config::delete($fields);
            }
            unset($formList[$id]);
            if ($fieldsDeleteState && Config::set('m.form.userFormList', $formList)) {
                $this->flash->success('删除成功');
            }
        }
        return $this->moved(array(
            'for' => 'adminFormList',
        ));
    }

    public function fieldAddAction()
    {
        extract($this->variables['router_params']);
        $formList = Config::get('m.form.userFormList');
        if(!isset($formList[$id])){
            return $this->notFount();
        }
        $userFormFieldEditArr = Config::get('form.userFormFieldEdit');
        $userFormFieldEditArr['settings']['form_id'] = $id;
        $userFormFieldEditForm = $this->form->create($userFormFieldEditArr);
        if($userFormFieldEditForm->isValid()){
            if($userFormFieldEditForm->save()) {
                return $this->moved(array('for' => 'adminFormFieldSort', 'id' => $id));
            }
        }
        $this->variables += array(
            '#templates' => 'pageNoWrapper',
            'content' => array(
                'formEditorForm' => array(
                    '#templates' => 'box',
                    'wrapper' => false,
                    'title' => $formList[$id]['title'].'-添加字段',
                    'max' => false,
                    'color' => 'success',
                    'size' => '12',
                    'content' => array(
                        'formEditorForm' => $userFormFieldEditForm->renderForm(),
                    ),
                ),
            ),
        );
    }

    public function fieldEditAction()
    {
        extract($this->variables['router_params']);
        $formList = Config::get('m.form.userFormList');
        if(!isset($formList[$form_id])){
            return $this->notFount();
        }
        $fields = Config::get($formList[$form_id]['fields']);
        if(!isset($fields[$id])){
            return $this->notFount();
        }
        $userFormFieldEditArr = Config::get('form.userFormFieldEdit');
        unset($userFormFieldEditArr['id']);
        $userFormFieldEditArr['settings']['form_id'] = $form_id;
        $userFormFieldEditArr['settings']['id'] = $id;
        $userFormFieldEditForm = $this->form->create($userFormFieldEditArr,$fields[$id]);
        if($userFormFieldEditForm->isValid()){
            if($userFormFieldEditForm->save()) {
                return $this->moved(array('for' => 'adminFormFieldSort', 'id' => $id));
            }
        }
        $this->variables += array(
            '#templates' => 'pageNoWrapper',
            'content' => array(
                'formEditorForm' => array(
                    '#templates' => 'box',
                    'wrapper' => false,
                    'title' => $formList[$form_id]['title'].'-编辑字段：'.$fields[$id]['label'],
                    'max' => false,
                    'color' => 'success',
                    'size' => '12',
                    'content' => array(
                        'formEditorForm' => $userFormFieldEditForm->renderForm(),
                    ),
                ),
            ),
        );
    }

    public function fieldDeleteAction()
    {
        extract($this->variables['router_params']);
        $reUrl = array(
            'for' => 'adminFormFieldSort',
            'form_id' => $form_id,
        );
        $formList = Config::get('m.form.userFormList');
        if(!isset($formList[$form_id])){
            $this->flash->error('删除失败，表单不存在');
            return $this->moved($reUrl);
        }
        $fields = Config::get($formList[$form_id]['fields']);
        if(!isset($fields[$id])){
            $this->flash->error('删除失败，表单字段不存在');
            return $this->moved($reUrl);
        }
        unset($fields[$id]);
        if(Config::set($formList[$form_id]['fields'],$fields)){
            $this->flash->success('删除成功');
        }else{
            $this->flash->error('删除失败');
        }
        return $this->moved($reUrl);
    }

    public function fieldSortAction()
    {
        extract($this->variables['router_params']);
        $content = array();
        $formList = Config::get('m.form.userFormList');
        if(!isset($formList[$id])){
            $this->flash->error('删除失败，表单不存在');
            return $this->notFount();
        }

        $data = Config::get($formList[$id]['fields']);
        if ($this->request->isPost() && $this->request->hasPost('rh')) {
            $rh = json_decode($this->request->getPost('rh'));
            $rh = jsonToHierarchy($rh);
            $newData = array();
            foreach ($rh as $key => $value){
                $newData[$key] = $data[$key];
            }
            if(Config::set($formList[$id]['fields'],$newData)){
                $this->flash->success('菜单排序成功');
                $data = $newData;
            }
        }

        $this->variables += array(
            '#templates' => 'pageNoWrapper',
            'content' => array(),
        );
        $content['formSort'] = array(
            '#templates' => 'box',
            'title' => $formList[$id]['title'].'字段排序',
            'max' => false,
            'wrapper' => true,
            'color' => 'primary',
            'size' => '12',
            'content' => array(
                '#templates' => 'adminFormFieldSort',
                'id' => 'adminFormFieldSort',
                'title_display' => false,
                'data' => $data,
            ),
        );
        $this->variables['content'] += $content;
    }
}
