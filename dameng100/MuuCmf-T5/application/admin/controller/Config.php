<?php
namespace app\admin\controller;

use app\admin\controller\Admin;
use think\Db;
use app\admin\builder\AdminConfigBuilder;

/**
 * 后台配置控制器
 */
class Config extends Admin
{

    /**
     * 配置管理
     */
    public function index()
    {
        /* 查询条件初始化 */
        $map = [];
        $map = ['status' => 1, 'title' => array('neq', '')];
        if (isset($_GET['group'])) {
            $map['group'] = input('group', 0);
        }
        if (isset($_GET['name'])) {
            $map['name'] = array('like', '%' . (string)input('name') . '%');
        }
        // $map=
        list($list,$page) = $this->commonLists('Config', $map, 'sort,id');
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $list = $list->toArray()['data'];
        
        $this->assign('group', config('CONFIG_GROUP_LIST'));
        $this->assign('group_id', input('get.group', 0));
        $this->assign('list', $list);
        $this->setTitle(lang('_CONFIG_MANAGER_'));
        return $this->fetch();
    }

    /**
     * 编辑配置
     */
    public function edit($id = 0)
    {
        if (request()->isPost()) {
            $data = input('');
            //验证器
            $validate = $this->validate(
                [
                    'name'  => $data['name'],
                    'title'   => $data['title'],
                ],[
                    'name'  => 'require|max:25',
                    'title'   => 'require',
                ],[
                    'name.require' => '标识必须填写',
                    'name.max'     => '标识最多不能超过25个字符',
                    'title.require'   => '标题必须填写', 
                ]
            );
            if(true !== $validate){
                // 验证失败 输出错误信息
                $this->error($validate);
            }

            $data['status'] = 1;//默认状态为启用

            $res = $resId = model('Config')->editData($data);
            cache('DB_CONFIG_DATA', null);
            //记录行为
            action_log('update_config', 'config', $resId, is_login());
            $this->success(lang('_SUCCESS_UPDATE_'), Cookie('__forward__'));
            
        } else {
            /* 获取数据 */
            if($id != 0){
                $info = model('Config')->getDataById($id);
            }else{
                $info = [];
            }

            $this->assign('info', $info);
            $this->setTitle(lang('_CONFIG_EDIT_'));
            return $this->fetch();
        }
    }

    /**
     * 批量保存配置
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function save($config)
    {
        if ($config && is_array($config)) {
            $Config = Db::name('Config');
            foreach ($config as $name => $value) {
                $map = array('name' => $name);
                $Config->where($map)->setField('value', $value);
            }
        }
        cache('DB_CONFIG_DATA', null);
        $this->success(lang('_SUCCESS_SAVE_').lang('_EXCLAMATION_'));
    }

    /**
     * 删除配置
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function del()
    {
        $id = array_unique((array)input('id', 0));

        if (empty($id)) {
            $this->error(lang('_DATA_OPERATE_SELECT_'));
        }

        $map = array('id' => array('in', $id));
        if (Db::name('Config')->where($map)->delete()) {
            cache('DB_CONFIG_DATA', null);
            //记录行为
            action_log('update_config', 'config', $id, UID);
            $this->success(lang('_SUCCESS_DELETE_').lang('_EXCLAMATION_'));
        } else {
            $this->error(lang('_FAIL_DELETE_').lang('_EXCLAMATION_'));
        }
    }

    // 获取某个标签的配置参数
    public function group()
    {
        $id = input('id', 1,'intval');
        $type = config('CONFIG_GROUP_LIST');

        $list = Db::name("Config")->where(['status' => 1, 'group' => $id])->field('id,name,title,extra,value,group,remark,type')->order('sort asc')->select();

        if ($list) {
            $this->assign('list', $list);
        }
        $this->assign('id', $id);
        $this->assign('type', $type);
        $this->setTitle($type[$id] . lang('_SETTINGS_'));
        return $this->fetch();
    }

    /**
     * 配置排序
     * @author huajie <banhuajie@163.com>
     */
    public function sort()
    {
        if (request()->isGet()) {
            $ids = input('get.ids');

            //获取排序的数据
            $map = array('status' => array('gt', -1), 'title' => array('neq', ''));
            if (!empty($ids)) {
                $map['id'] = array('in', $ids);
            } elseif (input('group')) {
                $map['group'] = input('group');
            }
            $list = M('Config')->where($map)->field('id,title')->order('sort asc,id asc')->select();

            $this->assign('list', $list);
            $this->meta_title = lang('_CONFIG_SORT_');
            $this->display();
        } elseif (request()->isPost()) {
            $ids = input('post.ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key => $value) {
                $res = Db::name('Config')->where(['id' => $value])->setField('sort', $key + 1);
            }
            if ($res !== false) {
                $this->success(lang('_SUCCESS_SORT_').lang('_EXCLAMATION_'), Cookie('__forward__'));
            } else {
                $this->eorror(lang('_FAIL_SORT_').lang('_EXCLAMATION_'));
            }
        } else {
            $this->error(lang('_BAD_REQUEST_').lang('_EXCLAMATION_'));
        }
    }

