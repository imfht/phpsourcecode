<?php
class seoAction extends backendAction {

    public function url() {
        $config_file = CONF_PATH . 'url.php';
        $config = require $config_file;
        if (IS_POST) {
            $url_model = $this->_post('url_model', 'intval', 0);
            $url_suffix = $this->_post('url_suffix', 'trim');
            $url_depr = $this->_post('url_depr', 'trim');
            $new_config = array(
                'URL_MODEL' => $url_model,
                'URL_HTML_SUFFIX' => $url_suffix,
                'URL_PATHINFO_DEPR' => $url_depr,
            );
            $var='var url_mode = \''.$url_model.'\',url_htm = \''.$url_suffix.'\',url_str = \''.$url_depr.'\';';
            if ($this->update_config($new_config, $config_file)) {
            	$this->write_var($var);
                $this->ajaxReturn(1, L('operation_success'));
            } else {
                $this->ajaxReturn(0, L('file_no_authority'));
            }
            
            
            
        } else {
            $this->assign('config', $config);
            $this->display();
        }
    }

    public function page() {
        $setting_mod = D('global');
        if (IS_POST) {
            $seo_config = $_POST['seo_config'];
            $seo_config = serialize($seo_config);
            $setting_mod->where(array('name'=>'seo_config'))->save(array('data'=>$seo_config));
            $this->ajaxReturn(1, L('operation_success'));
        } else {
            $seo_config = $setting_mod->where(array('name'=>'seo_config'))->getField('data');
            $this->assign('seo_config', unserialize($seo_config));
            $this->display();
        }
    }
}