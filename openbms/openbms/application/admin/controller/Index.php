<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Index extends AdminBase
{
    protected $noLogin = [
        'login',
        'captcha'
    ];
    protected $noAuth = [
        'index',
        'uploadImage',
        'uploadFile',
        'uploadVideo',
        'iconLibs',
        'logout'
    ];

    protected function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        // 快捷方式
        $where = ['index' => 1, 'status' => 1];
        if (session('admin_auth.username') != config('administrator')) {
            $access      = model('authGroupAccess')->with('authGroup')
                ->where('uid', session('admin_auth.admin_id'))->find();
            $where['id'] = ['in', $access['rules']];
        }
        $shortcut = model('authRule')->where($where)->order('pid asc,sort_order asc')->select();
        // 服务器信息
        $server = [
            'os'                  => PHP_OS, // 服务器操作系统
            'sapi'                => PHP_SAPI, // 服务器软件
            'version'             => PHP_VERSION, // PHP版本
            'mysql'               => db()->query('select VERSION() as version'), // mysql 版本
            'root'                => $_SERVER['DOCUMENT_ROOT'], // 当前运行脚本所在的文档根目录
            'max_execution_time'  => ini_get('max_execution_time') . 's', // 最大执行时间
            'upload_max_filesize' => ini_get('upload_max_filesize'), // 文件上传限制
            'memory_limit'        => ini_get('memory_limit'), // 允许内存大小
            'date'                => date('Y-m-d H:i:s', time()), // 服务器时间
        ];
        return $this->fetch('index', ['shortcut' => $shortcut, 'server' => $server]);
    }

    public function login()
    {
        is_admin_login() && $this->redirect('admin/index/index'); // 登录直接跳转
        if ($this->request->isPost()) {
            $param  = $this->request->param();
            $result = $this->validate($param, 'login');
            if ($result !== true) {
                $this->error($result);
            }
            $admin = model('admin')->where([
                'username' => $param['username'],
                'password' => md5($param['password'])
            ])->find();
            if ($admin) {
                $admin['status'] != 1 && $this->error('账号已禁用');
                // 保存状态
                $auth = [
                    'admin_id' => $admin['id'],
                    'username' => $admin['username'],
                ];
                session('admin_auth', $auth);
                session('admin_auth_sign', data_auth_sign($auth));
                // 更新信息
                model('admin')->save([
                    'last_login_time' => time(),
                    'last_login_ip'   => $this->request->ip(),
                    'login_count'     => $admin['login_count'] + 1,
                ], ['id' => $admin['id']]);
                insert_admin_log('登录了后台系统');
                $this->success('登录成功', url('admin/index/index'));
            } else {
                $this->error('账号或密码错误');
            }
        }
        return $this->fetch('login');
    }

    public function captcha()
    {
        $config = [
            // 验证码字符集合
            'codeSet'  => '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY',
            // 验证码字体大小(px)
            'fontSize' => 16,
            // 是否画混淆曲线
            'useCurve' => false,
            // 验证码图片高度
            'imageH'   => 42,
            // 验证码图片宽度
            'imageW'   => 135,
            // 验证码位数
            'length'   => 4,
            // 验证成功后是否重置
            'reset'    => true,
        ];
        return captcha('', $config);
    }

    public function uploadImage()
    {
        try {
            $file = $this->request->file('file');
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload' . DS . 'image');
            if ($info) {
                $upload_image = unserialize(config('upload_image'));
                if ($upload_image['is_thumb'] == 1 || $upload_image['is_water'] == 1 || $upload_image['is_text'] == 1) {
                    $object_image = \think\Image::open($info->getPathName());
                    // 图片压缩
                    if ($upload_image['is_thumb'] == 1) {
                        $object_image->thumb($upload_image['max_width'], $upload_image['max_height']);
                    }
                    // 图片水印
                    if ($upload_image['is_water'] == 1) {
                        $object_image->water(ROOT_PATH . str_replace('/', '\\', trim($upload_image['water_source'], '/')), $upload_image['water_locate'], $upload_image['water_alpha']);
                    }
                    // 文本水印
                    if ($upload_image['is_text'] == 1) {
                        $font = !empty($upload_image['text_font']) ? str_replace('/', '\\', trim($upload_image['text_font'], '/')) : 'vendor\topthink\think-captcha\assets\zhttfs\1.ttf';
                        $object_image->text($upload_image['text'], ROOT_PATH . $font, $upload_image['text_size'], $upload_image['text_color'], $upload_image['text_locate'], $upload_image['text_offset'], $upload_image['text_angle']);
                    }
                    $object_image->save($info->getPathName());
                }
                return ['code' => 1, 'url' => '/upload/image/' . str_replace('\\', '/', $info->getSaveName())];
            } else {
                return ['code' => 0, 'msg' => $file->getError()];
            }
        } catch (\Exception $e) {
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }

    public function uploadFile()
    {
        try {
            $file = $this->request->file('file');
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload' . DS . 'file');
            if ($info) {
                return ['code' => 1, 'url' => '/upload/file/' . str_replace('\\', '/', $info->getSaveName())];
            } else {
                return ['code' => 0, 'msg' => $file->getError()];
            }
        } catch (\Exception $e) {
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }

    public function uploadVideo()
    {
        try {
            $file = $this->request->file('file');
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload' . DS . 'video');
            if ($info) {
                return ['code' => 1, 'url' => '/upload/video/' . str_replace('\\', '/', $info->getSaveName())];
            } else {
                return ['code' => 0, 'msg' => $file->getError()];
            }
        } catch (\Exception $e) {
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }

    public function iconLibs()
    {
        return $this->fetch('iconLibs');
    }

    // 修改密码
    public function editPassword()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            // 验证条件
            empty($param['password']) && $this->error('请输入旧密码');
            empty($param['new_password']) && $this->error('请输入新密码');
            empty($param['rep_password']) && $this->error('请输入确认密码');
            !check_password($param['new_password'], 6, 16) && $this->error('请输入6-16位的密码');
            $param['new_password'] != $param['rep_password'] && $this->error('两次密码不一致');
            $admin = model('admin')->where('id', session('admin_auth.admin_id'))->find();
            $admin['password'] != md5($param['password']) && $this->error('旧密码错误');
            $data = ['id' => session('admin_auth.admin_id'), 'password' => $param['new_password']];
            if ($this->update('admin', $data, false) === true) {
                insert_admin_log('修改了登录密码');
                $this->success('更新成功', url('admin/index/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('editPassword');
    }

    // 退出登录
    public function logout()
    {
        insert_admin_log('退出了后台系统');
        session('admin_auth', null);
        session('admin_auth_sign', null);
        $this->redirect('admin/index/login');
    }

    // 清除缓存
    public function clear()
    {
        clear_cache();
        insert_admin_log('清除了系统缓存');
        $this->success('清除成功');
    }
}
