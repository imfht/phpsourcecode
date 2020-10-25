<?php
use think\Url;

if (!function_exists('url')) {
    /**
     * Url生成
     * @param string        $url 路由地址
     * @param string|array  $vars 变量
     * @param bool|string   $suffix 生成的URL后缀
     * @param bool|string   $domain 域名
     * @return string
     */
    function url($url = '', $vars = '', $suffix = true, $domain = false)
    {
        static $array = null;
        if ($array===null) {
            $array = @include(RUNTIME_PATH.'url_cfg.php');
            if (empty($array)) {
                $array = [];
            }
        }
        if ($vars && is_string($vars)) {
            parse_str($vars,$vars);
        }
        $par = '';
        $_vars = $vars;     //避免改变顺序
        if ($vars) {
            ksort($vars);
            $par = http_build_query($vars);
        }
        
        if ($domain===false) {
            $url = full_url($url);
            list($m_name,$m_file,$m_action) = explode('/', $url);
            $md = modules_config($m_name);
            if(IN_WAP===true){
                if ($md['wap_domain']) {
                    $domain = $md['wap_domain'];
                }
            }else{
                if ($md['pc_domain']) {
                    $domain = $md['pc_domain'];
                }
            }
            
            if ($domain===false) {                
                if(IN_WAP===true && config('webdb.wap_domain')){
                    $domain = config('webdb.wap_domain');
                }elseif(config('webdb.pc_domain')){
                    $domain = config('webdb.pc_domain');
                }
            }
        }
        
        if ($par && $array[$url][$par]) {
            $_url = Url::build($url.'?'.$par, [], $suffix, $domain);
        }else{
            $_url = Url::build($url, $_vars, $suffix, $domain);
        }
        
        if ( ($m_name=='index'&&!in_array($m_file, ['alonepage','index','login','reg','plugin']))|| ($m_name=='member'&&!in_array($m_file, ['index'])) ) { //避免ajax或框架的跨域
            $_url = preg_replace("/^(http|https):\/\/([^\/]+)\//i", "/",$_url);
        }elseif($url=='index/index/index'){
            $_url = str_replace('index/index/index.html','',$_url);
        }
        return $_url;
        
    }
}