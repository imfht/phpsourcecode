<?php
// +----------------------------------------------------------------------
// | tp5_vue_authbuild
// +----------------------------------------------------------------------
// | Author: King east <1207877378@qq.com>
// +----------------------------------------------------------------------


namespace tpvue\admin\controller\access;


use think\Validate;
use tpvue\admin\builder\AdminFormBuilder;
use tpvue\admin\controller\BaseController;
use tpvue\admin\model\AdminAuthGroupAccessModel;
use tpvue\admin\model\AdminModel;
use tpvue\admin\validate\AdminValidate;

class ManagerController extends BaseController
{
    /**
     * 管理员列表
     * @return string
     */
    public function index()
    {
        $this->assign('meta_title', '管理列表');
        // 搜索
        $keyword = isset($this->param['keyword']) ? $this->param['keyword'] : false;
        $condition = [];
        if ($keyword) {
            $condition['realname|mobile'] = ['like', '%' . $keyword . '%'];
        }

        $SuperAdminData = AdminModel::order('register_time desc , update_time desc')->paginate(8, false, ['query' => request()->param()]);
        $this->assign('list', $SuperAdminData);
        return $this->fetch('access/manager/index');
    }


    /**
     * 新增管理
     * @return parent
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $form = $this->request->post();
            $valid = new AdminValidate();
            if (!$valid->check($form)) {
                $this->error($valid->getError());
            }

            if(AdminModel::where('username', $form['username'])->value('id')){
                $this->error('当前账户已存在，请勿重复添加');
            }
            $member = AdminModel::create($form);
            if (!$member) {
                $this->error('新增失败');
            }
            $this->success('新增成功！', 'admin/access/manager/index');
        }

        $builder = new AdminFormBuilder();
        return $builder->setMetaTitle('新增管理')
            ->addFormItem('username', '用户登录名', '请填写用户登录名', 'text', '', 'required')
            ->addFormItem('nickname', '会员昵称', '请填写会员昵称', 'text', '', 'required')
            ->addFormItem('mobile', '手机号码', '请填写11位手机号码', 'number', '')
            ->addFormItem('password', '登录密码', '请填写登录密码', 'password', '', 'required')
            ->addFormItem('repassword', '确认密码', '请确认登录密码', 'password', '', 'required')
            ->addFormItem('avatar', '头像', '', 'picture')
            ->addFormItem('status', '用户状态', '', 'radio', array(0 => '禁用', 1 => '启用'))
            ->setFormData([
                'status' => 1
            ])
            ->addButton('submit')
            ->addButton('back')// 设置表单按钮
            ->fetch();
    }


    /**
     * 编辑管理
     * @return parent
     */
    public function edit()
    {
        $id = $this->request->get('id');
        if (empty($id)) {
            $this->error('参数错误');
        }
        $admin = AdminModel::where('id', $id)->find();
        if (!$admin) {
            $this->error('管理账号不存在');
        }
        if ($this->request->isPost()) {
            $form = $this->request->post();
            $err = $this->validate($form, AdminValidate::class, 'edit');
            if ($err !== true) {
                $this->error($err);
            }

            if(AdminModel::where('username', $form['username'])->where('id', '<>', $admin->id)->value('id')){
                $this->error('当前账户已存在，请勿重复添加');
            }
            if (isset($form['password'])) {
                if ($form['password'] === '') {
                    unset($form['password']);
                }
            }
            $admin->save($form);
            $this->success('修改成功！', 'admin/access/manager/index');
        }

        unset($admin['password']);
        $builder = new AdminFormBuilder();
        return $builder->setMetaTitle('编辑管理')
            ->addFormItem('username', '用户登录名', '请填写用户登录名', 'text', '', 'required')
            ->addFormItem('nickname', '会员昵称', '请填写会员昵称', 'text', '', 'required')
            ->addFormItem('mobile', '手机号码', '请填写11位手机号码', 'number', '')
            ->addFormItem('password', '登录密码', '请填写登录密码', 'password', '')
            ->addFormItem('avatar', '头像', '', 'picture')
            ->addFormItem('status', '用户状态', '', 'radio', array(0 => '禁用', 1 => '启用'))
            ->setFormData($admin)
            ->addButton('submit')
            ->addButton('back')// 设置表单按钮
            ->fetch();
    }


    /**
     * 删除
     */
    public function delete()
    {
        $id = $this->request->get('id');
        if (empty($id)) {
            $this->error('参数错误');
        }
        $admin = AdminModel::where('id', $id)->field('id')->find();
        if (!$admin) {
            $this->error('管理账号不存在');
        }
        $admin->delete();

        $this->success('删除成功');
    }

    /**
     * [resetPassword 修改密码]
     */
    public function resetPassword()
    {
        if ($this->request->isPost()) {
            //$oldpassword=I('post.oldpassword',false);
            $form = $this->request->post();
            $newpassword=$form['newpassword'];
            $repassword=$form['repassword'];
            if(!empty($newpassword)){
                if ($newpassword==$repassword) {
                    $uid=input('post.id',is_login(),'intval');
                    $user = new AdminModel;
                    $res=$user->save(['password'=>$newpassword],['id'=>$uid]);
                    if ($res) {
                        session(null);
                        cookie(null);
                        $this->success('密码修改成功', url('Admin/login'));
                    }
                } else {
                    $this->error('两次密码不一致');
                }
            } else {
                $this->error('密码不得为空');
            }
        }else {
            // 获取账号信息
            $info = AdminModel::where(array('id'=>is_login()))->find();
            // 使用FormBuilder快速建立表单页面。
            $builder = new AdminFormBuilder();
            return $builder->setMetaTitle('修改密码')
                ->addFormItem('newpassword','新密码','','password','','','placeholder=填写新密码')
                ->addFormItem('repassword','重复密码','','password','','','placeholder=填写重复密码')
                ->setFormData($info)
                ->addButton('submit')->addButton('back')    // 设置表单按钮
                ->fetch();
        }
    }


}