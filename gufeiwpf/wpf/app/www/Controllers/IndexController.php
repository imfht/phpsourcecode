<?php
namespace Wpf\App\Www\Controllers;
class IndexController extends \Wpf\App\Www\Common\Controllers\CommonController{

    public function indexAction(){
        $this->headercss
            ->addCss("theme/assets/frontend/onepage/css/style.css")
            ->addCss("theme/assets/frontend/onepage/css/style-responsive.css")
            ->addCss("theme/assets/frontend/onepage/css/themes/red.css")
            ->addCss("theme/assets/frontend/onepage/css/custom.css");
            
        //$this->footerjs
        //    ->addJs("theme/assets/global/plugins/jquery-migrate.min.js");
    }

}

