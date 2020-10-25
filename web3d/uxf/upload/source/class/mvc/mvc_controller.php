<?php

/**
 * 控制器类，负责用户请求的调度、数据的输入输出处理
 */
class Mvc_Controller extends Discuz_Base {
    
    /**
     *
     * @var string
     */
    protected $style = 'default';
    private $_helpers = array();
    protected $apptpl = 'view';
    
    /**
     * 模版后缀 
     * @var string
     */
    protected $tplsuffix = '.htm';
    
    /**
     * 布局文件名，不含后缀
     * @var string 
     */
    protected $layouts = '';
    
    /**
     * 控制器id名
     * @var string 
     */
    protected $_id;
    
    /**
     * 所属模块名
     * @var string 
     */
    protected $_moduleId;
    
    /**
     * 当前请求的动作名
     * @var string 
     */
    protected $_actId;

    /**
     * 视图对象
     * @var Mvc_View 
     */
    protected $view = null;

    /**
     *
     * @var string 动作的前缀，不能为空
     */
    protected $_actPrefix = 'act';

    public function __construct($module_id, $id, $act_id) {
        $this->_id = $id;
        $this->_moduleId = $module_id;
        $this->_actId = $act_id;
        $this->view = new Mvc_View();
    }
    
    /**
     * 供应用入口调度
     * @throws HttpException
     */
    public function run(){
        $action = $this->_actPrefix . ucfirst($this->_actId);
        if (is_callable(array($this, $action)) !== TRUE) {
            throw new HttpException('系统错误，无法找到您请求的页面！', 404);
        }
        $this->$action();
    }

    /**
     * 构造当前模块下指定模版文件的绝对路径
     * @param string $tpl_name
     * @return string
     */
    public function getTplFilePath($tpl_name) {
        return $this->getModulePath() . "{$this->apptpl}/{$this->_id}/{$tpl_name}{$this->tplsuffix}";
    }

    /**
     * 设置视图中要用的各种变量
     * @param string $k 变量名
     * @param string $v 变量值
     * @return void 
     */
    public function setVar($k, $v) {
        $this->view->setVar($k, $v);
    }

    /**
     * 
     * 此函数导入模块内部所需的类库，只引入，不实例化，利于IDE自动提示
     * 
     * /source/class 下的类全部由系统自动加载，其它目录下手动加载
     * 默认编程以模块为单位，模块之间不产生直接联系
     * 
     * @param string $class 类名，如Table_Travel、Service_Meijing、Lib_Charset
     * @return boolean
     */
    public function import($class, $ext='.php') {
        return C::import(strtolower($class), strtolower(UX_MODULES_DIR . "/{$this->_moduleId}"));
    }

    /**
     * 引入模版显示
     */
    public function display($tpl) {
        $tpldir = '/source/' .UX_MODULES_DIR . "/{$this->_moduleId}/{$this->apptpl}/{$this->_id}/";

        $this->view->display($tpl, $tpldir);
    }

    /**
     * 获取当前模块的绝对路径
     * @return string
     */
    public function getModulePath() {
        return DISCUZ_ROOT . '/source/' .UX_MODULES_DIR . "/{$this->_moduleId}/";
    }

    // 释放资源
    public function __destruct() {
    }
}
