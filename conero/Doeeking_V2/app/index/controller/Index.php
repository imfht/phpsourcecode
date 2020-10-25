<?php
namespace app\index\controller;
use think\Loader;
use think\Controller;
class Index extends Controller
{
    use IndexTrait; // 风格化控制
    public function index()
    {
        $theme = null;                
        // URL 加载
        if(empty($theme)){
            $theme = isset($_GET['theme'])? $_GET['theme']:null;   
            $urlJson = bsjson($theme);
            //println($urlJson);die;
            // 格式类型 - url?theme={theme:"主体名词","day":"today"}
            if(isset($urlJson['theme']) && isset($urlJson['day']) && $urlJson['day'] == sysdate('date')) $theme = $urlJson['theme'];
            else $theme = null;
        }        
        // session 加载
        if(empty($theme)) $theme = $this->reportThemeChooseRpt();        
        $this->reportThemeChooseRpt($theme);    // 记录选择结果
        switch($theme){            
            case 'bootstrap':    // bootstrap 风格化  theme_default
                return $this->iThemeBootstrap();
            default:            // 默认 风格化
                $check = $this->reportThemeChooseRpt();
                if($check != 'default') $this->reportThemeChooseRpt('default');
                return $this->iThemeDefault();
        }
    }
    private function _dev()
    {
        $html = '
            <a href="javascript:void(0);" class="app_btn" dataid="sysadmin" dataurl="/conero/index/appsh/app/admin"><div class="app">系统管理</div></a>  
        ';
        $this->assign('app',$html);
    }
    public function test()
    {
        //phpinfo();
        //debugOut(get_defined_constants(true)['user'],true);
        //debugOut(get_defined_constants(true)['user']);
        if(isset($_GET['this'])){
            debugOut($this,true);return;
        }elseif(isset($_GET['config'])){
            debugOut(get_defined_constants(true)['user'],true);return;
        }elseif(isset($_GET['jsvar'])){
            //var_dump(config('vst_session'));
            $this->_JsVar('key','sssssss');
            $this->_JsVar('key2','sssssss');
            $this->_JsScript('
                var ujju = "666666";
            ');
            //debugOut($this->_JsVar(),true);
            return $this->fetch('test');
        }
        $this->gLoginAuth('EllisConero','Brximl');
        $vit = function($t){
            debugOut($t->aboutVisit(),true);
        };
        $vit($this);
        echo date('Y-m-d');
        echo '<br>'.time();
        echo '<br>->TTTT';;
        
    }
}
