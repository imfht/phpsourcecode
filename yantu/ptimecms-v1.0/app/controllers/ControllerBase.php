<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    public function jsonResponse($error,$result="",$message=""){
        header('Content-Type: text/json; charset=utf-8');
        echo json_encode(["error"=>$error,"data"=>$result,"message"=>$message]);
        $this->view->disable();
        exit;
    }

    public function getGlobal(){
        //全局设置
        $settingResult = Setting::find();
        foreach ($settingResult as $value) {
            $setting[$value->keyword] = $value->value;
        }
        $setting = (object)$setting;

        //导航
        $navResultSource = Menu::find([
            "is_visible = 1",
            "order"=>"father_id ASC, weight DESC, id ASC"
            ]);

        foreach ($navResultSource as $nav) {
            $father_id  = $nav->father_id;
            $id         = $nav->id;

            if($nav->url == ""){
                $nav->url = "/".$nav->object."/".$nav->object_id;
            }

            $navResult[$father_id][] =  $nav;
        }
        foreach ($navResult as $navs) {
            foreach ($navs as $nav) {
                if(isset($navResult[$nav->id])){
                    $nav->url = $navResult[$nav->id][0]->url;
                }
            }
        }

        //友情链接
        $favolink_category_id = $setting->favolink_category_id;
        $favolinkResult = Link::find([
                "category_id = $favolink_category_id AND is_visible = 1 AND is_delete = 0",
                "order"=>"weight DESC, created_at DESC"
                ]);
        
   
        $this->view->setVar("nav",      $navResult);
        $this->view->setVar("favolink", $favolinkResult);
        $this->view->setVar("setting",  $setting);
    }

    public function route404(){
        echo "404";
        $this->view->disable();
        exit;
    }
	
}
