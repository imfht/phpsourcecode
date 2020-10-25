<?php
namespace app\home\controller;
use think\Controller;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/21 0021
 * Time: 下午 4:37
 */
class Site extends Controller{
    public function __construct(\think\Request $request)
    {
        parent::__construct($request);
        /* 设置路由参数 */
    }
    //当任何函数加载时候  会调用此函数
    public function _initialize(){//默认的方法  会自动执行 特征有点像构造方法
        if (empty(get_lang_id())){
            cookie('think_var', 'zh-cn');
        }
        session_start();
        $this->session_id = session_id(); // 当前的 session_id
        define('SESSION_ID',$this->session_id); //将当前的session_id保存为常量，供其它方法调用
        $this->setCont();//设置站点基本信息
        //设置手机版参数
        if(isset($_GET['mobile']) || MOBILE){
            $tpl_name=get_site('mobile_tpl');
        }else{
            $tpl_name=get_site('tpl_name');
        }
        //设置常量
        define('TPL_NAME', $tpl_name);
        //MEDIA信息
        $media=$this->getMedia();
        $this->assign('media', $media);
    }
    protected function siteFetch($template = '', $vars = [], $replace = [], $get_site = []){
        $tpl_name=TPL_NAME.DS.$template;
        return $this->fetch($tpl_name, $vars, $replace, $get_site);
    }
    /**
     * 页面Meda信息组合
     * @return array 页面信息
     */
    protected function getMedia($title='',$keywords='',$description='')
    {
        if(empty($title)){
            $title=get_site('site_title').' - '.get_site('site_subtitle');
        }else{
            $title=$title.' - '.get_site('site_title').' - '.get_site('site_subtitle');
        }
        if(empty($keywords)){
            $keywords=get_site('site_keywords');
        }
        if(empty($description)){
            $description=get_site('site_description');
        }
        return array(
            'title'=>$title,
            'keywords'=>$keywords,
            'description'=>$description,
        );
    }
    /**
     * 页面不存在
     * @return array 页面信息
     */
    protected function error404(){
        throw new \Exception("404页面不存在！", 404);
    }
    /**
     * 留言错误
     */
    protected function errorBlock(){
        return "<script>alert('通讯错误!请检查表单名称!')</script>";
    }
    /**
     * 设置站点基本信息
     */
    protected function setCont(){

        //设置站点
        $url = $_SERVER['HTTP_HOST'];
        $detect = new \org\Mobile_Detect();
        if (get_site('mobile_status')==1) {
            //网站跳转
            if (!$detect->isMobile() && !$detect->isTablet()) {
                if (get_site('site_url') && $url <> get_site('site_url')) {
                    $this->redirect('http://' . get_site('site_url') . $_SERVER["REQUEST_URI"]);
                }
                define('MOBILE', false);
            } else {
                if (get_site('mobile_domain') && $url <> get_site('mobile_domain')) {
                    $this->redirect('http://' . get_site('mobile_domain') . $_SERVER["REQUEST_URI"]);
                }
                define('MOBILE', true);
            }
        } else {
            //禁用手机版本
            define('MOBILE', false);
        }
    }
}