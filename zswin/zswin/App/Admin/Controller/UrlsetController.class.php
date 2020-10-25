<?php
namespace Admin\Controller;

class UrlsetController extends CommonController {

    public function index() {
        $config_file = './App/Home/Conf/url.php';
        $config = require $config_file;
       
        if (IS_POST) {
            $url_model = I('url_model',0, 'int');
            $url_suffix = I('url_suffix');
            $url_depr = I('url_depr');
            $new_config = array(
                'URL_MODEL' => $url_model,
                'URL_HTML_SUFFIX' => $url_suffix,
                'URL_PATHINFO_DEPR' => $url_depr,
            );
            $var='var url_mode = \''.$url_model.'\',url_htm = \''.$url_suffix.'\',url_str = \''.$url_depr.'\';';
            if ($this->update_config($new_config, $config_file)) {
            	
                 $this->mtReturn(200,'修改成功！');
            } else {
               
                $this->mtReturn(300, '修改失败！');
            }
            
            
            
        } else {
        	
            $this->assign('config', $config);
            $this->display();
        }
    }
    
   
}