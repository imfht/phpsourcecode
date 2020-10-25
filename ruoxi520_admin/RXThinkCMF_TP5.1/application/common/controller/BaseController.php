<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace app\common\controller;

use think\Controller;
use think\Facade\Config;

/**
 * 基类控制器
 * @author 牧羊人
 * @date 2019/3/20
 */
class BaseController extends Controller
{
    /**
     * 构造方法
     * @author 牧羊人
     * @date 2019/3/20
     */
    public function __construct()
    {
        parent::__construct();
        //TODO...
    }

    /**
     * 初始化操作
     * @author 牧羊人
     * @date 2019/3/20
     */
    protected function initialize()
    {
        parent::initialize();

        // 初始化请求配置
        $this->initRequestConfig();

        // 初始化全局地址
        $this->initRequestUrl();

        // 初始化上传配置
        $this->initUploadConfig();
    }

    /**
     * 初始化请求配置
     * @author 牧羊人
     * @date 2019/3/20
     */
    private function initRequestConfig()
    {
        // 定义是否GET请求
        defined('IS_GET') or define('IS_GET', $this->request->isGet());

        // 定义是否POST请求
        defined('IS_POST') or define('IS_POST', $this->request->isPost());

        // 定义是否AJAX请求
        defined('IS_AJAX') or define('IS_AJAX', $this->request->isAjax());

        // 定义是否PAJAX请求
        defined('IS_PJAX') or define('IS_PJAX', $this->request->isPjax());

        // 定义是否PUT请求
        defined('IS_PUT') or define('IS_PUT', $this->request->isPut());

        // 定义是否DELETE请求
        defined('IS_DELETE') or define('IS_DELETE', $this->request->isDelete());

        // 定义是否HEAD请求
        defined('IS_HEAD') or define('IS_HEAD', $this->request->isHead());

        // 定义是否PATCH请求
        defined('IS_PATCH') or define('IS_PATCH', $this->request->isPatch());

        // 定义是否为手机访问
        defined('IS_MOBILE') or define('IS_MOBILE', $this->request->isMobile());

        // 定义是否为cli
        defined('IS_CLI') or define('IS_CLI', $this->request->isCli());

        // 定义是否为cgi
        defined('IS_CGI') or define('IS_CGI', $this->request->isCgi());

        // 模块名称
        $module_name = $this->request->module();
        // 控制器名称
        $controller_name = $this->request->controller();
        // 操作方法名称
        $action_name = $this->request->action();

        // 定义模块名
        defined('MODULE_NAME') or define('MODULE_NAME', $module_name);
        // 定义控制器名
        defined('CONTROLLER_NAME') or define('CONTROLLER_NAME', $controller_name);
        // 定义操作方法名
        defined('ACTION_NAME') or define('ACTION_NAME', $action_name);

        // 自定义渲染
        $this->assign('module', strtolower($module_name));
        $this->assign('app', strtolower($controller_name));
        $this->assign('act', strtolower($action_name));
    }

    /**
     * 初始化全局地址
     * @author 牧羊人
     * @date 2019/3/20
     */
    private function initRequestUrl()
    {
        // 系统前台域名
        $this->assign('siteUrl', SITE_URL);

        // 系统后台域名
        $this->assign('adminUrl', MAIN_URL);

        // 系统WAP域名
        $this->assign('wapUrl', WAP_URL);

        // 系统API域名
        $this->assign('apiUrl', API_URL);

        // 系统脚本域名
        $this->assign('scriptUrl', SCRIPT_URL);

        // 图片域名
        $this->assign('imgUrl', IMG_URL);
    }

    /**
     * 初始化上传配置
     * @author 牧羊人
     * @date 2020/1/2
     */
    private function initUploadConfig()
    {
        // 图片上传配置
        $this->assign('uploadImgExt', config("upload.image_config.upload_image_ext"));
        $this->assign('uploadImgSize', config("upload.image_config.upload_image_size"));
        $this->assign('uploadImgMax', config("upload.image_config.upload_image_max"));

        // 视频上传配置
        $this->assign('uploadVideoExt', config("upload.video_config.upload_video_ext"));
        $this->assign('uploadVideoSize', config("upload.video_config.upload_video_size"));
        $this->assign('uploadVideoMax', config("upload.video_config.upload_video_max"));

        // 文件上传配置
        $this->assign('uploadVideoExt', config("upload.file_config.upload_file_ext"));
        $this->assign('uploadVideoSize', config("upload.file_config.upload_file_size"));
        $this->assign('uploadVideoMax', config("upload.file_config.upload_file_max"));
    }
}
