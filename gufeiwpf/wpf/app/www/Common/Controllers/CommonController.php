<?php
namespace Wpf\App\Www\Common\Controllers;
class CommonController extends \Wpf\Common\Controllers\CommonController{
    public function initialize(){
        parent::initialize();
        
    }
    
    public function onConstruct(){
        parent::onConstruct();
        
        
        $this->headercssurl
            ->addCss(STATIC_URL."/css/google/font_shop.css",false,false)
            ->addCss(STATIC_URL."/theme/assets/global/plugins/font-awesome/css/font-awesome.min.css",false,false);
            //->addCss(STATIC_URL."/theme/assets/global/plugins/simple-line-icons/simple-line-icons.min.css",false,false);
            //->addCss(STATIC_URL."/"."theme/assets/global/css/components.css",false,false,array("id"=>"style_components"))
            //->addCss(STATIC_URL."/"."theme/assets/admin/layout/css/themes/darkblue.css",false,false,array("id"=>"style_color"));
        
        $this->headercss
            ->setPrefix(STATIC_URL."/")      
            //->addCss("css/google/font.css")
            //->addCss("theme/assets/global/plugins/font-awesome/css/font-awesome.min.css")
            //->addCss("theme/assets/global/plugins/simple-line-icons/simple-line-icons.min.css")
            ->addCss("theme/assets/global/plugins/bootstrap/css/bootstrap.min.css")
            ->addCss("theme/assets/global/plugins/fancybox/source/jquery.fancybox.css")
            ->addCss("theme/assets/global/plugins/carousel-owl-carousel/owl-carousel/owl.carousel.css")
            ->addCss("theme/assets/global/plugins/slider-revolution-slider/rs-plugin/css/settings.css");
             
        $this->headercss__    
            ->setPrefix(STATIC_URL."/")     
            ->addCss("theme/assets/global/css/components.css",false,false,array("id"=>"style_components"))
            ->addCss("theme/assets/frontend/layout/css/style.css")
            ->addCss("theme/assets/frontend/pages/css/style-revolution-slider.css")
            ->addCss("theme/assets/frontend/layout/css/style-responsive.css")
            ->addCss("theme/assets/frontend/layout/css/themes/red.css",false,false,array("id"=>"style-color"))
            ->addCss("theme/assets/frontend/layout/css/custom.css");
            
            
        $this->headerjs
            ->setPrefix(STATIC_URL."/")     
            ->addJs("theme/assets/global/plugins/jquery.min.js");
            
            
        $this->footerjs
            ->setPrefix(STATIC_URL."/")     
            ->addJs("theme/assets/global/plugins/jquery-migrate.min.js")
            ->addJs("theme/assets/global/plugins/jquery-ui/jquery-ui.min.js")
            
            ->addJs("theme/assets/global/plugins/bootstrap/js/bootstrap.min.js")
            ->addJs("theme/assets/frontend/layout/scripts/back-to-top.js")
            
            ->addJs("theme/assets/global/plugins/fancybox/source/jquery.fancybox.pack.js")
            ->addJs("theme/assets/global/plugins/carousel-owl-carousel/owl-carousel/owl.carousel.min.js")
            ->addJs("theme/assets/global/plugins/slider-revolution-slider/rs-plugin/js/jquery.themepunch.revolution.min.js")
            ->addJs("theme/assets/global/plugins/slider-revolution-slider/rs-plugin/js/jquery.themepunch.tools.min.js")
            ->addJs("theme/assets/frontend/pages/scripts/revo-slider-init.js")
            
            ->addJs("theme/assets/frontend/layout/scripts/layout.js");
            
            
    }
}