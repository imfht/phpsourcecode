<?php

/*
 * 主题系统逻辑，都在这里处理
 */

class Theme {

    /**
     * node逻辑处理
     * @param type $node
     * @param type $type
     * @return string
     * node页面覆写机制
     * 1、先查找node-nid.blade.php
     * 2、再查找node.blade.php
     *
     * category页面覆写机制
     * 1、先查找node-type.blade.php
     * 2、再查找node.blade.php
     *
     * home首页覆写机制
     * 1、先查找node-home.blade.php
     * 2、再查找node.blade.php
     */
    public static function node($node, $type = null) {//print_r($node);die();
        //1、查找本主题
        if (isset($type)) {
            if (View::exists('Theme::templates.node-' . $type)) {
                $view_name = 'Theme::templates.node-' . $type;
            } else {
                $view_name = 'Theme::templates.node';
            }
        } elseif (View::exists('Theme::templates.node-' . $node['id'])) {
            $view_name = 'Theme::templates.node-' . $node['id'];
        } elseif (View::exists('Theme::templates.node')) {
            $view_name = 'Theme::templates.node';
        } else {
            //2、查找系统默认主题
            $view_name = self::node_default($node, $type);
        }

        return $view_name;
    }

    public static function node_default($node, $type = null) {
        if (isset($type)) {
            if (View::exists('DefaultTheme::templates.node-' . $type)) {
                $view_name = 'DefaultTheme::templates.node-' . $type;
            } else {
                $view_name = 'DefaultTheme::templates.node';
            }
        } elseif (View::exists('DefaultTheme::templates.node-' . $node['id'])) {
            $view_name = 'DefaultTheme::templates.node-' . $node['id'];
        } elseif (View::exists('DefaultTheme::templates.node')) {
            $view_name = 'DefaultTheme::templates.node';
        } else {
            $view_name = '';
        }
        return $view_name;
    }

    /**
     * -----------------------------------------------------------------------
     * category逻辑处理
     * @param type $nodes
     * @param type $category
     * @param type $type
     * @return string
     * category页面覆写机制
     * 1、先查找category-id.blade.php
     * 2、再查找category.blade.php
     */
    public static function category($nodes, $category, $type = null) {
        //1、查找本主题
        if (View::exists('Theme::templates.category-' . $category['id'])) {
            $view_name = 'Theme::templates.category-' . $category['id'];
        } elseif (View::exists('Theme::templates.category')) {
            $view_name = 'Theme::templates.category';
        } else {
            //2、查找系统默认主题
            $view_name = self::category_default($category);
        }

        return $view_name;
    }

    public static function category_default($category) {
        if (View::exists('DefaultTheme::templates.category-' . $category['id'])) {
            $view_name = 'DefaultTheme::templates.category-' . $category['id'];
        } elseif (View::exists('DefaultTheme::templates.category')) {
            $view_name = 'DefaultTheme::templates.category';
        } else {
            $view_name = '';
        }
        return $view_name;
    }

    //专门为分类的node
    public static function node_category($nodes, $category, $type = null) {
        $html = '';
        if (View::exists('Theme::templates.node-category-' . $category['id'])) {
            foreach ($nodes as $node) {
                $html .= View::make('Theme::templates.node-category-' . $category['id'], $node);
            }
        } elseif (View::exists('Theme::templates.node-category')) {
            foreach ($nodes as $node) {
                $html .= View::make('Theme::templates.node-category', $node);
            }
        } else {
            $html = self::node_category_default($nodes, $category);
        }
        if (empty($html)) {
            $html = '<p>该分类下无任何内容</p>';
        }
        return $html;
    }

    public static function node_category_default($nodes, $category) {
        $html = '';
        if (View::exists('DefaultTheme::templates.node-category-' . $category['id'])) {
            foreach ($nodes as $node) {
                $html .= View::make('DefaultTheme::templates.node-category-' . $category['id'], $node);
            }
        } elseif (View::exists('DefaultTheme::templates.node-category')) {
            foreach ($nodes as $node) {
                $html .= View::make('DefaultTheme::templates.node-category', $node);
            }
        } else {
            $html = '';
        }

        return $html;
    }

    //专门为首页的node
    public static function node_home($nodes, $type = null) {
        $html = '';
        if (View::exists('Theme::templates.node-home')) {
            foreach ($nodes as $node) {
                $html .= View::make('Theme::templates.node-home', $node);
            }
        } elseif (View::exists('DefaultTheme::templates.node-home')) {
            foreach ($nodes as $node) {
                $html .= View::make('DefaultTheme::templates.node-home', $node);
            }
        } else {
            $html .= '';
        }

        return $html;
    }

