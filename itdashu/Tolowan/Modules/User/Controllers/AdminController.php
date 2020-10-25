<?php
namespace Modules\User\Controllers;

use Core\Config;
use Core\Mvc\Controller;

use Core\Model;
use Modules\File\Models\File as File;
use Modules\User\Library\Common;
use Modules\User\Models\User;

class AdminController extends Controller
{

    public function editAction()
    {
        extract($this->variables['router_params']);
        $editFormInfo = Config::get('user.editForm');
        if($id){
            $editFormInfo['settings']['id'] = $id;
        }
        $editForm = new Form($editFormInfo);
        $this->variables += array(
            'title' => '用户列表',
            'description' => '',
            'type' => $role,
            'breadcrumb' => array(
                'admin' => array(
                    'href' => array(
                        'for' => 'adminIndex',
                    ),
                    'name' => '控制台',
                ),
                'rolesList' => array(
                    'name' => '用户列表'
                )
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
        $content['userList'] = array(
            '#templates' => 'box',
            'wrapper' => true,
            'title' => '用户列表',
            'max' => false,
            'color' => 'primary',
            'size' => '6',
            'content' => array(
                'list' => array(
                    '#templates' => 'adminUserList',
                    'data' => $data
                ),
            ),
        );
        $content['userHandle'] = array(
            '#templates' => 'box',
            'wrapper' => true,
            'title' => '添加用户',
            'max' => false,
            'id' => 'right_handle',
            'color' => 'success',
            'size' => '6',
            'content' => array(
                'list' => $userForm->renderForm(),
            ),
        );
        $this->variables['content'] += $content;
    }

    public function editorAction()
    {
        extract($this->variables['router_params']);
        $user = User::findFirst($id);
        if (!$user) {
            return false;
        }
        $userEditorArr = Config::get('user.userForm');
        $userEditorArr['formId'] = 'userEditor';
        $userEditorArr['settings']['id'] = $id;
        $userRoles = Common::userRoles($id);
        $userData = $user->toArray();
        $userData['roles'] = $userRoles;
        unset($userData['password']);
        $userEditorForm = new Form($userEditorArr, $userData);
        if ($userEditorForm->isValid()) {
            $userEditorForm->save();
            return $this->moved(array('for' => 'adminUserList', 'page' => 1));
        }
        $this->variables += array(
            '#templates' => 'pageNoWrapper',
            'content' => array(
                'userEditorForm' => array(
                    '#templates' => 'box',
                    'wrapper' => false,
                    'title' => '用户编辑-' . $user->name,
                    'max' => false,
                    'color' => 'success',
                    'size' => '12',
                    'content' => array(
                        'userEditorForm' => $userEditorForm->renderForm(),
                    ),
                ),
            ),
        );
    }

}
