<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\model;

use cms\model\_category;

/**
 * 导航
 * @author sigmazel
 * @since v1.0.2
 */
class _nav{
	//导航标签
    public function block_multi($json){
        $_category = new _category();

        $params = json_decode($json, 1);

        if($params['cache']){
            $key = str_replace('-', '_', $params['key']).'_nav';

            $navs = cache_read($key);
            if(!is_array($navs)){
                $navs = $_category->get_tree(0, 'nav');
                cache_write($key, $navs);
            }
        }else $navs = $_category->get_tree(0, 'nav');

        return $navs;
    }

    //导航路径标签
    public function block_crumbs($json){
        $_category = new _category();

        $params = json_decode($json, 1);

        if(empty($params['identity'])) return null;

        $identity = $params['identity'];

        if(substr($params['identity'], 0, 1) == '$'){
            if(strpos($params['identity'], '[') === false){
                $identity = $GLOBALS[substr($params['identity'], 1)];
            }else{
                $identity_var = substr($params['identity'], 1, strpos($params['identity'], '[') - 1);
                $identity_key = substr($params['identity'], strpos($params['identity'], '['));
                $identity_key = str_replace(array('[', ']'), '', $identity_key);

                $identity = $GLOBALS[$identity_var][$identity_key];
            }

            return $_category->get_crumbs($identity);
        }

        $category = $_category->get_by_identity($identity, 'nav');
        return $_category->get_crumbs($category);
    }
    
}
?>