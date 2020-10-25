<?php
namespace app\common\controller;

use think\Controller;
use think\Config;
use think\View;
use think\Db;
/**
 * 插件类
 */
abstract class Addons extends Controller{

    //视图实例对象
    protected $view             = null;
    // 当前错误信息
    protected $error;

    public $info                =   [];
    public $addons_path         =   '';
    public $config_file         =   '';
    public $custom_config       =   '';
    public $admin               =   '';
    public $access_url          =   [];


    /**
     * 架构函数
     * @access public
     */
    public function __construct(){

        // 获取当前插件目录
        $this->addons_path = ADDONS_PATH . $this->getName(). DS;

        // 读取当前插件配置信息
        if (is_file($this->addons_path . 'Config.php')) {
            $this->config_file = $this->addons_path . 'Config.php';
        }
        
        $view_replace_str = Config::get('view_replace_str');
        $view_replace_str['__ADDONROOT__'] = $this->addons_path;
        Config::set('view_replace_str', $view_replace_str);

        // 初始化视图模型
        $config = ['view_path' => $this->addons_path . 'view/'];
        $config = array_merge(Config::get('template'), $config);
        $this->view = new View($config, Config::get('view_replace_str'));

        // 控制器初始化
        if (method_exists($this, '_initialize')) {
            $this->_initialize();
        }
    }
    
    /**
     * 模板变量赋值
     * @access protected
     * @param mixed $name 要显示的模板变量
     * @param mixed $value 变量的值
     * @return Action
     */
    final protected function assign($name,$value='') {
        $this->view->assign($name,$value);
        return $this;
    }

    /**
     * 加载模板和页面输出 可以返回输出内容
     * @access public
     * @param string $template 模板文件名或者内容
     * @param array $vars 模板输出变量
     * @param array $replace 替换内容
     * @param array $config 模板参数
     * @return mixed
     * @throws \Exception
     */
    final public function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        if (!is_file($template)) {
            $template = '/' . $template;
        }
        // 关闭模板布局
        $this->view->engine->layout(false);

        echo $this->view->fetch($template, $vars, $replace, $config);
    }

    /**
     * 获取当前模块名
     * @return string
     */
    final public function getName()
    {
        $data = explode('\\', get_class($this));
        return strtolower(array_pop($data));
    }

    /**
     * 检查配置信息是否完整
     * @return bool
     */
    final public function checkInfo()
    {
        $info_check_keys = ['name', 'title', 'description', 'author', 'version'];
        foreach ($info_check_keys as $value) {
            if (!array_key_exists($value, $this->info)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 获取插件的配置数组
     */
    final public function getConfig($name=''){
        static $_config = [];
        if(empty($name)){
            $name = $this->getName();
        }
        if(isset($_config[$name])){
            return $_config[$name];
        }
        $config = [];
        $map['name']    =   $name;
        $map['status']  =   1;
        $config  =   Db::name('Addons')->where($map)->value('config');
        if($config){
            $config   =   json_decode($config, true);
        }else{
            if (is_file($this->config_file)) {
                $temp_arr = include $this->config_file;
                foreach ($temp_arr as $key => $value) {
                    if($value['type'] == 'group'){
                        foreach ($value['options'] as $gkey => $gvalue) {
                            foreach ($gvalue['options'] as $ikey => $ivalue) {
                                $config[$ikey] = $ivalue['value'];
                            }
                        }
                    }else{
                        $config[$key] = $temp_arr[$key]['value'];
                    }
                }
            }
        }
        $_config[$name]     =   $config;
        return $config;
    }

    /**初始化钩子的方法，防止钩子不存在的情况发生
     * @param $name
     * @param $description
     * @param int $type
     * @return bool
     */
    public function initHook($name,$description,$type=1){
        $hook=Db::name('hooks')->where(['name'=>$name])->find();
        if(!$hook){
            $hook['name']=$name;
            $hook['description']=$description;
            $hook['type']=$type;
            $hook['update_time']=time();
            $hook['addons']=$this->getName();
            $result=Db::name('hooks')->insert($hook);

            if($result===false){
                return false;
            }else{
                return true;
            }
        }
        return true;
    }

    /**
     * 解析数据库语句函数
     * @param string $sql  sql语句   带默认前缀的
     * @param string $tablepre  自己的前缀
     * @return multitype:string 返回最终需要的sql语句
     */
    public function sqlSplit($sql, $tablepre, $type='install') {

        $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);
        if($type == 'install'){
            //获取表前缀
            $r_tablepre = preg_replace("/[\s\S]*CREATE TABLE IF NOT EXISTS `([a-zA-Z]+_)[\s\S]*/", "\\1", $sql);
        }else{
            $r_tablepre = preg_replace("/[\s\S]*DROP TABLE IF EXISTS `([a-zA-Z]+_)[\s\S]*/", "\\1", $sql);
        }
        //替换表前缀
        $sql = str_replace($r_tablepre, $tablepre , $sql);
        $sql = str_replace("\r", "\n", $sql);
        $ret = [];
        $num = 0;
        $queriesarray = explode(";\n", trim($sql));
        unset($sql);

        foreach ($queriesarray as $query) {
            $ret[$num] = '';
            $queries = explode("\n", trim($query));
            $queries = array_filter($queries);
            foreach ($queries as $query) {
                $str1 = substr($query, 0, 1);
                if ($str1 != '#' && $str1 != '-')
                    $ret[$num] .= $query;
            }
            $num++;
        }
        return $ret;
    }

    /**
     * 获取当前错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    //必须实现安装
    abstract public function install();

    //必须卸载插件方法
    abstract public function uninstall();
}
