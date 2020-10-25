<?php
/**
 * 公共函数
 */

if (!function_exists("M")) {
    /**
     * 获取模块实例
     *
     * @param string $name 模块名称
     *
     * @return mixed
     */
    function M($name)
    {
        static $instances = array();
        if (isset($instances[$name])) {
            return $instances[$name];
        }

        $classFile = App::getRootPath() . "/module/$name.php";
        if (is_file($classFile)) {
            include_once $classFile;
        }

        $className = "Module$name";
        if (class_exists($className)) {
            $instances[$name] = new $className();
            return $instances[$name];
        }

        return null;
    }
}

if (!function_exists("getHtmlTitle")) {
    /**
     * 获取网页标题
     *
     * @param string $url 网址
     */
    function getHtmlTitle($url)
    {
        $url = trim($url);
        if (!$url) {
            return false;
        }

        $res = @http($url, null, 10, array(CURLOPT_FOLLOWLOCATION => true));
        if ($res === false) {
            return false;
        }

        $isGBK = preg_match("/<meta[^>]+(gbk|gb2312|big5)[^>]*>/iU", $res);
        preg_match("/<title>([\w\W]+)<\/title>/iU", $res, $m);
        $title = isset($m[1]) ? trim(preg_replace("/\r|\n/", "", $m[1])) : "";
        $title = preg_replace("/\s+/", " ", $title);
        if ($isGBK) {
            $title = iconv("GBK", "UTF-8", $title);
        }

        return trim($title);

    }
}

if (!function_exists("getPageHtml2")) {
    /**
     * 分页函数
     *
     * @param string $url 分页URL
     * @param int $page 当前页数
     * @param int $each 每页显示记录数
     * @param int $count 总记录数
     *
     * @return string
     */
    function getPageHtml2($url, $page, $each, $count)
    {
        $page = max((int) $page, 1);
        $each = max((int) $each, 1);
        $count = (int) $count;
        if ($count < 1) {
            return "";
        }

        $total_page = ceil($count / $each);
        $html = "<strong>$page</strong>";
        $len = 6;
        $l = $r = 1;
        $min = $max = $page;
        while (1) {
            if ($page - $l > 0) {
                $_page = $page - $l;
                $_url = str_replace("[p]", $_page, $url);
                $html = '<a href="' . $_url . '">' . $_page . '</a>' . $html;
                $l++;
                $len--;
                $min = $_page;
            }
            if ($len == 0) {
                break;
            }

            if ($page + $r < $total_page + 1) {
                $_page = $page + $r;
                $_url = str_replace("[p]", $_page, $url);
                $html .= '<a href="' . $_url . '">' . $_page . '</a>';
                $r++;
                $len--;
                $max = $_page;
            }
            if ($len == 0) {
                break;
            }

            if ($page - $l <= 0 && $page + $r > $total_page) {
                break;
            }

        }

        if ($min > 2) {
            $html = '<span>...</span>' . $html;
        }

        if ($max < $total_page - 1) {
            $html .= '<span>...</span>';
        }

        if ($min > 1) {
            $_url = str_replace("[p]", 1, $url);
            $html = '<a href="' . $_url . '">1</a>' . $html;
        }

        if ($max < $total_page) {
            $_url = str_replace("[p]", $total_page, $url);
            $html .= '<a href="' . $_url . '">' . $total_page . '</a>';
        }

        return $html;
    }
}

if (!function_exists("getPageHtml3")) {
    /**
     * 分页函数
     *
     * @param string $url 分页URL
     * @param int $page 当前页数
     * @param int $each 每页显示记录数
     * @param int $count 总记录数
     *
     * @return string
     */
    function getPageHtml3($url, $page, $each, $count)
    {
        $page = max((int) $page, 1);
        $each = max((int) $each, 1);
        $count = (int) $count;
        if ($count < 1) {
            return "";
        }

        $total_page = ceil($count / $each);
        $html = "";
        if ($page > 1) {
            $_url = str_replace("[p]", $page - 1, $url);
            $html .= '<a href="' . $_url . '">上一页</a>';
        }
        if ($page < $total_page) {
            $_url = str_replace("[p]", $page + 1, $url);
            $html .= '<a href="' . $_url . '">下一页</a>';
        }
        return $html;
    }
}

if (!function_exists("getOauthInstance")) {
    /**
     * 获取第三方登录接口实例
     *
     * @param string $type 第三方登录类型
     *
     * @return mixed
     */
    function getOauthInstance($type)
    {
        $config = include_once App::getRootPath() . "/config/oauth.php";
        $libPath = App::getRootPath() . "/include/oauth";
        switch ($type) {
            case "weibo":
                include_once $libPath . "/OauthWeibo.php";
                return new OauthWeibo($config["weibo"]);
            case "qq":
                include_once $libPath . "/OauthQQ.php";
                return new OauthQQ($config["qq"]);
            case "baidu":
                include_once $libPath . "/OauthBaidu.php";
                return new OauthBaidu($config["baidu"]);
            default:
                return null;
        }
    }
}
