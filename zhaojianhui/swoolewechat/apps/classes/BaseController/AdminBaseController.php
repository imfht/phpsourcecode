<?php
namespace App\BaseController;

/**
 * 管理后台基类控制器
 * @package App\Controller\Api
 */
class AdminBaseController extends BaseController
{
    /**
     * seo信息.
     *
     * @var
     */
    public $seoData;
    /**
     * 面包屑数据
     * title.
     *
     * @var
     */
    public $breadcrumbData;
    /**
     * 消息数据
     * @var
     */
    private $msgData;
    /**
     * 当前路径，关系到权限
     * @var string
     */
    public $currentUrl = '';
    /**
     * 菜单html
     * @var
     */
    public $menuHtml;

    public function __construct($swoole)
    {
        parent::__construct($swoole);
        $this->template_dir .= 'Admin/';
        //开启session
        $this->session->start();
        //组合当前菜单值
        if (isset($this->swoole->env['mvc']['directory']) && $this->swoole->env['mvc']['directory']){
            $this->currentUrl .= '/'.$this->swoole->env['mvc']['directory'];
        }
        if (isset($this->swoole->env['mvc']['controller']) && $this->swoole->env['mvc']['controller']){
            $this->currentUrl .= '/'.$this->swoole->env['mvc']['controller'];
        }
        if (isset($this->swoole->env['mvc']['view']) && $this->swoole->env['mvc']['view']){
            $this->currentUrl .= '/'.$this->swoole->env['mvc']['view'];
        }
        //非登录界面都要验证是否登录
        if (strpos($this->currentUrl, '/Admin/Login') === false){
            \Swoole\Auth::loginRequire();
        }
        //初始化菜单html
        $this->menuHtml = (new \App\Service\SysMenu())->buildAdminTreeMenu(strtolower($this->currentUrl));
        //添加面包屑
        $this->addBreadcrumb('首页','/Admin/Index/index');

        //添加系统配置
        $siteconf = \Swoole::$php->config['site'];
        $this->assign('siteconf', $siteconf);
        \Swoole::$php->config['qiniu'];
    }
    /**
     * 设置网页seo标题.
     *
     * @param $title
     */
    public function setSeoTitle($title)
    {
        $this->seoData['title'] = $title;
    }

    /**
     * 添加面包屑导航.
     *
     * @param array $navData
     */
    public function addBreadcrumb($title, $url)
    {
        $this->breadcrumbData[] = ['title'=>$title, 'url'=>$url];
    }

    /**
     * 清空面包屑导航数据.
     */
    public function unsetBreadcrumb()
    {
        $this->breadcrumbData = [];
    }

    /**
     * 信息提示页.
     *
     * @param string $type
     * @param string $msg
     * @param string $redirectUrl
     * @param array  $otherData
     */
    public function showMsg($type = 'success', $msg = '', $redirectUrl = '', $otherData = [])
    {
        if ($redirectUrl){
            $this->msgData['redirectUrl'] = $redirectUrl;
        }else{
            $this->msgData['redirectUrl'] = $this->request->server['HTTP_REFERER'] ?? '/admin/index/index';
        }
        $this->msgData['message']     = $msg;
        $templateFile                 = 'common/showmsg.php';
        switch ($type) {
            case 'success':
                $this->msgData['title'] = '操作成功';
                break;
            case 'error':
                $this->msgData['title'] = '操作失败';
                break;
            case 'info':
                $this->msgData['title'] = '消息提示';
                break;
            case 'warning':
                $this->msgData['title'] = '错误警告';
                break;
            case '404':
                $this->msgData['title'] = '页面没找到';
                $templateFile           = 'common/show404.php';
                $this->http->status(404);
                break;
            case '500':
                $this->msgData['title'] = '服务器发生错误';
                $templateFile           = 'common/show500.php';
                $this->http->status(500);
                break;
            default:
                $this->msgData['title']  = '未知信息';
        }
        $this->seoData['title'] = $this->msgData['title'];
        $this->msgData['status'] = $type;
        $this->msgData['data']  = $otherData;
        if ($this->is_ajax) {
            $otherData['redirectUrl'] = $this->msgData['redirectUrl'];
            return $this->msgData;
            //$this->http->finish($jsonStr);
        } else {
            $this->assignData = $this->msgData;
            $content          = $this->fetch($templateFile);
            $this->http->finish($content);
        }
        return true;
    }
}