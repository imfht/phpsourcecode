<?php
namespace app\home\controller;

use app\common\controller\Home;

class Install extends Home
{
    protected function initialize()
    {
    }
    
    public function index()
    {
        header('Content-Type:text/html;charset=utf-8');         
        
        if (file_exists(dirname(__FILE__) . DS . 'install.lock')) {
            $this->redirect('home/Index/index');
            exit;
        }
        
        $dbconfig = config('database.');
        
        //连接数据库
        $dsn = "mysql:host={$dbconfig['hostname']};port=3306;charset=utf8";
        try {
            $db = new \PDO($dsn, $dbconfig['username'], $dbconfig['password']);
        } catch (\PDOException $e) {
            echo '失败：数据库连接失败<br>';
            exit();
        }
        
        //建立数据库
        $dbname = $dbconfig['database']; 
        $sql = "CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8";
        
        if(!$db->exec($sql)) {
            echo '失败：数据库`' . $dbname . '`创建失败<br>';
        } else {
            echo '成功：数据库`' . $dbname . '`创建成功<br>';
        }        
        
        if (!file_exists(dirname(WWW_ROOT) . DS . 'data' . DS . 'database.sql')) {
            echo '失败：' . dirname(WWW_ROOT) . DS . 'data' . DS . 'database.sql' . '文件不存在';
            exit ;
        }
        
        $dbSql = file_get_contents(dirname(WWW_ROOT) . DS . 'data' . DS . 'database.sql');
        $dbSql = str_replace("\r", "\n", $dbSql);
        $dbSql = explode(";\n", $dbSql);
        //替换表前缀
        $default_tablepre = "woo_";
        $table_prefix = $dbconfig['prefix'];
        $dbSql = str_replace(" `{$default_tablepre}", " `{$table_prefix}", $dbSql);
        
        foreach ($dbSql as $item) {
            $item = trim($item);
            if (empty($item)) {
                continue;
            }
            preg_match('/CREATE TABLE IF NOT EXISTS `([^ ]*)`/i', $item, $matches);
            if ($matches) {
                $table_name = $matches[1];
                db()->query($item);
                echo "成功：数据表`{$table_name}`创建成功<br>";
            } else {
                db()->query($item);
            }
        }
        
        
        echo '----------------------------<br>';       
        ##Menu
        $this->loadModel('Menu');
        $count = $this->Menu->count();
        if (!$count) {
            $this->Menu->isValidate(false)->save(['id' => 1, 'parent_id' => 0, 'family' => ',1,', 'title' => '网站导航', 'type' => 'Menu']);
            echo '成功：导航安装成功<br>';
        } else {
            echo '失败：导航已安装<br>';
        }
        
        ##ManageMenu
        $this->loadModel('ManageMenu');
        $count = $this->ManageMenu->count();
        if (!$count) {
            $this->ManageMenu->isValidate(false)->save(['id' => 1, 'parent_id' => 0, 'title' => '用户导航']);
            echo '成功：用户导航安装成功<br>';
        } else {
            echo '失败：用户导航已安装<br>';
        }
        
        
        ##AdPosition
        $this->loadModel('AdPosition');
        $count = $this->AdPosition->count();
        if (!$count) {
            $this->AdPosition->saveAll([
                ['title' => '首页Banner广告位', 'vari' => 'index_banner', 'width' => 1920 , 'height' => 600, 'mobile_width' => 600, 'mobile_height' => 450, 'is_thumb' => 1],
                ['title' => '内页Banner广告位', 'vari' => 'insider_banner', 'width' => 1920 , 'height' => 400, 'mobile_width' => 600, 'mobile_height' => 450, 'is_thumb' => 1]
            ]);
            echo '成功：广告位安装成功<br>';
        } else {
            echo '失败：广告位已安装<br>';
        }
        
        ##AdminMenu
        $this->loadModel('AdminMenu');
        $count = $this->AdminMenu->count();
        if (!$count) {
            $insert  = "INSERT INTO `woo_admin_menu` (`id`, `parent_id`, `title`, `url`, `icon`, `is_nav`, `is_debug`, `list_order`) VALUES
(1, 0, '后台导航', '', '', 0, 0, 0),
(2, 1, '内容', '', '', 1, 0, 0),
(3, 1, '微信', '', '', 0, 0, 0),
(4, 1, '商城', '', '', 0, 0, 0),
(40, 38, '数据管理', '', 'fa-database', 1, 0, 0),
(6, 8, '前台栏目', '/run/menu/lists', '', 1, 0, 0),
(7, 39, '后台栏目', '/run/admin_menu/lists', '', 1, 1, 0),
(8, 2, '内容管理', '', 'fa-file-text', 1, 0, 1),
(9, 8, '前台内容', '/run/menu/content', '', 1, 0, 1),
(10, 8, '回收站', 'run/dustbin/lists', '', 1, 0, 3),
(11, 37, '用户管理', '', 'fa-user', 1, 0, 2),
(12, 11, '用户列表', '/run/user/lists', '', 1, 0, 0),
(13, 11, '添加用户', '/run/user/create', '', 1, 0, 0),
(14, 11, '用户组列表', '/run/user_group/lists', '', 1, 0, 0),
(15, 2, '广告管理', '', 'fa-file-image-o', 1, 0, 4),
(16, 15, '广告位列表', '/run/ad_position/lists', '', 1, 0, 0),
(17, 15, '添加广告位', '/run/ad_position/create', '', 1, 0, 0),
(18, 38, '字典管理', '', 'fa-th-large', 1, 0, 5),
(19, 18, '字典列表', '/run/dictionary/lists', '', 1, 0, 0),
(20, 18, '添加字典', '/run/dictionary/create', '', 1, 0, 0),
(21, 37, '系统设置', '', 'fa-gears', 1, 0, 6),
(22, 21, '设置列表', '/run/setting/lists', '', 1, 0, 0),
(23, 21, '设置组列表', '/run/setting_group/lists', '', 1, 0, 0),
(24, 38, '开发工具', '', 'fa-search-plus', 1, 1, 7),
(25, 24, '模型管理', '/run/model/lists', 'fa-file-text', 1, 1, 0),
(26, 24, '邮件模板', '/run/email/lists', 'fa-envelope', 1, 1, 0),
(27, 24, '提取数据', '/run/query_data/lists', 'fa-line-chart', 1, 1, 0),
(28, 24, '模型生成', '/run/tool/addm', 'fa-book', 1, 1, 0),
(29, 24, '模板创建', '/run/tool/addv', 'fa-camera', 1, 1, 0),
(30, 24, '控制器生成', '/run/tool/addc', 'fa-paper-plane', 1, 1, 0),
(31, 37, '权限管理', '', 'fa-key', 1, 0, 3),
(32, 31, '用户授权', '/run/power/content', '', 1, 0, 0),
(33, 31, '权限节点', '/run/power_tree/lists', 'fa-tree', 1, 0, 0),
(35, 24, '地区管理', '/run/region/lists', 'fa-map-marker', 1, 0, 0),
(37, 1, '系统', '', '', 1, 0, 0),
(38, 1, '其他', '', '', 1, 0, 0),
(39, 37, '后台操作', '', 'fa-bar-chart', 1, 0, 0),
(41, 40, '数据备份', '/run/database/lists', '', 1, 0, 0),
(42, 40, '文件操作', '/run/database/filelist', '', 1, 0, 0),
(43, 8, '用户栏目', '/run/manage_menu/lists', '', 1, 0, 2);";  
            $insert = str_replace(" `{$default_tablepre}", " `{$table_prefix}", $insert);          
            db('AdminMenu')->execute($insert);
            $this->AdminMenu->writeToFile();
            echo '成功：后台栏目安装成功<br>';
        } else {
            echo '失败：后台栏目已安装<br>';
        }
        
        ##UserGroup
        $this->loadModel('UserGroup');
        $count =  $this->UserGroup->count();
        if (!$count) {
            $this->UserGroup->saveAll([
                ['title' => '后台管理员', 'alias' => 'Admin' ,'is_admin' => 1],
                ['title' => '注册会员', 'alias' => 'Member' ,'is_admin' => 0],
            ]);
            echo '成功：用户组安装成功<br>';
        } else {
            echo '失败：用户组已安装<br>';
        }
        
        ##User
        $this->loadModel('User');
        $count =  $this->User->count();
        if (!$count) {
            $group = $this->UserGroup->order(['id' => 'ASC'])->select();
            $this->User->save(['id' => 1, 'username' => 'admin', 'password' => 'admin', 'user_group_id' => $group[0]['id'], 'status' => 'verified']);
            echo '成功：用户安装成功<br>';
        } else {
            echo '失败：用户已安装<br>';
        }
        
        ##Model
        $this->loadModel('Model');
        $count =  $this->Model->count();
        if (!$count) {
            $now  = date('Y-m-d H:i:s');
            $insert = "INSERT INTO `woo_model` (`id`, `model`, `cname`, `is_menu`, `is_power`, `is_dustbin`, `created`, `modified`) VALUES
(1, 'Menu', '栏目', 1, 0, 0, '{$now}', '{$now}'),
(2, 'Article', '文章', 1, 0, 1, '{$now}', '{$now}'),
(3, 'Page', '单页', 1, 0, 1, '{$now}', '{$now}'),
(4, 'Product', '产品', 1, 0, 1, '{$now}', '{$now}'),
(5, 'Link', '链接', 1, 0, 1, '{$now}', '{$now}'),
(6, 'Download', '下载', 1, 0, 1, '{$now}', '{$now}'),
(7, 'Feedback', '留言', 1, 0, 1, '{$now}', '{$now}'),
(8, 'Album', '图集', 1, 0, 1, '{$now}', '{$now}'),
(9, 'Picture', '图片', 0, 0, 0, '{$now}', '{$now}'),
(10, 'AlbumPicture', '相册图片', 0, 0, 0, '{$now}', '{$now}'),
(11, 'ProductPicture', '产品图片', 0, 0, 0, '{$now}', '{$now}'),
(12, 'ArticlePicture', '文章图片', 0, 0, 0, '{$now}', '{$now}'),
(13, 'SettingGroup', '系统设置组', 0, 0, 0, '{$now}', '{$now}'),
(14, 'Setting', '系统设置项', 0, 0, 0, '{$now}', '{$now}'),
(15, 'Dustbin', '回收站项', 0, 0, 0, '{$now}', '{$now}'),
(16, 'UserGroup', '用户组', 0, 0, 0, '{$now}', '{$now}'),
(17, 'User', '用户', 0, 0, 0, '{$now}', '{$now}'),
(18, 'Member', '用户信息', 0, 0, 0, '{$now}', '{$now}'),
(19, 'UserLogin', '用户登录', 0, 0, 0, '{$now}', '{$now}'),
(20, 'Dictionary', '字典', 0, 0, 0, '{$now}', '{$now}'),
(21, 'DictionaryItem', '字典项', 0, 0, 0, '{$now}', '{$now}'),
(22, 'AdPosition', '广告位', 0, 0, 0, '{$now}', '{$now}'),
(23, 'Ad', '广告', 0, 0, 0, '{$now}', '{$now}'),
(24, 'Model', '模型', 0, 0, 0, '{$now}', '{$now}'),
(25, 'AdminMenu', '后台栏目', 0, 0, 0, '{$now}', '{$now}'),
(26, 'ManageMenu', '用户栏目', 0, 0, 0, '{$now}', '{$now}'),
(27, 'QueryData', '数据查询', 0, 0, 0, '{$now}', '{$now}'),
(28, 'Exlink', '栏目外链', 1, 0, 0, '{$now}', '{$now}'),
(29, 'PowerTree', '权限节点', 0, 0, 0, '{$now}', '{$now}'),
(30, 'Power', '权限', 0, 0, 0, '{$now}', '{$now}'),
(31, 'Email', '邮件模板', 0, 0, 0, '{$now}', '{$now}'),
(32, 'Region', '地区', 0, 0, 0, '{$now}', '{$now}');";
            $insert = str_replace(" `{$default_tablepre}", " `{$table_prefix}", $insert); 
            db('Model')->execute($insert);
            $this->Model->writeToFile();            
            echo '成功：模型安装成功<br>';
        } else {
            echo '失败：模型已安装<br>';
        }
        
        ##QueryData
        $this->loadModel('QueryData');
        $count =  $this->QueryData->count();
        if (!$count) {
            $insert = "INSERT INTO `woo_query_data` (`id`, `title`, `query`, `controller`, `menu_id`, `list_count`, `is_family`, `is_verify`, `type`, `contain`, `where`, `field`, `order`) VALUES
(1, '示范数据', 'index', '', 2, 4, 1, 0, 'select', '[''Menu'']', '[''is_index''=>1]', '[''id'',''title'',''date'',''image'']', '[''is_index''=>''DESC'']');";
            $insert = str_replace(" `{$default_tablepre}", " `{$table_prefix}", $insert); 
            db('QueryData')->execute($insert);
            echo '成功：数据提取安装成功<br>';
        } else {
            echo '失败：数据提取已安装<br>';
        }
        
        ##Email
        $this->loadModel('Email');
        $count =  $this->Email->count();
        if (!$count) {
            $insert = 'INSERT INTO `woo_email` (`id`, `title`, `vari`, `email_title`, `fromname`, `file`, `content`, `created`, `modified`) VALUES
(1, "注册验证模板", "register_verify", "注册验证模板", "", "", "<p>$username，你好</p>\r\n\r\n<p>您已经成功注册为<strong>$site_title</strong>会员！</p>\r\n\r\n<p><strong>请点击以下链接激活此帐号：</strong></p>\r\n\r\n<p><a href=\"$link\">$link</a></p>\r\n\r\n<p>此邮件由系统自动发送，请勿直接回复！</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p style=\"text-align: right;\">$datetime</p>\r\n\r\n<p style=\"text-align: right;\">$site_title</p>\r\n\r\n<p style=\"text-align: right;\">&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n", "2017-12-09 16:44:30", "2017-12-11 11:13:42");';
            $insert = str_replace(" `{$default_tablepre}", " `{$table_prefix}", $insert); 
            db('Email')->execute($insert);
            echo '成功：邮件模板安装成功<br>';
        } else {
            echo '失败：邮件模板已安装<br>';
        }
        
        ##导入地区数据
        if (file_exists(dirname(WWW_ROOT) . DS . 'data' . DS . 'region.sql')) {
            $insert = file_get_contents(dirname(WWW_ROOT) . DS . 'data' . DS . 'region.sql');
            $insert = str_replace(" `{$default_tablepre}", " `{$table_prefix}", $insert);
            db()->execute($insert);
            echo '成功：地区数据安装成功<br>';
        }
        
        
        
        
        ##系统设置分组
        $this->loadModel('SettingGroup');
        $count =  $this->SettingGroup->count();
        if (!$count) {
            $data = [
                [
                    'title' => '站点信息'
                ],
                [
                    'title' => '全局设置'
                ],
                [
                    'title' => '图片设置'
                ],
                [
                    'title' => '邮件配置'
                ],
                [
                    'title' => '短信配置'
                ],
                [
                    'title' => '优图配置'
                ]
            ];
            $this->SettingGroup->saveAll($data);
            echo  '成功：系统设置组安装成功<br>';
        } else {
            echo  '失败：系统设置组已安装<br>';
        }
        
        
        ##系统设置
        $this->loadModel('Setting');
        $count =  $this->Setting->count();
        if (!$count) {
            $setting_group = $this->SettingGroup->order(['id' => 'ASC'])->select();
            $data = [
                [
                    'title' => '网站名称',
                    'setting_group_id' => $setting_group[0]['id'],
                    'vari' => 'site_title',
                    'value' => 'WooCMS',
                    'type' => 'text',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => '网站关键字',
                    'setting_group_id' => $setting_group[0]['id'],
                    'vari' => 'site_keywords',
                    'value' => '',
                    'type' => 'text',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => '网站描述',
                    'setting_group_id' => $setting_group[0]['id'],
                    'vari' => 'site_description',
                    'value' => '',
                    'type' => 'textarea',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => '版权描述',
                    'setting_group_id' => $setting_group[0]['id'],
                    'vari' => 'site_copyright',
                    'value' => '&copy;WooCMS 版权所有',
                    'type' => 'text',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => '统计代码',
                    'setting_group_id' => $setting_group[0]['id'],
                    'vari' => 'site_code',
                    'value' => '',
                    'type' => 'textarea',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => '公司名称',
                    'setting_group_id' => $setting_group[0]['id'],
                    'vari' => 'corp_title',
                    'value' => '公司名称',
                    'type' => 'text',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => '公司电话',
                    'setting_group_id' => $setting_group[0]['id'],
                    'vari' => 'tel',
                    'value' => '公司电话',
                    'type' => 'text',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => '公司邮箱',
                    'setting_group_id' => $setting_group[0]['id'],
                    'vari' => 'email',
                    'value' => '公司邮箱',
                    'type' => 'text',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => '公司地址',
                    'setting_group_id' => $setting_group[0]['id'],
                    'vari' => 'address',
                    'value' => '公司地址',
                    'type' => 'text',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => 'ICP备案',
                    'setting_group_id' => $setting_group[0]['id'],
                    'vari' => 'icp',
                    'value' => '蜀ICP备xxxxxx号',
                    'type' => 'text',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => '公众号二维码',
                    'setting_group_id' => $setting_group[0]['id'],
                    'vari' => 'public_qrcode',
                    'value' => '',
                    'type' => 'file',
                    'options' => '',
                    'info' => ''
                ],                
                [
                    'title' => '开启审核',
                    'setting_group_id' => $setting_group[1]['id'],
                    'vari' => 'is_verify',
                    'value' => '1',
                    'type' => 'checker',
                    'options' => '',
                    'info' => '如果开启，前台数据必须审核通过以后才能显示'
                ],
                [
                    'title' => '审核默认值',
                    'setting_group_id' => $setting_group[1]['id'],
                    'vari' => 'default_verify',
                    'value' => '1',
                    'type' => 'radio',
                    'options' => '{"1":"默认审核","0":"默认不审核"}',
                    'info' => ''
                ],
                [
                    'title' => '注册邮箱认证',
                    'setting_group_id' => $setting_group[1]['id'],
                    'vari' => 'is_email_verify',
                    'value' => '0',
                    'type' => 'checker',
                    'options' => '',
                    'info' => '如果勾选，注册的时候必须通过邮箱认证'
                ],
                [
                    'title' => '移动端使用wap层',
                    'setting_group_id' => $setting_group[1]['id'],
                    'vari' => 'is_use_wap',
                    'value' => '0',
                    'type' => 'checker',
                    'options' => '',
                    'info' => '如果勾选，移动端访问将使用wap视图层'
                ],
                [
                    'title' => '全局分页条数',
                    'setting_group_id' => $setting_group[1]['id'],
                    'vari' => 'list_count',
                    'value' => '15',
                    'type' => 'text',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => '后台分页条数',
                    'setting_group_id' => $setting_group[1]['id'],
                    'vari' => 'admin_list_count',
                    'value' => '15',
                    'type' => 'text',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => '后台列表缓存',
                    'setting_group_id' => $setting_group[1]['id'],
                    'vari' => 'is_admin_cache',
                    'value' => '0',
                    'type' => 'checker',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => '留言是否显示列表',
                    'setting_group_id' => $setting_group[1]['id'],
                    'vari' => 'is_feedback_list',
                    'value' => '1',
                    'type' => 'checker',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => '开启栏目广告位',
                    'setting_group_id' => $setting_group[1]['id'],
                    'vari' => 'is_menu_position',
                    'value' => '0',
                    'type' => 'checker',
                    'options' => '',
                    'info' => '开启以后不同栏目可以单独创建对应栏目广告位'
                ],
                [
                    'title' => '是否开启Trace',
                    'setting_group_id' => $setting_group[1]['id'],
                    'vari' => 'is_trace',
                    'value' => '1',
                    'type' => 'checker',
                    'options' => '',
                    'info' => '建议程序员操作'
                ],
                [
                    'title' => '是否开启Debug',
                    'setting_group_id' => $setting_group[1]['id'],
                    'vari' => 'is_debug',
                    'value' => '1',
                    'type' => 'checker',
                    'options' => '',
                    'info' => '网站上线后不建议开启；建议程序员操作'
                ],
                [
                    'title' => '图片关联模型',
                    'setting_group_id' => $setting_group[2]['id'],
                    'vari' => 'use_picture_model',
                    'value' => '["Album"]',
                    'type' => 'checkbox',
                    'options' => '{"Album":"图集","Product":"产品","Article":"文章"}',
                    'info' => ''
                ],
                [
                    'title' => '全局缩略图类型',
                    'setting_group_id' => $setting_group[2]['id'],
                    'vari' => 'thumb_method',
                    'value' => '3',
                    'type' => 'radio',
                    'options' => '{"-1":"系统默认","1":"等比例缩放","2":"缩放后填充","3":"居中裁剪","4":"左上角裁剪","5":"右下角裁剪","6":"固定尺寸缩放"}',
                    'info' => ''
                ],
                [
                    'title' => '全局缩略图宽度',
                    'setting_group_id' => $setting_group[2]['id'],
                    'vari' => 'thumb_width',
                    'value' => '400',
                    'type' => 'text',
                    'options' => '',
                    'info' => '请填写整数，单位：像素'
                ],
                [
                    'title' => '全局缩略图高度',
                    'setting_group_id' => $setting_group[2]['id'],
                    'vari' => 'thumb_height',
                    'value' => '300',
                    'type' => 'text',
                    'options' => '',
                    'info' => '请填写整数，单位：像素'
                ],
                [
                    'title' => '全局默认图片',
                    'setting_group_id' => $setting_group[2]['id'],
                    'vari' => 'default_image',
                    'value' => '',
                    'type' => 'file',
                    'options' => '',
                    'info' => '适用于列表页必须图片时，而又没有上传图片的数据'
                ],
                [
                    'title' => '开启图片水印',
                    'setting_group_id' => $setting_group[2]['id'],
                    'vari' => 'is_water',
                    'value' => '0',
                    'type' => 'checker',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => '水印模型',
                    'setting_group_id' => $setting_group[2]['id'],
                    'vari' => 'water_model',
                    'value' => '',
                    'type' => 'checkbox',
                    'options' => '{"Article":"文章","Product":"产品","Album":"图集","AlbumPicture":"图集图片","Ad":"广告","Download":"下载","Page":"单页"}',
                    'info' => ''
                ],
                [
                    'title' => '水印类型',
                    'setting_group_id' => $setting_group[2]['id'],
                    'vari' => 'water_type',
                    'value' => 'text',
                    'type' => 'radio',
                    'options' => '{"text":"文字水印","image":"图片水印"}',
                    'info' => ''
                ],
                [
                    'title' => '水印位置',
                    'setting_group_id' => $setting_group[2]['id'],
                    'vari' => 'water_location',
                    'value' => '9',
                    'type' => 'radio',
                    'options' => '{"1":"左上","2":"上居中","3":"右上","4":"左中","5":"居中","6":"右中","7":"左下","8":"下居中","9":"右下"}',
                    'info' => ''
                ],
                [
                    'title' => '水印图片',
                    'setting_group_id' => $setting_group[2]['id'],
                    'vari' => 'water_image',
                    'value' => '',
                    'type' => 'file',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => '水印图片透明度',
                    'setting_group_id' => $setting_group[2]['id'],
                    'vari' => 'water_image_opacity',
                    'value' => '100',
                    'type' => 'text',
                    'options' => '',
                    'info' => '填写数值，范围1~100，100表示不透明'
                ],
                [
                    'title' => '水印文字',
                    'setting_group_id' => $setting_group[2]['id'],
                    'vari' => 'water_text',
                    'value' => '',
                    'type' => 'text',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => '水印文字大小',
                    'setting_group_id' => $setting_group[2]['id'],
                    'vari' => 'water_text_size',
                    'value' => '20',
                    'type' => 'text',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => '水印文字颜色',
                    'setting_group_id' => $setting_group[2]['id'],
                    'vari' => 'water_text_color',
                    'value' => '#ffffff',
                    'type' => 'color',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => '服务器地址',
                    'setting_group_id' => $setting_group[3]['id'],
                    'vari' => 'email_host',
                    'value' => 'smtp.163.com',
                    'type' => 'text',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => '发件邮箱账号',
                    'setting_group_id' => $setting_group[3]['id'],
                    'vari' => 'email_from',
                    'value' => '',
                    'type' => 'text',
                    'options' => '',
                    'info' => '应该和服务器地址对用类型'
                ],
                [
                    'title' => '发件账号密码',
                    'setting_group_id' => $setting_group[3]['id'],
                    'vari' => 'email_password',
                    'value' => '',
                    'type' => 'password',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => '发件人名称',
                    'setting_group_id' => $setting_group[3]['id'],
                    'vari' => 'email_fromname',
                    'value' => 'WooCMS',
                    'type' => 'text',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => 'accessKeyId',
                    'setting_group_id' => $setting_group[4]['id'],
                    'vari' => 'sms_keyid',
                    'value' => '',
                    'type' => 'text',
                    'options' => '',
                    'info' => '填写阿里大于短信接口accessKeyId'
                ],
                [
                    'title' => 'accessKeySecret',
                    'setting_group_id' => $setting_group[4]['id'],
                    'vari' => 'sms_keysecret',
                    'value' => '',
                    'type' => 'text',
                    'options' => '',
                    'info' => '填写阿里大于短信接口accessKeySecret'
                ],
                [
                    'title' => 'appid',
                    'setting_group_id' => $setting_group[5]['id'],
                    'vari' => 'yt_appid',
                    'value' => '',
                    'type' => 'text',
                    'options' => '',
                    'info' => 'http://open.youtu.qq.com/申请'
                ],
                [
                    'title' => 'secretId',
                    'setting_group_id' => $setting_group[5]['id'],
                    'vari' => 'yt_secretid',
                    'value' => '',
                    'type' => 'text',
                    'options' => '',
                    'info' => ''
                ],
                [
                    'title' => 'secretKey',
                    'setting_group_id' => $setting_group[5]['id'],
                    'vari' => 'yt_secretkey',
                    'value' => '',
                    'type' => 'text',
                    'options' => '',
                    'info' => ''
                ],
            ];
            
            $this->Setting->saveAll($data);
            $this->Setting->write_cache();
            echo  '成功：系统设置项安装成功<br>';
            
        } else {
            echo  '失败：系统设置项已安装<br>';
        }
        
        echo '-------------------------------------------------------------<br>';
        
        $absroot = (substr($this->request->root(true), -10) != '/index.php' ? $this->request->root(true) : substr($this->request->root(true), 0, -10)) . '/';         
        echo '<a href="' . $absroot . '" style="color:green;">访问前台</a>　　<a href="' . $absroot . 'run" style="color:green;">访问后台</a>　注：默认用户名和密码：admin<br>';        
        @touch(dirname(__FILE__) . DS . 'install.lock');
        exit();
    }      
}
