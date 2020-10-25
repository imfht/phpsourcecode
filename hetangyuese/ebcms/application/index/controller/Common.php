<?php
namespace app\index\controller;
use think\Controller;
class Common extends Controller
{

    protected $seo = [];
    public function _initialize()
    {

        defined('IS_INDEX') or define('IS_INDEX', true);

        // 判断网站是否关闭
        if (false !== \ebcms\Config::get('home.site.closed')) {
            header("Content-type: text/html; charset=utf-8");
            echo \ebcms\Config::get('home.site.closed_reason');
            die();
        }

        // 禁止错误输出
        if (!\think\Config::get('app_debug')) {
            error_reporting(0);
        }

        // 判断app是否可用
        if (!check_app(request() -> module())) {
            $this -> error('尚未开通！');
        }

        // 设置模板路径
        $theme = \ebcms\Config::get('home.site.theme')?:'default';
        if (true === \ebcms\Config::get('home.site.mobile') && request()->isMobile()) {
            if (is_dir(ROOT_PATH . 'templates' . DS . $theme . '_mobile')) {
                $theme = $theme . '_mobile';
            }else{
                header("Content-type: text/html; charset=utf-8");
                echo '移动端模板目录：/templates/'.$theme . '_mobile 不存在！';
                die();
            }
        }
        $this -> view -> config('view_base',ROOT_PATH .'templates' . DS . $theme . DS);

        // 设置行为监听
        \think\Hook::listen('index_init');

        // 设置seo
        if (request()->isGet()) {
            $config = \ebcms\Config::get('home.seo');
            $this->seo = [
                'sitename' => $config['sitename'],
                'title' => $config['title'],
                'keywords' => $config['keywords'],
                'description' => $config['description'],
            ];
        }
    }
}