    /**网站信息设置
     * @auth 陈一枭
     */
    public function website()
    {
        $builder = new AdminConfigBuilder();
        $data = $builder->handleConfig();
        $builder->title(lang('_SITE_INFO_'))->suggest(lang('_SITE_INFO_VICE_'));
        /*        $builder->keySelect('LANG', lang('_WEBSITE_LANGUAGE_'), lang('_SELECT_THE_DEFAULT_LANGUAGE_'), array('zh-cn' => lang('_SIMPLIFIED_CHINESE_'), 'en-us' => lang('_ENGLISH_')));*/
        $builder->keyText('WEB_SITE_NAME', lang('_SITE_NAME_'), lang('_SITE_NAME_VICE_'));
        $builder->keyText('ICP', lang('_LICENSE_NO_'), lang('_LICENSE_NO_VICE_'));

        $builder->keySingleImage('LOGO', lang('_SITE_LOGO_'), lang('_SITE_LOGO_VICE_'));
        $builder->keySingleImage('QRCODE', lang('_QR_WEIXIN_'), lang('_QR_WEIXIN_VICE_'));


        $builder->keySingleImage('JUMP_BACKGROUND', lang('_IMG_BG_REDIRECTED_'), lang('_IMG_BG_REDIRECTED_'));
        $builder->keyText('SUCCESS_WAIT_TIME', lang('_TIME_SUCCESS_WAIT_'), lang('_TIME_SUCCESS_WAIT_VICE_'));
        $builder->keyText('ERROR_WAIT_TIME', lang('_TIME_FAIL_WAIT_'), lang('_TIME_FAIL_WAIT_VICE_'));

        $builder->keyTextArea('ABOUT_US', lang('_CONTENT_ABOUT_US_'), lang('_CONTENT_ABOUT_US_VICE_'),'all');
        $builder->keyTextArea('SUBSCRIB_US', lang('_CONTENT_FOLLOW_US_'), lang('_CONTENT_FOLLOW_US_VICE_'));
        $builder->keyTextArea('COPY_RIGHT', lang('_INFO_COPYRIGHT_'), lang('_INFO_COPYRIGHT_VICE_'));

        
        $builder->group(lang('_BASIC_INFORMATION_'), array('WEB_SITE_NAME', 'ICP', 'LOGO', 'QRCODE', 'LANG'));

        $builder->group(lang('_THE_FOOTER_INFORMATION_'), array('ABOUT_US', 'SUBSCRIB_US', 'COPY_RIGHT'));

        $builder->group(lang('_JUMP_PAGE_'), array('JUMP_BACKGROUND', 'SUCCESS_WAIT_TIME', 'ERROR_WAIT_TIME'));
        $builder->keyBool('GET_INFORMATION', lang('_OPEN_INSTANT_ACCESS_TO_THE_MESSAGE_'),lang('_OPEN_INSTANT_ACCESS_TO_THE_MESSAGE_VICE_'));
        $builder->keyText('GET_INFORMATION_INTERNAL', lang('_MESSAGE_POLLING_INTERVAL_'), lang('_MESSAGE_POLLING_INTERVAL_VICE_'));
        $builder->group(lang('_PERFORMANCE_SETTINGS_'), array('GET_INFORMATION','GET_INFORMATION_INTERNAL'));
        

        $builder->data($data);
        $builder->keyDefault('SUCCESS_WAIT_TIME', 2);
        $builder->keyDefault('ERROR_WAIT_TIME', 5);
        $builder->keyDefault('LANG', 'zh-cn');
        $builder->keyDefault('GET_INFORMATION',1);
        $builder->keyDefault('GET_INFORMATION_INTERNAL',10);

        $builder->buttonSubmit();
        $builder->display();
    }
    /**
     * 扩展配置
     * @return [type] [description]
     */
    public function expandConfig(){
        $builder = new AdminConfigBuilder();
        $data = $builder->handleConfig();
        $builder->title(lang('_SITE_INFO_'))->suggest(lang('_SITE_INFO_VICE_'));

        //上传功能
        $addons = \think\Hook::get('uploadDriver');
        $opt = array('local' => lang('_LOCAL_'));
        foreach ($addons as $name) {
            if (class_exists($name)) {
                $class = new $name();
                $config = $class->getConfig();
                if ($config['switch']) {
                    $opt[$class->info['name']] = $class->info['title'];
                }

            }
        }

        $builder->keySelect('PICTURE_UPLOAD_DRIVER', lang('_PICTURE_UPLOAD_DRIVER_'), lang('_PICTURE_UPLOAD_DRIVER_'), $opt);
        $builder->keySelect('DOWNLOAD_UPLOAD_DRIVER', lang('_ATTACHMENT_UPLOAD_DRIVER_'), lang('_ATTACHMENT_UPLOAD_DRIVER_'), $opt);


        $builder->group(lang('_UPLOAD_CONFIGURATION_'), array('PICTURE_UPLOAD_DRIVER', 'DOWNLOAD_UPLOAD_DRIVER'));
        unset($opt);
        
        //短信验证
        //短信插件需放置在sms钩子内
        $addons = \Think\Hook::get('sms');
        $opt = array('none' => lang('_NONE_'));
        foreach ($addons as $name) {
            if (class_exists($name)) {
                $class = new $name();
                $config = $class->getConfig();
                if ($config['switch']) {
                    $opt[$class->info['name']] = $class->info['title'];
                }
            }
        }
        $builder
            ->keySelect('SMS_HOOK', lang('_SMS_SENDING_SERVICE_PROVIDER_'), lang('_SMS_SEND_SERVICE_PROVIDERS_NEED_TO_INSTALL_THE_PLUG-IN_'), $opt)
            ->keyText('SMS_SIGN', lang('_SMS_PLATFORM_SIGN_'), lang('_SMS_PLATFORM_SIGN_CONT_'))
            ->keyDefault('SMS_SIGN','【MuuCmf】');

        $builder
            ->group(lang('_SMS_CONFIGURATION_'), 'SMS_HOOK,SMS_SIGN');
        unset($opt);

        $builder->data($data);
        $builder->buttonSubmit();
        $builder->display();
    }
}
