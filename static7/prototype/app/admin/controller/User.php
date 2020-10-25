<?php

namespace app\admin\controller;

use think\Loader;
use think\Url;
use think\Request;
use think\Session;
use think\Db;
use think\Cookie;
use think\Config;

/**
 * 后台用户管理控制器
 * @author static7
 */
class User extends Admin {

    /**
     * 后台用户首页
     * @author staitc7 <static7@qq.com>
     */
    public function index() {
        $Member = Loader::model('Member');
        $user_list = $Member->userList();
        $value = [
            'list' => $user_list['data'] ?? null,
            'page' => $user_list['page']
        ];
        $this->view->metaTitle = '用户管理';
        return $this->view->assign($value)->fetch();
    }

    /**
     * 添加用户
     * @author staitc7 <static7@qq.com>
     */
    public function edit() {
        $this->view->metaTitle = '添加用户';
        return $this->view->fetch();
    }

    /**
     * 添加用户
     * @author staitc7 <static7@qq.com>
     */
    public function add() {
        $Member = Loader::model('Member');
        $info = $Member->userAdd();
        return is_array($info) ?
                $this->success('添加成功', Url::build('User/index')) :
                $this->error($info);
    }

    /**
     * 单条数据状态修改
     * @param Request $request
     * @param int $value 状态
     * @param null $ids
     * @internal param ids $int 数据条件
     * @author staitc7 <static7@qq.com>
     */
    public function setStatus(Request $request,$value = null, $ids = null) {
        empty($ids) && $this->error('请选择要操作的数据');
        if ((int) $ids === $this->uid) {
            $this->error('不允许对超级管理员执行该操作');
        }
        !is_numeric((int) $value) && $this->error('参数错误');
        $info = Loader::model('Member')->setStatus(['uid' => ['in', (int) $ids]], ['status' => $value]);
        return $info !== FALSE ?
                $this->success($value == -1 ? '删除成功' : '更新成功') :
                $this->error($value == -1 ? '删除失败' : '更新失败');
    }

    /**
     * 批量数据更新
     * @param Request $request
     * @param int $value 状态
     * @author staitc7 <static7@qq.com>
     */
    public function batchUpdate(Request $request,$value = null) {
        $ids = array_unique($request->post());
        empty($ids['ids']) && $this->error('请选择要操作的数据');
        if (in_array((string) $this->root, $ids, true)) {
            $this->error('用户中包含超级管理员，不能执行该操作');
        }
        !is_numeric((int) $value) && $this->error('参数错误');
        $info = Loader::model('Member')->setStatus(['uid' => ['in', $ids['ids']]], ['status' => $value]);
        return $info !== FALSE ?
                $this->success($value == -1 ? '删除成功' : '更新成功') :
                $this->error($value == -1 ? '删除失败' : '更新失败');
    }

    /**
     * 修改昵称初始化
     * @author huajie <banhuajie@163.com>
     */
    public function updateNickname() {
        $nickname = Loader::model('Member')->oneUser(['uid' => $this->uid], 'nickname');
        return $this->view->assign('info', $nickname)->fetch();
    }

    /**
     * 修改昵称提交
     * @param string $password 密码
     * @param string $nickname 昵称
     * @author huajie <banhuajie@163.com>
     */
    public function submitNickname($password = null, $nickname = null) {
        empty($password) && $this->error('请输入密码');
        empty($nickname) && $this->error('请输入昵称');
        //密码验证
        $uid = Loader::model('UcenterMember', 'api')->login($this->uid, $password, 4); //密码验证
        if ($uid == -2) {
            return $this->error('密码不正确');
        }
        $Member = Loader::model('Member');
        $data = $Member->renew(['nickname' => $nickname, 'uid' => $this->uid]);
        if (is_array($data)) {
            $user = Session::get('user_auth');
            $user['username'] = $data['nickname'];
            Session::set('user_auth', $user);
            Session::set('user_auth_sign', data_auth_sign($user));
            return $this->success('修改昵称成功！');
        } else {
            return $this->error($data);
        }
    }

