<?php
namespace Wpf\App\Files\Controllers;
class IndexController extends \Wpf\Common\Controllers\CommonController
{

    public function indexAction()
    {
        $this->response->setStatusCode(404, "Not Found");
        $s_url = "";
        if(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'])
            $s_url =$_SERVER['REQUEST_URI'];
        
        if($endpos = strpos($s_url,"?")){
            $s_url = substr($s_url,0,$endpos);
        }
        //var_dump($s_url);

        $url = $s_url;//请求url
        $redirect = trim($s_url);//重定位置
        
        
        if(!$redirect){
            $this->response->send();
            exit;
        } 
        $paths = explode('/',$redirect);
        
        if(in_array($paths[1],$this->config->PHOTO_ALLOW_PATH->toArray())){
            //\Common\Api\PhotoApi::processImage($paths, $redirect, $url);
            
            $model = new \Wpf\Common\Models\Photo();
            
            
            $model->processImage($paths,$redirect,$url);
        }
        $this->response->send();
        exit;
    }

}

