<?php
/** .-------------------------------------------------------------------
 * | Author: OkCoder <1046512080@qq.com>
 * | Git: https://www.gitee.com/okcoder
 * | Copyright (c) 2012-2019, www.i5920.com. All Rights Reserved.
 * '-------------------------------------------------------------------*/

namespace OkCoder\ApiDoc;

use think\Request;
use think\View;


class Controller
{

    protected $assets_path = "";
    protected $view_path = '';
    protected $root = '';

    protected $request; # Request 实例
    protected $view;    # 视图类实例

    # 资源类型
    protected $mimeType = [
        'xml'  => 'application/xml,text/xml,application/x-xml',
        'json' => 'application/json,text/x-json,application/jsonrequest,text/json',
        'js'   => 'text/javascript,application/javascript,application/x-javascript',
        'css'  => 'text/css',
        'rss'  => 'application/rss+xml',
        'yaml' => 'application/x-yaml,text/yaml',
        'atom' => 'application/atom+xml',
        'pdf'  => 'application/pdf',
        'text' => 'text/plain',
        'png'  => 'image/png',
        'jpg'  => 'image/jpg,image/jpeg,image/pjpeg',
        'gif'  => 'image/gif',
        'csv'  => 'text/csv',
        'html' => 'text/html,application/xhtml+xml,*/*',
    ];

    public function __construct(Request $request = null)
    {
        //5.1 去除常量调整导致的问题
        if (!defined('THINK_VERSION')) {
            if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
        }
        //有些程序配置了默认json问题
        config('default_return_type', 'html');

        if (is_null($request)) {
            $request = Request::instance();
        }
        $this->request = $request;

        $this->assets_path = __DIR__ . DS . 'assets' . DS;
        $this->view_path   = __DIR__ . DS . 'view' . DS;
        if (!defined('THINK_VERSION')) {
            $this->doc = new Doc((array)\think\facade\Config::pull('okcoder_doc'));
        } else {
            $this->doc = new Doc((array)\think\Config::get('okcoder_doc'));
        }

        $config     = [
            'view_path'      => $this->view_path,
            'default_filter' => ''
        ];
        $this->view = new View($config);
        if (!$this->view->engine) {
            $this->view->init($config);
        }
        $this->view->assign('web', $this->doc->__get());

        $this->assets_path = $this->doc->__get("static_path") ?: '/doc/assets';
        $this->view->assign('assets', $this->assets_path);
        $this->root = $this->request->root() ?: $this->request->domain();

        if ($this->request->session('doc.is_login') !== $this->doc->__get('password')
            && $this->doc->__get('password')
            && $this->request->url() !== '/doc/login'
            && stristr($this->request->url(), '/assets') == false
        ) {
            session('doc.request_url', get_url());
            header('location:/doc/login');
            exit();
        }

        // 序言文档
        $this->view->assign('document', $this->doc->__get('document'));

        // 版本号
        $this->view->assign('versions', $this->doc->__get('controller'));


        // 左侧菜单
        $this->view->assign('menu', $this->doc->get_api_list(input('version', 0, 'intval')));
    }

    # 解析资源
    public function assets()
    {
        $assets_path = __DIR__ . DS . 'assets' . DS;
        $path        = str_replace("doc/assets", "", $this->request->pathinfo());
        $ext         = $this->request->ext();
        if ($ext) {
            $type    = "text/html";
            $content = file_get_contents($assets_path . $path);
            if (array_key_exists($ext, $this->mimeType)) {
                $type = $this->mimeType[$ext];
            }
            return response($content, 200, ['Content-Length' => strlen($content)])->contentType($type);
        }
    }


    /** 显示模板
     * @param $name
     * @param array $vars
     * @param array $config
     * ----------------------------------------------------
     * @author Victor
     * @QQ 1046512080
     * @url http://www.sucailong.com / http://www.i5920.com
     */
    protected function template($name, $vars = [], $config = [])
    {
        $vars = array_merge(['root' => $this->root], $vars);
        return $this->view->fetch($name, $vars, $config);
    }


    public function index()
    {
        return $this->template('index');
    }

    public function module($name = '')
    {
        if (class_exists($name)) {
            $reflection = new \ReflectionClass($name);
            $doc_str    = $reflection->getDocComment();
            $doc        = new Parser();
            # 解析类
            $class_doc = $doc->parse_class($doc_str);
            $this->view->assign('data', $class_doc);
        }
        return $this->template('module');
    }

    public function action($name = '')
    {
        if ($this->request->isAjax()) {
            list($class, $action) = explode("::", $name);
            $data = $this->doc->get_api_detail($class, $action);
            # 全局header
            $data['_header'] = $this->doc->__get('header');
            # 全局参数
            $data['_params'] = $this->doc->__get('params');
            $this->result($data, 0, 'SUCCESS');
        } else {
            return $this->template('action');
        }
    }

    public function document($name = 'explain')
    {
        $this->view->assign('data', $this->doc->__get('document')[$name]);
        return $this->template('doc_' . $name);
    }

    // debug 格式化参数
    public function format_params()
    {
        $header           = $this->format($this->request->param('header'));
        $header["Cookie"] = $this->request->param('cookie');
        $params           = $this->format($this->request->param('params'));
        return ['params' => $params, 'header' => $header];
    }

    private function format($data = [])
    {
        if (!$data || count($data) < 1) {
            return [];
        }
        $result = [];
        foreach ($data['name'] as $k => $v) {
            $result[$v] = $data['value'][$k];
        }
        return $result;
    }


    use \traits\controller\Jump;

    public function login()
    {
        if ($this->request->isPost()) {
            if (input('post.password') != $this->doc->__get('password')) {
                $this->error('您输入的密码不正确');
                exit();
            } else {
                session('doc.is_login', input('post.password'));
                $this->success('登录成功', session('doc.request_url') ?: '/doc');
            }
        } else {
            if (session('doc.is_login') == $this->doc->__get('password')) {
                header('location:/doc');
            } else {
                return $this->template('login');
            }
        }

    }


}