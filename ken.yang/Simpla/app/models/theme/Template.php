<?php

/*
 * 主题模板输出
 */

class Template {

    /**
     * css组装
     * @param array $css
     * @return string
     */
    public static function css($css) {
        $html = '';
        foreach ($css as $row) {
            //$html .= '<link href="' . THEME_STATIC .'/'.$row . '" rel="stylesheet">';
            $html .= HTML::style(THEME_STATIC . '/' . $row['url'], array('weight' => $row['weight']));
        }
        return $html;
    }

    /**
     * js组装
     * @param array $js
     * @return string
     */
    public static function js($js) {
        $html = '';
        foreach ($js as $row) {
            //$html .= '<script src="' . THEME_STATIC .'/'.$row . '"></script>';
            $html .= HTML::script(THEME_STATIC . '/' . $row['url'], array('weight' => $row['weight']));
        }
        return $html;
    }

    /**
     * 添加css
     * @param type $data
     * @param type $weight
     */
    public static function add_css($data, $weight) {
        $css[] = array('url' => $data, 'weight' => $weight);
        //进行排序
        // 取得列的列表 
        foreach ($css as $key => $row) {
            $url[$key] = $row['url'];
            $weight[$key] = $row['weight'];
        }

        // 将数据根据 volume 降序排列，根据 edition 升序排列 
        // 把 $data 作为最后一个参数，以通用键排序 
        array_multisort($url, SORT_DESC, $weight, SORT_ASC, $css);
        View::share('css', $css);
        View::share('theme_css', self::css($css));
    }

    /**
     * 添加JS
     * @param type $data
     * @param type $weight
     */
    public static function add_js($data, $weight) {
        $js[] = array('url' => $data, 'weight' => $weight);
        //进行排序
        // 取得列的列表 
        foreach ($js as $key => $row) {
            $url[$key] = $row['url'];
            $weight[$key] = $row['weight'];
        }

        // 将数据根据 volume 降序排列，根据 edition 升序排列 
        // 把 $data 作为最后一个参数，以通用键排序 
        array_multisort($url, SORT_DESC, $weight, SORT_ASC, $css);
        View::share('js', $js);
        View::share('theme_js', self::js($js));
    }

}
