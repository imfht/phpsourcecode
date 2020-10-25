<?php

/*
 * 模板配制
 */

return array(
    /**
     * 模板解析左分隔符
     * @param string
     */
    'left_delimiter' => '{',
    /**
     * 模板解析右分隔符
     * @param string
     */
    'right_delimiter' => '}',
    /**
     * 是否运行模板内插入PHP代码
     * @param bool
     */
    'php_off' => true,
    /**
     * 自动创建子目录
     * @param bool
     */
    'use_sub_dirs' => true,
    /**
     * 是否压缩模板
     * @param bool
     */
    'strip_space' => true,
    /**
     * Gzip数据压缩传输
     * @param bool
     */
    'header_gzip' => true,
    /**
     * 模板标签禁用黑名单
     * @param string 
     */
    'black_list' => array('\$this', 'raintpl::', 'self::', '_SESSION', '_SERVER', '_ENV', 'eval', 'exec', 'unlink', 'rmdir'),
    /**
     * 样式路径
     * @param string 
     */
    'style_path_url' => '/style/',
    /**
     * 模板缓存过期时间,为-1，则设置缓存永不过期,0可以让缓存每次都重新生成
     * @param int
     */
    'cache_lifetime' => 3600,
    /**
     * 编译目录
     * @param string
     */
    'compile_dir' => dirname(\feros\view::DIR) . \feros\view::DS . 'Runtime' . \feros\view::DS . 'Compile',
    /**
     * 缓存目录
     * @param string
     */
    'cache_dir' => dirname(\feros\view::DIR) . \feros\view::DS . 'Runtime' . \feros\view::DS . 'Cache',
    /**
     * 模板目录 多个目录用数组
     * @param array|string
     */
    'template_path' => dirname(\feros\view::DIR) . \feros\view::DS . 'Template',
    /**
     * 模板输出编码
     * @param string
     */
    'template_charset' => 'utf-8',
    /**
     * 模板输出类型
     * @param string
     */
    'template_type' => 'text/html',
    /**
     * 模板后缀
     * @param string
     */
    'template_suffix' => '.html',
    /**
     * 模板语言包
     * @param string
     */
    'template_lang' => 'zh-cn',
    /**
     * 模板大小 单位MB
     * @param int
     */
    'template_size' => 1,
);