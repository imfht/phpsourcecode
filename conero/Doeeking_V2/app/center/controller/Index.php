<?php
/*  2016年12月11日 星期日
 *  个人中心首页
*/
namespace app\center\controller;
use think\Loader;
use think\Controller;
class Index extends Controller
{
    // 初始化
    private $CenterLogicMain;// 个人中心业务逻辑首页
    public function _initialize(){
        if($this->_initTplCheck(['ajax','save','internet'])) return;        
        $this->CenterLogicMain = $this->getServerName();
        $opts = [
            'auth'=>'','title'=>uInfo('nick').'-Conero-账号管理','js'=>[],'css'=>['center'],'bootstrap'=>true
        ];
        $action = request()->action();
        if($action == 'index') $opts['js'] = ['center'];
        if(is_object($this->CenterLogicMain)){            
            $this->CenterLogicMain->init($opts,$action);
            // println($opts,$this->CenterLogicMain);
        }
        $this->loadScript($opts);
    }
    // 业务逻辑名称处理
    private function getServerName()
    {
        $data = $_GET;    
        $action = request()->action();    
        $name = 'user';
        $isEdit = getUrlBind('edit');
        if(count($data) > 0 && empty($isEdit)){       // main 页面
            $i = 0; 
            foreach($data as $k=>$v){
                $i ++;
                if(empty($v)){
                    $name = str_replace('_html','',$k);
                    break;
                }
            }
            // println($name,$i,$data);            
        }
        elseif($action == 'edit') $name = $isEdit;
        $name = ucfirst($name);
        $logic = '\app\center\Logic\\'.$name;
        if(!class_exists($logic)) return strtolower($name);
        return new $logic($this);        
    }
    public function index()
    {
        //echo $this->CenterLogicMain->main();die;
        // 中心菜单生成器
        $menu = '';
        $data = model('Menu')->getMenuList('center');
        $home = '';
        foreach($data as $v){            
            if(strtolower($v['code_mk']) == 'home' && empty($home)) $home = '<li class="active"><a href="'.$v['url'].'">'.$v['descrip'].'<span class="sr-only">(current)</span></a></li> ';
            else $menu .= '<li><a href="'.$v['url'].'">'.$v['descrip'].'</a></li>';
        }
        if($menu) $menu = $home.$menu;

        $this->assign([
            'page'       => uInfo(),
            'idx_center_menu'=> $menu,
            'logic_main' => (is_object($this->CenterLogicMain))? $this->CenterLogicMain->main() : $this->logicUnFind()
        ]);
        return $this->fetch(APP_PATH.'center/view/center.html');
    }
    // 页面不存在
    private function logicUnFind()
    {
        $html = '<div class="alert alert-warning" role="alert">'
                .'<strong>错误!</strong> 页面 '.$this->CenterLogicMain.'不存在.'                
                .'</div>'
                .'<div calss="well">CENORO @...</div>'   
                .'<img src="/conero/public/img/loading-football.jpg" alt="页面不存在" class="img-circle">'
                .'<img src="/conero/public/img/loading-football.jpg" alt="页面不存在" class="img-circle">'
                .'<img src="/conero/public/img/loading-football.jpg" alt="页面不存在" class="img-circle">'             
                .'<img src="/conero/public/img/Elle-Fanning-1.jpg" alt="页面不存在" class="img-thumbnail">'                
                .'<img src="/conero/public/img/saoirse_ronan_flaunt_magazine_2016_05.jpg" alt="页面不存在" class="img-thumbnail">'                
        ;
        return $html;
    }
    // ajax 支持
    public function ajax()
    {
        $ajax = getUrlBind('ajax');
        if($ajax){
            $name = ucfirst($ajax);
            $logic = '\app\center\Logic\\'.$name;
            if(!class_exists($logic)) return strtolower($ajax).'请求页面无效';
            (new $logic($this))->ajax();die;
        }
        utf8();
        echo '欢迎访问-conero网站';
    }
    // php 动态保存
    public function save()
    {
        $app = getUrlBind('save');
        if($app){
            $name = ucfirst($app);
            $logic = '\app\center\Logic\\'.$name;
            if(!class_exists($logic)) return strtolower($app).'请求页面无效';
            (new $logic($this))->save();return;
        }
        utf8();echo '欢迎访问-conero网站';
    }
     // php 动态保存
    public function edit()
    {
        $app = getUrlBind('edit');
        if($app){
            $name = ucfirst($app);
            $logic = '\app\center\Logic\\'.$name;
            if(!class_exists($logic)) return strtolower($app).'请求页面无效';
            $this->assign('user',uInfo('nick'));
            (new $logic($this))->edit($this->view);
            /*
            $logic = (new $logic($this));
            $action = request()->action();
            $opts = [
                'auth'=>'','title'=>uInfo('nick').'-Conero-账号管理','js'=>['center'],'css'=>['center'],'bootstrap'=>true
            ];
            $logic->init($opts,'edit');
            $logic->edit($this->view);                        
            $this->loadScript($opts);
            */
            return $this->fetch(APP_PATH.'center/view/edit.html');
        }
        utf8();echo '欢迎访问-conero网站';
    }
    // 页面跳转记录保存
    public function internet()
    {
        if(isset($_GET['url'])){
            list($data,$mode,$map) = $this->_getSaveData('sys_no');
            $url = base64_decode($_GET['url']);
            $type = isset($data['type'])? $data['type']:'';
            switch($type){
                case 'updateCtt':       // 地址跳转统计
                    // println($data,$mode,$map,$url);
                    $tb = 'sys_organs';
                    $vstCtt = $this->croDb($tb)->where($map)->value('visit_count');
                    $saveData = [
                        'visit_count' => $vstCtt + 1,
                        'last_vtime'  => sysdate()
                    ];
                    $this->croDb($tb)->where($map)->update($saveData);
                    go($url);
                    break;
            }
        }
        // exit;
    }
}