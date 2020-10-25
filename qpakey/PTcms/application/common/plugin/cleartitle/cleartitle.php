<?php
class cleartitlePlugin extends PT_Plugin{
    //插入的hook
    public $tag='template_compile_end';
    // 默认配置
    public $config=array();
    // 执行方法
    public function run(&$content)
    {
        //$config=$this->loadconfig();
        $content=preg_replace('{\s*-\s*Power\s*by\s*PTcms\s*</title>}isU','</title>',$content);
        return $content;
    }
}