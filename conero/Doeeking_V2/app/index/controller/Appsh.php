<?php
namespace app\index\controller;
use think\Loader;
use think\Controller;
class Appsh extends Controller
{
    public function index()
    {}
    public function app()
    {
        $app = getUrlBind('app');
        $file = APP_PATH.$app.'/Apparition.php';
        if(is_file($file)){
            $class = '\\app\\'.$app.'\\Apparition';
            $evil = new $class();
            $opt = $evil->OptAction();
            $loadOption = [
                'auth'=>'','title'=>'Conero','js'=>['appsh/index'],'css'=>['appsh/index']
            ];
            if(isset($opt['auth'])) $loadOption['auth'] = $opt['auth'];
            if(isset($opt['title'])) $loadOption['title'] = $opt['title'];
            $app_url = isset($opt['home']) && !empty($opt['home'])? $opt['home']:null;
            $this->loadScript($loadOption);
            $this->_JsVar('_app_',$app);
            $this->assign([
                'app_nav'    => $evil->app_nav()
                ,'about_app' => $evil->about_app()
                ,'app_url'   => $app_url
            ]);
            return $this->fetch('app');
        }
        else{
            go('/conero/index/appsh/');
        }
    }
}