<?php
namespace Modules\Install\Controllers;

use Core\Config;
use Core\Mvc\Controller;

class InstallController extends Controller
{

    /**
     * @param $type
     * @param $page
     */
    public function indexAction()
    {
        extract($this->variables['router_params']);
        $testForm = Config::get('install.validation');
        $testForm = $this->form->create($testForm);
        if ($testForm->isValid()) {
            $saveState = $testForm->save();
            if($saveState){
                return $this->temMoved(['for'=>'test']);
            }else{
                $this->flash->error('密码不正确，请访问查看帮助');
            }
        }

        $this->variables += [
            '#templates' => 'install',
            'title' => '安装新的Tolowan站点',
            'testForm' => $testForm->renderForm(),
        ];
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

    /**
     * @return mixed
     */
    public function testAction()
    {
        extract($this->variables['router_params']);
        $output = ['root'=>false,'web' => false];
        if(is_writable(ROOT_DIR)){
            $output['root'] = '根目录没有写入权限：'.ROOT_DIR;
        }
        if(is_writable(ROOT_DIR.'Web')){
            $output['web'] = 'WEB目录没有写入权限：'.ROOT_DIR.'/Web';
        }
        if(!function_exists('scandir') || !function_exists('stream_context_create') || !function_exists('fopen') || !function_exists('file_get_contents') || !function_exists('file_put_contents')){
            $output['function'] = '程序运行请保持以下函数没有被禁用：scandir，stream_context_create，fopen，file_get_contents，file_put_contents';
        }
        if(!function_exists('exif_imagetype')){
            $output['exif_imagetype'] = 'exif_imagetype函数不存在，请确保开启了exif扩展';
        }
        $this->variables += array(
            '#templates' => 'installTest',
            'title' => '安装新的Tolowan站点',
            'output' => $output,
        );
    }

    /**
     * @param $id
     */
    public function oneAction()
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

    public function twoAction()
    {
        extract($this->variables['router_params']);
        $formList = Config::get('m.form.userFormList');
        if (!isset($formList[$id])) {
            return $this->notFount();
        }
        $userFormFieldEditArr = Config::get('form.userFormFieldEdit');
        $userFormFieldEditArr['settings']['form_id'] = $id;
        $userFormFieldEditForm = $this->form->create($userFormFieldEditArr);
        if ($userFormFieldEditForm->isValid()) {
            if ($userFormFieldEditForm->save()) {
                return $this->moved(array('for' => 'adminFormFieldSort', 'id' => $id));
            }
        }
        $this->variables += array(
            '#templates' => 'pageNoWrapper',
            'content' => array(
                'formEditorForm' => array(
                    '#templates' => 'box',
                    'wrapper' => false,
                    'title' => $formList[$id]['title'] . '-添加字段',
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

    public function endAction()
    {
        extract($this->variables['router_params']);
        $formList = Config::get('m.form.userFormList');
        if (!isset($formList[$form_id])) {
            return $this->notFount();
        }
        $fields = Config::get($formList[$form_id]['fields']);
        if (!isset($fields[$id])) {
            return $this->notFount();
        }
        $userFormFieldEditArr = Config::get('form.userFormFieldEdit');
        unset($userFormFieldEditArr['id']);
        $userFormFieldEditArr['settings']['form_id'] = $form_id;
        $userFormFieldEditArr['settings']['id'] = $id;
        $userFormFieldEditForm = $this->form->create($userFormFieldEditArr, $fields[$id]);
        if ($userFormFieldEditForm->isValid()) {
            if ($userFormFieldEditForm->save()) {
                return $this->moved(array('for' => 'adminFormFieldSort', 'id' => $id));
            }
        }
        $this->variables += array(
            '#templates' => 'pageNoWrapper',
            'content' => array(
                'formEditorForm' => array(
                    '#templates' => 'box',
                    'wrapper' => false,
                    'title' => $formList[$form_id]['title'] . '-编辑字段：' . $fields[$id]['label'],
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
}
