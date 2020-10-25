<?php

/*
 *  @author myf
 *  @date 2014-11-13
 *  @Description 全局Controller基础类库
 *  @web http://www.minyifei.cn
 */

class Controller {

    //smarty模板变量
    private $smt;

    /**
     * 初始化方法
     * @param String $c Controller名称
     */
    public function _sys_init_action($c) {
        $this->smt = new Smarty();
        $this->smt->left_delimiter = "{{ ";
        $this->smt->right_delimiter = " }}";
        //设置模板目录
        $this->smt->template_dir = APP_PATH . "/tpl/" . strtolower($c);
        //设置编译目录
        $this->smt->compile_dir = TMP_PATH . "/tpl_c";
        //定义程序相对路径
        $this->smt->assign("myf_path", getBasePath());
        //定义程序绝对URL路径
        $this->smt->assign("myf_full_url", getFullURL());
        //定义程序根目录绝对URL路径
        $this->smt->assign("myf_project_url", getProjectURL());
    }
    
    /**
     * 添加插件目录
     * @param type $dir
     */
    public function addPluginsDir($dir){
        $this->smt->addPluginsDir($dir);
    }
    
    /**
     * 重置smartyDelimiter
     * @param type $left
     * @param type $right
     */
    public function setDelimiter($left,$right){
         $this->smt->left_delimiter = $left;
        $this->smt->right_delimiter = $right;
    }
    
    /**
     * 设置smarty模板位置
     * @param type $dir
     */
    public function setTemplateDir($dir){
        $this->smt->template_dir = $dir;
    }

    /**
     * action前执行的全局方法，可继承并重构
     */
    public function _before_action() {
        
    }

    /**
     * action后执行的全局方法,可继承并重构
     */
    public function _after_action() {
        
    }

    /**
     * smarty 设置模板变量
     * @param String $name
     * @param Object $value
     */
    public function assign($name, $value) {
        $this->smt->assign($name, $value);
    }

    /**
     * 获取模板解析后的内容
     * @param String $tplName 模板名称
     * @return String 模板解析后的内容
     */
    public function fetch($tplName) {
        return $this->smt->fetch($tplName);
    }

    /**
     * 显示模板解析后的内容
     * @param String $tplName 模板名称
     */
    public function display($tplName) {
        $this->smt->display($tplName);
    }

    /**
     * 魔术方法
     * @param type $name
     * @param type $arguments
     */
    public function __call($name, $arguments) {
        echo "error url 404";
    }

    /**
     * 错误跳转
     */
    public function error($msg, $url = 'javascript:history.back(-1);') {
        $this->smt->template_dir = APP_SYS_PATH . "/";
        $this->smt->assign("state", "error");
        $this->smt->assign("msg", $msg);
        $this->smt->assign("time", 3);
        $this->smt->assign("url", $url);
        $this->smt->display("msg.html");
    }

    /**
     * 成功跳转
     */
    public function success($msg, $url) {
        $this->smt->template_dir = APP_SYS_PATH . "/";
        $this->smt->assign("state", "success");
        $this->smt->assign("msg", $msg);
        $this->smt->assign("url", $url);
        $this->smt->assign("time", 1);
        $this->smt->display("msg.html");
    }

}
