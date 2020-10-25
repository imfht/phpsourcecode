<?php

class AdminController extends PT_Controller {

    // 过滤列表
    protected $map = array();

    public function __construct() {
        $this->config->set('tpl_theme', '');
        $this->config->set('layout', true);
        $this->config->set('rewritepower', 0);
        $this->config->set('layout_name', '/application/admin/view/public_layout.html');
        $this->skipnode   = array('admin.index.index');
        $this->skinAction = array('ajax', 'uploadone');
    }

    public function init() {
        $this->session->start();
        // 登录状态判断
        if (empty($_SESSION['admin'])) {
            //未登录
            $this->redirect(U('admin.public.login'));
        } else {
            $this->view->username  = $_SESSION['admin']['username'];
            $this->view->groupname = $_SESSION['admin']['groupname'];
            $this->session->close();
        }
        // 当前页面信息
        $this->view->menuinfo = $this->model('adminnode')->getMenuInfo();
        //判断是否有权限访问当前页面 创始人 访问权限 免验节点 ajax
        if ($_SESSION['admin']['userid'] != '1'
            && (!empty($this->view->menuinfo['nodeid']) && !in_array($this->view->menuinfo['nodeid'], explode(',', $this->model->get('admin_group', $_SESSION['admin']['groupid'], 'node'))))
            && !in_array(MODULE_NAME . '.' . CONTROLLER_NAME . '.' . ACTION_NAME, $this->skipnode)
            && in_array(ACTION_NAME, $this->skinAction)
        ) {
            $this->error('您没有权限访问这个页面！', 0, 0);
        }
        // 其他初始化
    }

    public function delAction() {
        $id = $this->input->request('id', 'int', 0);
        $this->model->del(array('id' => $id));
        $this->success('删除成功');
    }

    public function multiAction() {
        $param['update_user_id'] = $_SESSION['admin']['userid'];
        $param['update_time']    = NOW_TIME;
        if (isset($_POST['changestatus'])) {
            foreach ($_POST['id'] as $k => $v) {
                $param['id']     = $v;
                $param['status'] = $_POST['value'][$k];
                $this->model->edit($param);
            }
            $this->success('修改状态成功');
        } elseif (isset($_POST['method'])) {
            switch ($_POST['method']) {
                case 'reorder':
                    foreach ($_POST['id'] as $id) {
                        $param['id']       = $id;
                        $param['ordernum'] = $_POST['ordernum'][$id];
                        $this->model->edit($param);
                    }
                    break;
                case 'rebuild':
                    foreach ($_POST['id'] as $id) {
                        $this->model->createJs($id);
                    }
                    break;
                case 'mdel':
                    foreach ($_POST['id'] as $id) {
                        $this->model->del(array('id' => $id));
                    }
                    break;
                case 'recover':
                    foreach ($_POST['id'] as $id) {
                        $param['id']     = $id;
                        $param['status'] = 1;
                        $this->model->edit($param);
                    }
                    break;
                case 'forbidden':
                    foreach ($_POST['id'] as $id) {
                        $param['id']     = $id;
                        $param['status'] = 0;
                        $this->model->edit($param);
                    }
                    break;
                default:
                    $this->error('未定义指定的操作');
            }
            $this->success('操作成功');
        } else {
            var_dump($_POST);
        }
    }

    public function ajaxAction() {
        $id    = $this->input->request('id', 'int', 0);
        $value = $this->input->request('param', 'en', '');
        $oid   = $this->model->where(array('key' => $value))->field('id')->getField('id');
        if ($oid && $oid != $id) {
            $data = array('status' => 'n', 'info' => '您输入的Key已经使用过了');
        } else {
            $data = array('status' => 'y', 'info' => '您输入的Key可以使用');
        }
        $this->ajax($data);
    }

    //解析搜索
    protected function _parsemap() {
        $searchtype = $this->input->request("searchtype", 'str', '');
        $searchkey  = $this->input->request("searchkey", 'str', '');
        if ($searchkey && $searchtype) {
            return array($searchtype => array('like', '%' . $searchkey . '%'));
        }
        return array();
    }

    //文件上传
    public function uploadoneAction() {
        $upload = new upload();
        if (!empty($_FILES)) {
            /*@var attachmentModel*/
            $model=$this->model('attachment');
            $file = array_shift($_FILES);
            if (!empty($_POST['original_filename'])) $file['name'] = $_POST['original_filename'];
            // 判断是否有已经上传的内容
            $hash = md5(F($file['tmp_name']));
            $url  = $model->where(array('hash' => $hash))->getfield('url');
            if ($url) $this->ajax(array("success" => true, 'msg' => 'success', 'file_path' => $url));
            // 未上传过 开始上传
            $this->config->set('storage_path', $this->config->get('upload_path', 'uploads'));
            //文件源
            $upload->setFile($file);
            $data = $upload->uploadone();
            if ($data['status'] == 1) {
                //写入附件表
                $param = array(
                    'create_user_id' => $_SESSION['admin']['userid'],
                    'create_time'    => NOW_TIME,
                    'name'           => $data['info']['filename'],
                    'path'           => $data['info']['filepath'],
                    'url'            => $data['info']['fileurl'],
                    'ext'            => $data['info']['ext'],
                    'size'           => $data['info']['size'],
                    'hash'           => $data['info']['hash'],
                );
                $model->add($param);
                //返回数据
                $ajax = array("success" => true, 'msg' => 'success', 'file_path' => $data['info']['fileurl']);
            } else {
                $ajax = array("success" => false, 'msg' => $data['info']);
            }
        } else {
            $ajax = array("success" => false, 'msg' => '没有找到上传的文件');
        }
        $this->ajax($ajax);
    }
}