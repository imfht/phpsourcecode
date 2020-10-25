<?php
namespace app\home\controller;
use think\Controller;
use think\View;

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
        session_start();
        $this->session_id = session_id(); // 当前的 session_id
        define('SESSION_ID',$this->session_id); //将当前的session_id保存为常量，供其它方法调用
        //MEDIA信息
        $media=$this->getMedia();
        $this->assign('media', $media);
    }
    protected function siteFetch($template = '', $vars = [], $replace = [], $get_site = []){
        $tpl_name=get_site('tpl_name').DS.$template;
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
}