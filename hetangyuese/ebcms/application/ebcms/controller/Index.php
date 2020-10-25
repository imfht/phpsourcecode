<?php
namespace app\ebcms\controller;
class Index extends \app\ebcms\controller\Common
{
    
    public function index()
    {
        if (request()->isGet()) {
            if ($tpl = input('tpl')) {
                if (in_array($tpl, ['main'])) {
                    return $this->fetch($tpl);
                }
            } else {
                return $this->fetch();
            }
        }
    }

    // 清理缓存
    public function runtime()
    {
        if (request()->isPost()) {
            \ebcms\Func::deldir(TEMP_PATH);
            \ebcms\Func::deldir(LOG_PATH);
            \think\Cache::clear();
            \ebcms\Func::deldir(RUNTIME_PATH . 'patch' . DS);
            $this->success('成功清理系统缓存！');
        }
    }

    // 修改自己的密码
    public function password()
    {
        if (request()->isGet()) {
            return \ebcms\Form::fetch();
        } elseif (request()->isPost()) {
            // 判断密码是否正确
            $manager = \think\Db::name('manager') -> find(\think\Session::get('manager_id'));
            if (\ebcms\Func::crypt_pwd(input('oldpassword'), $manager['email']) != $manager['password']) {
                $this->error('密码错误');
            }

            // 重置密码
            \think\Db::transaction(function() use($manager){
                \think\Db::name('manager') -> where('id', \think\Session::get('manager_id')) -> setField('password',\ebcms\Func::crypt_pwd(input('password'), $manager['email']));
            });
            $this->success('修改成功');
        }
    }

    // webuploader上传
    public function upload()
    {
        \think\Config::set('default_return_type', 'json');
        $file = request()->file('file');
        $file->validate([
            'size'  =>  2048000,
            // 'type'  =>  ['image/jpeg','image/png','image/jpg'],
            'ext'  =>  ['jpg','gif','bmp','png','zip','rar'],
        ]);
        $path = '';
        if (input('path')) {
            if (preg_match('/^[\/_0-9a-z]{1,200}$/i',input('path'))) {
                $path = input('path');
                $path = strpos($path, '/') !== 0 ? '/'.$path : $path;
            }
        }
        $info = $file->move('./upload' . $path);
        if (false !== $info) {
            $this->success('上传成功！', '', [
                'pathname' => substr(str_replace('\\', '/', $info->getPath() . '/' . $info->getBasename()), strlen('./upload')),
                'name' => $info->getBasename(),
            ]);
        } else {
            $this->error($file->getError());
        }
    }

    // 编辑器上传
    public function ueditor()
    {
        \think\Config::set('default_return_type', 'json');
        $config = [
            'catcherPathFormat' => '/image/{yyyy}{mm}{dd}/{time}{rand:6}',
            'fileManagerListPath' => '/file/',
            'filePathFormat' => '/file',
            'imageManagerListPath' => '/image/',
            'imagePathFormat' => '/image',
            'scrawlPathFormat' => '/image/{yyyy}{mm}{dd}/{time}{rand:6}',
            'snapscreenPathFormat' => '/image',
            'videoPathFormat' => '/video',
        ];
        $config = array_merge($config, (array)\ebcms\Config::get('system.ueditor.upload'));
        $data = new \ebcms\Ueditor($config);
        return $data->output();
    }

    // 发邮箱
    public function email(){
        if (request() -> isGet()) {
            return \ebcms\Form::fetch();
        }elseif (request() -> isPost()) {
            $email = input('email');
            $user = input('user','尊敬的用户');
            $topic = input('topic','系统消息');
            $content = input('content');
            if (\ebcms\Func::sendmail($email, $user, $topic, htmlspecialchars_decode($content))) {
                $this -> success('发送成功！');
            }else{
                $this -> error('发送失败！');
            }
        }
    }

}