<?php

/**
 * 合并加载JS和CSS文件
 *
 * @author brivio
 */
class loadTag {

    private $jm;
    private $dir;

    function __construct() {
        $this->jm = new JSMin();
        $this->dir = new Dir();
    }

    public function js($options) {
        $path = MT_DATA_PATH . 'static/' . md5($options['href']) . '.js';
        $statics_url = C('wkcms_statics_url') ? C('wkcms_statics_url') : './public';
        if (!is_file($path)) {
            //静态资源地址
            $files = explode(',', $options['href']);
            $content = '';
            foreach ($files as $val) {
                $val = str_replace('__PUBLIC__', $statics_url, $val);
                $content.=file_get_contents($val);
            }
            file_put_contents($path, $this->jm->minify($content));
        }
        echo ( '<script type="text/javascript" src="' . __ROOT__ . '/data/static/' . md5($options['href']) . '.js?' . WKCMS_RELEASE . '"></script>');
    }
}