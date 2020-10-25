<?php

/**
 * 插件管理控制器
 * Class Addon
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class AddonsController extends AuthController
{
    private $db;

    /**
     * [__init 构造函数]
     * @return [type] [description]
     */
    public function __init()
    {
        parent::__init();
        //删除插件缓存
        S('hooks',null);
        $this->db = K('Addons');
    }

    /**
     * [index 插件列表]
     * @return [type] [description]
     */
    public function index()
    {
        $data = $this->db->getAddonList();
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * [disabled 禁用插件]
     * @return [type] [description]
     */
    public function disabled()
    {
        if ($this->db->disabledAddon())
        {
            $this->success('禁用成功，请刷新后台');
        }
        $this->error($this->db->error);
    }

    /**
     * [enabled 启用插件]
     * @return [type] [description]
     */
    public function enabled()
    {
        if ($this->db->enabledAddon()) {
            $this->success('启用成功，请刷新后台');
        } else {
            $this->error($this->db->error);
        }
    }

    /**
     * [add 创建插件]
     */
    public function add()
    {
        if (IS_POST)
        {
            $data = $_POST;
            $data['has_adminlist'] = isset($_POST['has_adminlist']) ? 1 : 0; //有后台
            $data['has_outurl'] = isset($_POST['has_outurl']) ? 1 : 0; //前台访问
            $data['config'] = isset($_POST['config']) ? 1 : 0; //有配置文件
            $data['viewTag'] = isset($_POST['viewTag']) ? 1 : 0; //有前台标签文件

            // 字段验证
            $this->db->validate = array(
                array('name', 'nonull', '插件标识不能为空！', 2, 3),
                array('name', 'regexp:/^[a-zA-Z]+$/i', '插件标识必须为英文字母', 2, 3),
                array('name', 'addonUniqueCheck', '该插件已经存在！', 2, 3),
                array('title', 'nonull', '插件名称不能为空！', 2, 3),
                array('version', 'nonull', '插件版本不能为空！', 2, 3),
                array('author', 'nonull', '插件作者不能为空！', 2, 3),
                array('description', 'nonull', '插件描述不能为空！', 2, 3),
            );

            // 验证插件数据合法性
            if (!$this->db->validate($data))
            {
                $this->error($this->db->error);
            }

            // 插件名首字母大小
            $data['name'] = ucfirst($data['name']);

            // 验证安装目录
            if (!is_writable(APP_ADDON_PATH))
            {
                $this->error(APP_ADDON_PATH . ' 不可写');
            }
            //-------------------插件目录----------------------
            $addonDir = APP_ADDON_PATH . $data['name'] . '/';
            if(!dir::create($addonDir))
            {
                $this->error('插件目录创建失败');
            }
            //-------------------配置文件----------------------
            if ($data['config'])
            {
                copy(MODULE_PATH . 'Data/Addon/configAddon.php', $addonDir . 'config.php');
            }
            //-------------------标签目录------------------
            if($data['viewTag'])
            {
                if(!dir::create($addonDir.'Tag'))
                {
                    $this->error('标签Tag目录创建失败');
                }
                $viewTagPhp=<<<tag
<?php
//标签类文件命名规范：Addon插件名Tag
class Addon{$data['name']}Tag
{
    //声明标签
    public \$tag = array(
        'addon_{$data['name']}_test' => array('block' => 1, 'level' => 4),
    );
     //示例标签
     //a) 标签命名规范：_addon_插件名_标签
     //b) 插件安装后才可以使用标签
     //c) 模板使用<addon_{$data['name']}_test> </addon_{$data['name']}_test>调用
    public function _addon_{$data['name']}_test(\$attr, \$content)
    {
        return '这是标签测试结果';
    }
}
tag;
                file_put_contents($addonDir.'Tag/Addon'.$data['name'].'Tag.class.php',$viewTagPhp);

            }
            //--------------------控制器目录----------------------
            if ($data['has_adminlist'] || $data['has_outurl'])
            {
                if(!Dir::create($addonDir . 'Controller'))
                {
                    $this->error('控制器目录创建失败');
                }
            }
            //--------------------后台控制器----------------------

            if ($data['has_adminlist'])
            {
                $controller = <<<str
<?php
/**
 * {$data['name']} 插件
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */

class AdminController extends AddonController {

    public function index() {
        \$this->display();
    }
}
str;
                file_put_contents($addonDir . 'Controller/AdminController.class.php', $controller);
            }
            //--------------------前台控制器----------------------
            if ($data['has_outurl'])
            {
                $controller = <<<str
<?php
/**
 * {$data['name']} 插件
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */

class IndexController extends AddonController {

    public function index() {
        \$this->display();
    }
}
str;
                file_put_contents($addonDir . 'Controller/IndexController.class.php', $controller);
            }
            //--------------------插件控制器----------------------
            $addonData = <<<str
<?php
/**
 * {$data['name']} 插件
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class {$data['name']}Addon extends Addon
{

    // 插件信息
    public \$info = array(
        'name' => '{$data['name']}',
        'title' => '{$data['title']}',
        'description' => '{$data['description']}',
        'status' => 1,
        'author' => '{$data['author']}',
        'version' => '{$data['version']}',
        'has_adminlist' => {$data['has_adminlist']},
    );

    // 安装
    public function install()
    {
        return true;
    }

    // 卸载
    public function uninstall()
    {
        return true;
    }
str;
            if (isset($data['hooks']))
            {
                foreach ($data['hooks'] as $hook)
                {
                    $addonData .= "
    //实现的{$hook}钩子方法
    public function {$hook}(\$param){
    }\n";
                }
            }
            $addonData .= '}';
            file_put_contents($addonDir . $data['name'] . 'Addon.class.php', $addonData);

            //创建View视图文件
            if ($data['has_adminlist'])
            {
                Dir::create($addonDir . 'View/Admin');
                copy(MODULE_PATH . 'Data/Addon/addonAdmin.php', $addonDir . 'View/Admin/index.php');
            }
            if ($data['has_outurl'])
            {
                Dir::create($addonDir . 'View/Index');
                copy(MODULE_PATH . 'Data/Addon/addonIndex.html', $addonDir . 'View/Index/index.html');
            }
            $this->success('安装成功，请刷新后台');
        }
        $this->assign('hooks', M('hooks')->all());
        $this->display();
    }







    /**
     * 配置管理图片上传
     */
    public function upload()
    {
        $upload = new Upload();
        $file = $upload->upload();
        if (empty($file))
        {
            $this->ajax('上传失败');
        }
        $data = $file[0];
        $data['uid'] = $_SESSION['user']['uid'];
        M('upload')->add($data);
        $this->ajax($data);
    }
    /**
     * [config 设置配置项]
     * @return [type] [description]
     */
    public function config()
    {
        if (IS_POST)
        {
            $id = Q('post.id', 0, 'intval');
            $data = M('addons')->find($id);
            if (empty($data)) {
                $this->error(':( 插件没安装');
            }
            $config = serialize(Q('post.config', '', ''));
            if (M('addons')->where("id=$id")->save(array('config' => $config))) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        } else {
            $id = Q('id', 0, 'intval');
            $data = M('addons')->find($id);
            if (empty($data)) {
                $this->error(':( 插件没安装');
            }
            $addonObj = $this->db->getAddonObj($data['name']); //获取插件对象
            $data['config'] = unserialize($data['config']);
            $configFile = require $addonObj->configFile; //文件配置
            if (!empty($configFile)) {
                foreach ($configFile as $key => $value) {
                    if (isset($data['config'][$key])) {
                        $configFile[$key]['value'] = $data['config'][$key];
                    }
                }
            }
            $this->assign('data', $data);
            $this->assign('configFile', $configFile);
            $this->display();
        }
    }

    /**
     * [package 打包插件]
     * @return [type] [description]
     */
    public function package()
    {
        $addon = Q('addon', '', '');
        if (!$addon || !is_dir(APP_ADDON_PATH . $addon))
        {
            $this->error('插件不存在');
        }
        $zip = new PclZip(APP_ADDON_PATH . $addon . '.zip');
        if ($zip->create(APP_ADDON_PATH . $addon))
        {
            $this->success('压缩成功，请到Addons目录查看');
        }
        $this->error('压缩失败');
    }

    /**
     * [install 安装插件]
     * @return [type] [description]
     */
    public function install()
    {
        if ($this->db->installAddon()) {
            $this->success('安装成功，请刷新后台');
        } else {
            $this->error($this->db->error);
        }
    }

    /**
     * [uninstall 卸载插件]
     * @return [type] [description]
     */
    public function uninstall()
    {
        if ($this->db->uninstallAddon()) {
            $this->success('卸载成功，请刷新后台');
        } else {
            $this->error($this->db->error);
        }
    }
    /**
     * [help 查看帮助]
     * @return [type] [description]
     */
    public function help(){
        C('TPL_FIX','.html');
        $addon = Q('addon');
        $this->display(APP_ADDON_PATH.$addon.'/help');
    }
}