    /**
     * --------------------------------------------------------------------
     * blogk区块逻辑处理
     * @param type $blocks
     * @param type $type
     * @return string
     * block页面覆写机制
     * 1、先查找block-id.blade.php
     * 2、再查找block-area.blade.php
     * 3、最后查找block.blade.php
     */
    public static function block($blocks, $name, $type = null) {
        $html = '';
        foreach ($blocks as $block) {
            //如果是系统(system)或者第三方模块(model)，则执行方法
            if ($block['type'] != 'customer') {
                $callback = $block['callback'];
                eval("\$block['body'] = $callback"); //使用eval函数执行
            }
            //如果是普通模块(customer)，则直接进入主题模板
            if (View::exists('Theme::templates.block-' . $block->id)) {
                $view_name = 'Theme::templates.block-' . $block->id;
                $html .= View::make($view_name, $block);
            } elseif (View::exists('Theme::templates.block-' . $name)) {
                $view_name = 'Theme::templates.block-' . $name;
                $html .= View::make($view_name, $block);
            } elseif (View::exists('DefaultTheme::templates.block')) {
                $view_name = 'Theme::templates.block';
                $html .=View::make($view_name, $block);
            } else {
                //2、查找系统默认主题
                $html = self::block_default($block, $name);
            }
        }
        return $html;
    }

    public static function block_default($block, $name) {
        if (View::exists('DefaultTheme::templates.block-' . $block->id)) {
            $view_name = 'DefaultTheme::templates.block-' . $block->id;
            $html .= View::make($view_name, $block);
        } elseif (View::exists('DefaultTheme::templates.block-' . $name)) {
            $view_name = 'DefaultTheme::templates.block-' . $name;
            $html .= View::make($view_name, $block);
        } elseif (View::exists('DefaultTheme::templates.block')) {
            $view_name = 'DefaultTheme::templates.block';
            $html .=View::make($view_name, $block);
        } else {
            $view_name = '';
            $html .='';
        }
        return $html;
    }

    /**
     * -----------------------------------------------------------------------
     * user逻辑处理
     * @param type $user
     * @param type $type
     * @return string
     * user页面覆写机制
     * 1、先查找user-id.blade.php
     * 2、再查找user.blade.php
     */
    public static function user($user, $type = null) {
        //1、查找本主题
        if (View::exists('Theme::templates.user.user-' . $user['id'])) {
            $view_name = 'Theme::templates.user.user-' . $user['id'];
        } elseif (View::exists('Theme::templates.user.user')) {
            $view_name = 'Theme::templates.user.user';
        } else {
            //2、查找系统默认主题
            $view_name = self::user_default($user);
        }

        return $view_name;
    }

    public static function user_default($user) {
        //1、查找本主题
        if (View::exists('DefaultTheme::templates.user.user-' . $user['id'])) {
            $view_name = 'DefaultTheme::templates.user.user-' . $user['id'];
        } elseif (View::exists('DefaultTheme::templates.user.user')) {
            $view_name = 'DefaultTheme::templates.user.user';
        } else {
            $view_name = '';
        }

        return $view_name;
    }

    /**
     * ----------------------------------------------------
     * 消息
     * ----------------------------------------------------
     */
    public static function message() {
        if (View::exists('Theme::templates/message')) {
            $view_name = 'Theme::templates/message';
        } elseif (View::exists('DefaultTheme::templates/message')) {
            $view_name = 'DefaultTheme::templates/message';
        } else {
            $view_name = '';
        }

        return $view_name;
    }

    /**
     * -----------------------------------------------------
     * 站点维护
     * -----------------------------------------------------
     */
    public static function maintenance() {
        //前端主题
        $theme_default = Setting::find('theme_default')->value;
        View::addNamespace('Theme', dirname(dirname(__DIR__)) . '/themes/' . $theme_default . '/');
        //前端默认主题
        View::addNamespace('DefaultTheme', dirname(dirname(__DIR__)) . '/views/frontend/default/');
        if (View::exists('Theme::templates.maintenance')) {
            $view_name = 'Theme::templates.maintenance';
        } elseif (View::exists('DefaultTheme::templates.maintenance')) {
            $view_name = 'DefaultTheme::templates.maintenance';
        } else {
            $view_name = '';
        }

        return $view_name;
    }

    /**
     * -----------------------------------------------------
     * 默认
     * -----------------------------------------------------
     */
    public static function template($template) {
        if (View::exists('Theme::templates.' . $template)) {
            $view_name = 'Theme::templates.' . $template;
        } elseif (View::exists('DefaultTheme::templates.' . $template)) {
            $view_name = 'DefaultTheme::templates.' . $template;
        } else {
            $view_name = '';
        }

        return $view_name;
    }

}
