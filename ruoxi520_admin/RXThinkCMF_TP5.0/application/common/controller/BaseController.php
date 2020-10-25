<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 控制器基类
 * 
 * @author 牧羊人
 * @date 2018-12-08
 */
namespace app\common\controller;
use think\Controller;
use think\Config;
class BaseController extends Controller
{
    
    /**
     * 构造方法
     * 
     * @author 牧羊人
     * @date 2018-12-10
     */
    public function __construct()
    {
        parent::__construct();
        //TODO...
    }
    
    /**
     * 初始化操作
     * 
     * @author 牧羊人
     * @date 2018-12-08
     * (non-PHPdoc)
     * @see \think\Controller::_initialize()
     */
    public function _initialize() 
    {
        parent::_initialize();

        // 自定义常规变量
        define('REQUEST_METHOD',$_SERVER['REQUEST_METHOD']);
        define('IS_GET', REQUEST_METHOD =='GET' ? true : false);
        define('IS_POST', REQUEST_METHOD =='POST' ? true : false);
        define('IS_PUT', REQUEST_METHOD =='PUT' ? true : false);
        define('IS_DELETE', REQUEST_METHOD =='DELETE' ? true : false);
        
        // 设置基础参数
        $this->assign("siteName", Config::get('config.site_name'));
        $this->assign("nickName", Config::get('config.nick_name'));
        
        // 图片域名
        $this->assign('imgUrl',IMG_URL);
        
        // 系统应用参数
        define('APP', $this->request->controller());
        define('ACT', $this->request->action());
        $this->assign('module' , $this->request->module());
        $this->assign('app' , APP);
        $this->assign('act' , ACT);
        
        // 上传配置
        $uploadConfig = Config::get('config.upload');
        
        // 图片配置
        $this->assign('uploadImgExt', $uploadConfig['image_config']['upload_image_ext']);
        $this->assign('uploadImgSize', $uploadConfig['image_config']['upload_image_size']);
        $this->assign('uploadImgMax', $uploadConfig['image_config']['upload_image_max']);
        
        // 视频配置
        $this->assign('uploadVideoExt', $uploadConfig['video_config']['upload_video_ext']);
        $this->assign('uploadVideoSize', $uploadConfig['video_config']['upload_video_size']);
        $this->assign('uploadVideoMax', $uploadConfig['video_config']['upload_video_max']);
        
    }
    
}