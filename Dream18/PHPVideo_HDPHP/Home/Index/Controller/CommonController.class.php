<?php
/**
 * 前台公共基类
 * Class CommonController
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
abstract class CommonController extends Controller
{
    // 缓存目录
    protected $cacheDir;
    // 风格目录
    protected $VIEW_DIR;

    function __construct()
    {
        define('__THEME__', 'Theme/' . C('WEB_STYLE'));
        $this->VIEW_DIR = 'Theme/' . C('WEB_STYLE');
        parent::__construct();
    }

    /**
     * 404页面
     */
    protected function _404()
    {
        set_http_state(404);
        $this->display('Theme/system/404');
        exit;
    }

    /**
     * 验证网站关闭
     */
    protected function CheckWebClose()
    {
        if (!IS_ADMIN && !C("WEB_OPEN"))
        {
            $this->display('Theme/system/web_close');
            exit;
        }
    }
}