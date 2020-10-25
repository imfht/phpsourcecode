<?php
/**
 * Created by PhpStorm.
 * @author Luficer.p <81434146@qq.com>
 * Date: 16/11/3
 * Time: 下午12:53
 */

namespace LuciferP\TinyMvc\controllers;


use LuciferP\TinyMvc\models\User;

class Users extends BaseController
{

    public $page_title = '用户管理';
    private $page_size = 10;

    /**
     * @return string
     * @throws \Exception
     */
    public function index()
    {

        $page = @intval($_GET['page']);
        $page = $page > 0 ? $page : 1;
        $start = ($page - 1) * $this->page_size;

        $count = User::model()->count();

        $users = User::model()->limit($start, $this->page_size)->findAll();

        $pages = [
            'count' => $count,
            'size' => $this->page_size,
            'start' => $start,
            'page' => $page,
            'page_num' => ceil($count / $this->page_size)
        ];

        return $this->render(['users' => $users, 'pages' => $pages]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function create()
    {
        $error = [];
        if ($this->request['method'] == 'POST') {
            $user = new User();
            $user->name = $this->request['post']['name'];
            $user->password = $this->request['post']['password'];
            if ($user->save()) {

                $this->setToast('创建成功', $this->createUrl('/users/', ['id' => $user->getId()]));

            } else {
                $error = $user->getErrors();

                $this->setToast('创建失败' . join("<br>", $error));


            }
        }
        return $this->render(['error' => $error]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function update($id)
    {

        $user = User::model()->where(['id' => $id])->find();


        if ($this->request['method'] == 'POST') {

            $user->name = $this->request['post']['name'];
            $user->password = $this->request['post']['password'];
            if ($user->save()) {
                $this->setToast('修改成功', $this->createUrl('/users/', ['id' => $user->getId()]));


            } else {

                $error = $user->getErrors();
                $this->setToast('创建失败' . join("<br>", $error));


            }
        }

        if (!$data = $user->getAttributes()) {
            return $this->renderError(400, "用户 id:{$id} 不存在");

        }

        return $this->render(['user' => $data]);
    }

    /**
     * @return string
     * @throws \Exception
     */

    public function view($id)
    {

        $user = User::model()->where(['id' => $id])->find();

        if (!$data = $user->getAttributes()) {

            return $this->renderError(400, "用户 id:{$id} 不存在");

        }
        return $this->render(['user' => $data]);

    }

    /**
     * @throws \LuciferP\Orm\base\AppException
     */
    public function delete($id)
    {
        $user = User::model()->where(['id' => $id])->find();
        if (!$user->getAttributes()) {
            $this->renderError(400, "用户 id:{$id} 不存在");
        }
        if (!$user->delete()) {
            $error = $user->getErrors();
            $this->renderError(400, '删除失败' . join("<br>", $error));
        }

        $this->redirect($this->createUrl('/users'));

    }

}