    /**
     * 修改密码初始化
     * @author huajie <banhuajie@163.com>
     */
    public function updatePassword() {
        return $this->view->fetch();
    }

    /**
     * 修改密码提交
     * @param string $old 原密码
     * @param string $password 新密码
     * @param string $repassword 确认密码
     * @author huajie <banhuajie@163.com>
     */
    public function submitPassword($old = null, $password = null, $repassword = null) {
        empty($old) && $this->error('请输入原密码');
        empty($password) && $this->error('请输入新密码');
        empty($repassword) && $this->error('请输入确认密码');
        if ((string) $password !== (string) $repassword) {
            return $this->error('您输入的新密码与确认密码不一致');
        }
        $UcenterMember = Loader::model('UcenterMember', 'api');
        $res = $UcenterMember->updateUserFields($this->uid, $old, ['password' => $password]);
        if (is_numeric($res)) {
            return $this->success('修改密码成功！');
        } else {
            return $this->error($res);
        }
    }

    /**
     * 用户头像
     * @author staitc7 <static7@qq.com>
     */
    public function portrait() {
        $this->view->metaTitle = '设置头像';
        return $this->view->fetch();
    }

    /**
     * 头像裁剪坐标
     * @author staitc7 <static7@qq.com>
     */
    public function headPortrait() {
        $info = Loader::model('Picture', 'api')->portrait($this->uid);
        if (!is_object($info)) {
            return $this->error($info);
        }
        $id = $this->images($info);
        if ($id) {
            Cookie::delete("user_".$this->uid,"portrait_");
            Db::name('Member')->where('uid', $this->uid)->setField('portrait', $id);
            return $this->success('保存成功', Url::build('user/portrait'));
        }
    }

    /**
     * 图片裁剪
     * @param 类型 参数 参数说明
     * @author staitc7 <static7@qq.com>
     * @return int|mixed
     */
    public function images($info = null) {
        $tmp_data = Request::instance()->post('avatar_data');
        $tmp_data || $this->error('坐标参数错误');
        is_file($info->getPathname()) || $this->error('文件错误');
        $coordinate = json_decode($tmp_data);
        $portrait_path = Config::get('portrait_path');
        $image = \think\Image::open($info->getPathname());
        $pic = $image->crop($coordinate->width, $coordinate->height, $coordinate->x, $coordinate->y)
                        ->thumb(64, 64)->save(getcwd() . $portrait_path . "uid_{$this->uid}." . $info->getExtension());
        is_object($pic) || $this->error('保存失败');
        $Picture = Db::name('Picture');
        $data = ['md5' => md5($info->getInfo()['tmp_name']), 'sha1' => sha1($info->getInfo()['tmp_name'])];
        $id = $Picture->where($data)->value('id');
        if (empty($id)) {
            $data['status'] = 1;
            $data['path'] = $portrait_path . "uid_{$this->uid}." . $info->getExtension();
            $data['create_time'] = $info->getATime();
            $info_id = $Picture->insertGetId($data);
            $id = $info_id ? (int) $info_id : 0;
        }
        return $id;
    }

    /**
     * 相册
     * @param 类型 参数 参数说明
     * @author staitc7 <static7@qq.com>
     */
    public function photoGallery() {
        $list = Loader::model('PhotoGallery', 'api')
                ->photoGalleryList(['user_id' => $this->uid], 'id,path,group');
        $value = [
            'list' => $list['data'] ?? null,
            'page' => $list['page']
        ];
        $this->view->metaTitle = '相册管理';
        return $this->view->assign($value)->fetch();
    }

    /**
     * 相册
     * @param 类型 参数 参数说明
     * @author staitc7 <static7@qq.com>
     */
    public function webuploader() {
        return $this->view->fetch();
    }

    /**
     * 图片批量上传
     * @author staitc7 <static7@qq.com>
     */
    public function uploader() {
        $info = Loader::model('PhotoGallery', 'api')->upload('file');
        if ((int) $info['status'] !== 1) {
            header("HTTP/1.1 404 Not Found");
            header("Status: 404 Not Found");
            exit;
        }
        return $this->success('上传成功');
    }

}
