<?php
namespace workerbase\traits;
/**
 * @author fukaiyao
 */
trait Request {
    /**
     * @author fukaiyao
     * Xss过滤
     * @param string|array $input        要过滤的内容
     * @param bool $tags    去除脚本标签
     * @param bool $trim    去除两边空格
     * @return mixed|null|string|string[]
     */
    public function XssFilter($input, $tags = true, $trim = true) {
        if (is_array($input)) {
            foreach($input as $key => &$value)
            {
                if (is_array($value)) {
                    $value = $this->XssFilter($value);
                } else {
                    //去除字符串中两边多余的空格，剥去HTML、XML以及PHP的标签，转为html实体
                    if ($trim) {
                        $value = trim($value);
                    }
                    if ($tags) {
                        //替换脚本字样的字符串
                        $value = strip_tags($value);
                        $value = str_replace(array('<?', '<%', '<?php', '{php'), '', $value);
                        $value = preg_replace('/<s*?script.*(src)+/i', '', $value);
                    }
                    $value = htmlspecialchars($value);
                }

            }
        } else {
            if ($trim) {
                $input = trim($input);
            }
            if ($tags) {
                $input = strip_tags($input);
                $input = str_replace(array('<?', '<%', '<?php', '{php'), '', $input);
                $input = preg_replace('/<s*?script.*(src)+/i', '', $input);
            }
            $input = htmlspecialchars($input);
        }
        return $input;
    }

    /**
     * 获取post和get的参数
     * @author fukaiyao
     * @param $param      -要获取的参数名
     * @param $default    -单项默认值
     * @param $filter     -是否xss过滤
     * @return int|string|array|bool
     */
    public function GPC($param = '', $default = '', $filter = true){
        $_GPC = false;
        if (empty($param)) {
            $_GPC = $_GET;
            if (!empty($_POST)) {
                //参数合并
                $_GPC = array_merge($_GET, $_POST);
            }
        }
        elseif (isset($_GET[$param]) || isset($_POST[$param])) {
            if (isset($_GET[$param])) {
                $_GPC = $_GET[$param];
            }
            if (isset($_POST[$param])) {
                $_GPC = $_POST[$param];
            }
        }
        else {
           return $default;
        }

        if ($filter) {
            return $this->XssFilter($_GPC);
        } else {
            return $_GPC;
        }

    }

    /**
     * 获取post参数
     * @author fukaiyao
     * @param $param      -要获取的参数名
     * @param $default    -单项默认值
     * @param $filter     -是否xss过滤
     * @return int|string|array|bool
     */
    public function getPost($param = '', $default = '', $filter = true){
        $temp = false;
        if (empty($param)) {
            $temp = $_POST;
        }
        elseif (isset($_POST[$param])) {
            $temp = $_POST[$param];
        }
        else {
            return $default;
        }

        if ($filter) {
            return $this->XssFilter($temp);
        } else {
            return $temp;
        }

    }

    /**
     * 获取get参数
     * @author fukaiyao
     * @param $param      -要获取的参数名
     * @param $default    -单项默认值
     * @param $filter     -是否xss过滤
     * @return int|string|array|bool
     */
    public function getQuery($param = '', $default = '', $filter = true){
        $temp = false;
        if (empty($param)) {
            $temp = $_GET;
        }
        elseif (isset($_GET[$param])) {
            $temp = $_GET[$param];
        }
        else {
            return $default;
        }

        if ($filter) {
            return $this->XssFilter($temp);
        } else {
            return $temp;
        }

    }

    public function redirect($url,$statusCode=302)
    {
        if(substr($url, 0, 1) == '/') {
            $url = ltrim($url , '/');
        }
        header('Location: ' . loadf('getHost') . $url, true, $statusCode);
    }
}