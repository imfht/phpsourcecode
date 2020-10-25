<?php

/**
 * 文档url处理类
 * Class Url
 */
final class Url
{
    /**
     * 栏目url
     * @param $category 栏目
     * @return string
     */
    static function category($category)
    {
        switch ($category['cattype']) {
            case 3 :
                //外部链接
                return $category['cat_redirecturl'];
            case 4 :
                $ModelCache = S('model');
                //单文章
                $db = M('category');
                $table=$ModelCache[$category['mid']]['table_name'];
                $content = $db->join("__category__ c JOIN __{$table}__ t ON c.cid=t.cid")->where("c.cid={$category['cid']}")->find();
                if ($content) {
                    if ($category['cat_url_type'] == 1) {
                        //栏目生成静态
                        return Url::content($content);
                    } else {
                        return U('Index/Content/index', array('cid' => $content['cid'], 'aid' => $content['aid']));
                    }
                }
                break;
            case 2 :
            case 1 :
            default :
                //静态根目录
                $html_path = C("HTML_PATH") ? C("HTML_PATH") . '/' : '';
                //普通栏目
                if ($category['cat_url_type'] == 1) {
                    //栏目生成静态
                    return __ROOT__ . '/' . $html_path . $category['catdir'];
                } else {
                    return U('Index/Category/index', array('cid' => $category['cid']));
                }
        }
    }

    /**
     * 获得内容页url地址
     * 用于处理单页面与普通文章的url
     * @param array $field 文章字段
     * @return string
     */
    static public function content($field)
    {
        //没有跳转地址时
        if (empty($field['redirecturl'])) {
            /**
             * 满足以下任意条件才生成静态
             * 1 文章字段定义生成静态
             * 2 栏目开启内容页生成静态并且生成静态规则不为空
             */
            switch ($field['url_type']) {
                case 1 :
                    //文章字段设置为静态访问
                    return Url::contentHtml($field);
                case 2 :
                    //文章字段设置为动态访问
                    return U('Index/Content/index', array('cid' => $field['cid'], 'aid' => $field['aid']));
                case 3 :
                default :
                    //继承栏目
                    switch ($field['arc_url_type']) {
                        case 1 :
                            //静态
                            return Url::contentHtml($field);
                        case 2 :
                            //动态
                            return U('Index/Content/index', array('cid' => $field['cid'], 'aid' => $field['aid']));
                    }
            }
        } else {
            //文章设置跳转地址
            return $field['redirecturl'];
        }

    }

    /**
     * 获得内容页静态URL地址
     * @param $field
     * @return null|string
     */
    static public function contentHtml($field)
    {
        //HTML存放根目录
        $html_path = C("HTML_PATH") ? C("HTML_PATH") . '/' : '';
        //有自定义静态url时，直接使用（不需要通过栏目规则运算）
        if (!empty($field['html_path'])) {
            return __ROOT__ . '/' . $html_path . $field['html_path'];
        } else {
            //根据栏目规则
            $cache = S("category");
            $category = $cache[$field['cid']];
            //栏目定义的内容页生成静态规则
            $arc_html_url = $category['arc_html_url'];
            $_s = array('{catdir}', '{y}', '{m}', '{d}', '{aid}');
            //文章发表时间
            $time = getdate($field['addtime']);
            $_r = array($category['catdir'], $time['year'], $time['mon'], $time['mday'], $field['aid']);
            return __ROOT__ . '/' . $html_path . str_replace($_s, $_r, $arc_html_url);
        }
    }

}