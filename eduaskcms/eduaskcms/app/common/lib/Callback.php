<?php
namespace app\common\lib;

class Callback
{
    protected $ts; ##控制器对象
    protected $m;##当前模块
    protected $c;##当前控制名
    protected $a;##当前方法名
    protected $ca; ##控制器::方法名
    public $appBeforeAction = NULL;
    public $appAfterAction = NULL;

    public function __construct($controller)
    {
        $this->ts = $controller;
        $this->m = $this->ts->params['module'];
        $this->c = $this->ts->params['controller'];
        $this->a = $this->ts->params['action'];
        $this->ca = $this->c . '::' . $this->a;

        $appBeforeAction = 'appBefore' . \think\Loader::parseName($this->m, 1);
        $appAfterAction = 'appAfter' . \think\Loader::parseName($this->m, 1);
        if (is_callable([$this, $appBeforeAction])) $this->appBeforeAction = $appBeforeAction;
        if (is_callable([$this, $appAfterAction])) $this->appAfterAction = $appAfterAction;
    }
    ##每次访问URL方法之前（不管任何模块）都会执行
    ##在这里面可以写入自己的通用逻辑代码，而又不需要动核心App控制器代码
    ##这些方法里面的代码写法和控制器里面一样，只是用$this->ts 表示 控制器的$this
    public function appBeforeEach()
    {

    }

    ##每次访问URL方法后、渲染页面之前（不管任何模块，这个时候所有需要assign到页面的数据都已经准备好）都会执行
    public function appAfterEach()
    {

    }

    ##每次访问URL方法之前（仅限Home模块方法）都会执行
    public function appBeforeHome()
    {
        ##前台每个页面都需要家长的css 和js 
        $this->ts->assign->addJs([
            'jquery-1.11.1.min',
            '/files/layui-v2.2.6/layui.js',
            'common'
        ]);
        
        $this->ts->assign->addCss([
            '/files/layui-v2.2.6/css/layui.css',
            'global.css',
            'animate.css',
            '/files/awesome-4.7.0/css/font-awesome.min.css'
        ]);        
        
        ##前台栏目数据自动查询
        $queryList = db('QueryData')->where(['is_verify'=>1])->select();        
        foreach ($queryList as $query) {
            $is_query = false ;
            if ($query['query'] == 'index' && $this->ca == 'Index::index') {
                $is_query = true;
            }            
            if ($query['query'] == 'insider' && $this->ca != 'Index::index') {
                $is_query = true;
            }            
            if ($query['query'] == 'controller' && $this->c == trim($query['controller'])) {
                $is_query = true;
            }
            if ($query['query'] == 'all') {
                $is_query = true;
            }         
            if (!$is_query) {
                continue;
            }            
            foreach ($query as $field => &$fieldInfo) {
                if (!in_array($field, ['contain', 'where', 'field', 'order'])) {
                    continue ;
                }
                $fieldInfo = trim($fieldInfo);
                if ($fieldInfo) {
                    eval('$rslt = ' . trim($fieldInfo) . ';');
                    $fieldInfo = $rslt;
                }
            }            
            $this->ts->getMenuData($query['menu_id'], $query['list_count'], [
                'family' => !!$query['is_family'],##true 是否查询下级栏目；false 只查询当前栏目
                'type' => $query['type'],##select find
                'contain' => $query['contain'],##关联模型
                'where' => $query['where'],##条件
                'field' => $query['field'],##字段
                'order' => $query['order']##排序
            ]);
        }
        
        /*
         * ##手动查询栏目数据模板
        $this->ts->getMenuData(栏目ID(integer), 查询条数(integer), [
            'family' => true,##true 是否查询下级栏目；false 只查询当前栏目
            'type' => 'select',##select find
            'contain' => ['Menu'],##关联模型
            'where' => [],##条件
            'field' => [],##字段
            'order' => []##排序
        ]);
        ##查询以后，在这里可以使用 $this->ts->assign->query_data[栏目ID] 获取到数据
        ##查询以后，在试图中可以使用$query_data[栏目ID]获取到数据
        */
        
        ##可以类似于这样 单独指定不同控制器不同方法执行的代码
        switch ($this->ca) {
            case 'Article::show':
                ##文字列表页
                break;
            case 'Product::show':
                ##产品列表页
                break;
            case 'Product::view':
                ##产品详情页
                break;
        }
        
        if ($this->ca == 'Index::index') {
            ##首页页面
            $this->ts->assign->is_index = true;
            $this->ts->assign->addCss('index.css');
            
        } else {
            ##内页页面（非首页）
            $this->ts->assign->addCss(['insider.css', 'change.css']);
        }
    }

    ##每次访问URL方法后、渲染页面之前（仅限Home模块方法，这个时候所有需要assign到页面的数据都已经准备好）都会执行
    public function appAfterHome()
    {        
        if ($this->ca == 'Index::index') {
            ##首页广告位
            $this->ts->getAdData('index_banner', 0);
            $this->ts->assign->ad_var = 'index_banner';
            
        } else {
            ##内页广告位
            if (setting('is_menu_position')) {
                ##如果你希望每个栏目的广告都不一样，可以使用或参考下面代码（要求：不同栏目建一个广告位，广告为变量是：menu_栏目id）
                $path  = array_reverse((array)$this->ts->assign->path) ;
                $insiderAd = array();
                $this->ts->assign->ad_var = 'insider_banner';
                foreach ($path as $child_id) {
                    $this->ts->getAdData('menu_' . $child_id,0);
                    if ($this->ts->assign->ad['menu_' . $child_id]['Ad']) {
                        $this->ts->assign->ad_var = 'menu_' . $child_id;
                        break ;
                    }
                }           
                if (empty($this->ts->assign->ad[$c->assign->ad_var]['Ad'])) {
                    $this->ts->getAdData('insider_banner', 0);
                }
            } else {
                $this->ts->getAdData('insider_banner', 0);
                $this->ts->assign->ad_var = 'insider_banner';
            }
        }
    }

    ##每次访问URL方法之前（仅限Run模块方法）都会执行
    public function appBeforeRun()
    {
        $this->ts->assign->addJs([
            'jquery-3.2.1.min.js',
            '/files/layui-v2.2.6/layui.js',
            'admin/global.js'
        ]);
        

        $this->ts->assign->addCss([
            '/files/layui-v2.2.6/css/layui.css',
            'admin/global.css',
            'admin/animate.css',
            '/files/awesome-4.7.0/css/font-awesome.min.css'
        ]);
    }

    ##每次访问URL方法后、渲染页面之前（仅限Run模块方法，这个时候所有需要assign到页面的数据都已经准备好）都会执行
    public function appAfterRun()
    {

    }
    
    ##每次访问URL方法之前（仅限Run模块方法）都会执行
    public function appBeforeManage()
    {
        $this->ts->assign->addJs([
            'jquery-1.11.1.min',
            '/files/layui-v2.2.6/layui.js',
            'common'
        ]);
        
        
        $this->ts->assign->addCss([
            '/files/layui-v2.2.6/css/layui.css',
            'global.css',
            'manage/manage.css',
            'insider.css',
            'change.css',
            'animate.css',
            '/files/awesome-4.7.0/css/font-awesome.min.css'
        ]); 
    }

    ##每次访问URL方法后、渲染页面之前（仅限Run模块方法，这个时候所有需要assign到页面的数据都已经准备好）都会执行
    public function appAfterManage()
    {

    }
    ##以后扩展模块，每个模块都支持添加这样的2个方法用来表示 执行URL指定方法前和后的 动作
}
