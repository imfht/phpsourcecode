<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\facade\Cache;
use app\admin\model\Admin as AdminModel;
use app\cms\model\News as NewsModel;
use app\common\widget\Widget;

class Index extends Base
{
    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 后台多语言切换
     */
    public function lang()
    {
        if (!request()->isAjax()) {
            $this->error('提交方式不正确');
        } else {
            $lang = input('lang_s');
            session('login_http_referer', $_SERVER["HTTP_REFERER"]);
            switch ($lang) {
                case 'cn':
                    cookie('think_var', 'zh-cn');
                    break;
                case 'en':
                    cookie('think_var', 'en-us');
                    break;
                //其它语言
                default:
                    cookie('think_var', 'zh-cn');
            }
            Cache::clear();
            $this->success('切换成功', session('login_http_referer'));
        }
    }

    /*
     * 清理缓存
     */
    public function clear()
    {
        Cache::clear();
        $this->success('清理缓存成功');
    }
    /**
     * 管理员信息
     * @throws
     */
    public function profile()
    {
        $admin_model         = new AdminModel();
        $admin               = $admin_model->alias("a")
            ->join(config('database.prefix') . 'auth_group_access b', 'a.id =b.uid')
            ->join(config('database.prefix') . 'auth_group c', 'b.group_id = c.id')
            ->where(['a.id' => session('admin_auth.aid')])
            ->find();
        $news_model          = new NewsModel();
        $news_count          = $news_model->where('author', session('admin_auth.uid'))->count();
        $admin['news_count'] = $news_count;
        $this->assign('admin', $admin);
        //头像剪裁
        $attr_data['jcrop'] = [
            'upload_path' => '/data/upload/avatar',
            'upload_url'  => url('avatar'),
            'id'          => 'jcrop_avatar',
            'title'       => '头像修改'
        ];
        $this->assign('attr_data', $attr_data);
        return $this->fetch();
    }
    /**
     * 管理员头像
     */
    public function avatar()
    {
        $imgurl = input('imgurl');
        //去'/'
        $imgurl = str_replace('/', '', $imgurl);
        $url    = '/data/upload/avatar/' . $imgurl;
        if (config('yfcmf.storage.storage_open')) {
            //七牛
            $upload = \Qiniu::instance();
            $info   = $upload->uploadOne('.' . $url, "image/");
            if ($info) {
                $imgurl = config('yfcmf.storage.domain') . $info['key'];
                @unlink('.' . $url);
            }
        }
        $admin_model         = new AdminModel();
        $rst = $admin_model->where('id', session('admin_auth.aid'))->setField('avatar', $imgurl);
        if ($rst !== false) {
            session('admin_auth.avatar', $imgurl);
            session('admin_auth_sign', data_signature(session('admin_auth')));
            $this->success('头像更新成功', 'profile');
        } else {
            $this->error('头像更新失败', 'profile');
        }
    }
    public function softIndex()
    {
        //表格字段
        $fields = [
            ['title' => '软件名称', 'field' => 'name'],
            ['title' => '说明', 'field' => 'desc'],
            ['title' => '上传日期', 'field' => 'date'],
        ];
        //主键
        $pk = 'id';
        //数据
        $data = [
            ['id' => 1, 'name' => '谷歌浏览器', 'desc' => '更好的体验html5+css3效果，下载后解压进行安装', 'date' => '2015-11-5', 'download' => 'http://dlsw.baidu.com/sw-search-sp/soft/9d/14744/ChromeStandalone_50.0.2661.87_Setup.1461306176.exe'],
            ['id' => 2, 'name' => 'winrar压缩解压软件', 'desc' => '用于解压压缩包文件，这里主要用于解压本系统软件包。', 'date' => '2015-11-5', 'download' => 'http://dlsw.baidu.com/sw-search-sp/soft/2e/10849/wrar_5.30.0.0sc.1452057954.exe']
        ];
        //右侧操作按钮
        $right_action = [
            'download' => ['title' => '下载', 'field' => 'download', 'icon' => 'fa fa-cloud-download'],
        ];
        //实例化表单类
        $widget = new Widget();
        return $widget
            ->addtable($fields, $pk, $data, $right_action)
            ->setButton([])
            ->fetch();
    }
    public function formtest()
    {
        $widget = new Widget();
        return $widget
            ->addItem('captcha', 'verify','','验证码', [], 'required')
            ->setUrl(url('newsSave'))
            ->setAjax('ajaxForm-noJump')
            ->fetch();
    }
}
