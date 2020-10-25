<?php declare (strict_types = 1);
namespace msqphp\core\response;

use msqphp\base\filter\Filter;
use msqphp\base\header\Header;
use msqphp\base\json\Json;
use msqphp\base\xml\Xml;
use msqphp\core\config\Config;
use msqphp\core\traits;

final class Response
{
    use traits\CallStatic;

    use ResponseContentTrait;
    use ResponseFileTrait;

    // 回复类型
    public static $type = null;
    // 回复字符集
    public static $charset = null;

    // 抛出异常
    private static function exception(string $message): void
    {
        throw new ResponseException($message);
    }

    // 得到回复字符集
    private static function getCharset(): string
    {
        // 取框架配置中的字符集
        if (null === static::$charset) {
            static::$charset = Config::get('framework.charset');
        }
        return static::$charset;
    }

    // 发送对应header头
    private static function type(string $type)
    {
        // 如果回复格式不存在,即尚未回复
        if (null === static::$type) {
            // 赋值并发送header头
            static::$type = $type;
            Header::type($type, static::getCharset());
        } else {
            // 如果不为当前回复格式,异常
            $type === static::$type || static::exception('输出格式已经为' . static::$type . '无法再输出' . $type . '格式');
        }
    }

    public static function debugInfo(): void
    {
        if (\msqphp\Environment::getRunMode() === 'cli') {
            array_map(function ($v) {
                var_export($v);
            }, func_get_args());
            echo PHP_EOL;
        } else {
            echo '<pre>';

            array_map(function ($v) {
                var_export(Filter::html($v));
            }, func_get_args());

            echo '</pre><hr/>';
        }
    }

    public static function debugArray(array $data): void
    {
        array_map([__CLASS__, 'debugInfo'], $data);
    }
}

trait ResponseContentTrait
{
    /**
     * 打印数据
     *
     * @param   string  $type     数据格式
     * @param   stirng  $content  数据内容
     * @param   bool    $exit     是否退出
     * @param   bool    $return   返回对应值
     * @return  [type]
     */
    private static function dump(string $type, string $content, bool $exit, bool $return)
    {
        static::type($type);
        echo $content;
        $exit && exit;
        if ($return) {
            return $content;
        }
    }

    // xml格式返回
    public static function xml($xml_data, string $root = 'root', bool $exit = true, bool $return = false)
    {
        return static::dump('xml',
            '<?xml version="1.0" encoding="' . static::getCharset() . '"?><' . $root . '>' . Xml::encode($xml) . '</' . $root . '>'
            , $exit, $return);
    }

    // json格式返回
    public static function json($json_data, bool $exit = true, bool $return = false)
    {
        return static::dump('json', Json::encode($json_data), $exit, $return);
    }

    // html格式返回
    public static function html(string $html, bool $exit = true, bool $return = false)
    {
        return static::dump('html', $html, $exit, $return);
    }

    // JS窗口提示并跳转
    public static function alert(string $msg,  ? string $url = null, bool $exit = true, bool $return = false)
    {
        // 跳转页面
        $go_url = null === $url ? 'history.go(-1);' : 'window.location.href = "' . $url . '";';
        return static::dump('html',
            '<meta charset="' . static::$charset . '"><script type="text/javascript">alert("' . addslashes($msg) . '");' . $go_url . '</script>'
            , $exit, $return);
    }
    // js刷新页面
    public static function refresh(bool $exit = true) : void
    {
        static::dump('html',
            '<script type="text/javascript">location.reload();</script>'
            , $exit, $return);
    }

}

trait ResponseFileTrait
{
    /**
     * 打印一个文件
     *
     * @param   string  $____type        文件类型
     * @param   string  $____file        文件路径
     * @param   array   $____data        对应数据
     * @param   bool    $____exit        是否退出
     * @param   bool    $____return      是否返回输出内容
     * @return  ?string
     */
    private static function dumpFile(string $____type, string $____file, array $____data, bool $____exit, bool $____return)
    {
        // 文件不存在则异常
        is_file($____file) || static::exception($____file . '文件不存在,无法输出');
        // 设置回复格式
        static::type($____type);
        // 打散对应数据
        extract($____data, EXTR_OVERWRITE);
        // 静态则ob, 否则直接require
        if ($____return) {
            ob_start();
            ob_implicit_flush(0);
            require $____file;
            $____exit && exit;
            return ob_get_flush();
        } else {
            require $____file;
            $____exit && exit;
        }
    }

    /**
     * 得到视图文件对应路径(用于基础视图,success,error,unavailable,jump等视图)
     *
     * @param   string  $filename  文件类型
     *
     * @return  string
     */
    private static function getViewPath(string $filename): string
    {
        // 资源视图目录下是否存在
        $file = \msqphp\Environment::getPath('resources') . 'views' . DIRECTORY_SEPARATOR . $filename . '.html';
        // 不存在取框架资源中的视图
        is_file($file) || $file = \msqphp\Environment::getPath('framework') . 'resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $filename . '.html';
        // 仍不存在异常
        is_file($file) || static::exception($file . '视图文件不存在,无法进行相关操作');
        // 返回文件路径
        return $file;
    }
    // 打印多个文件
    private static function dumpFiles(string $type, array $files, array $data, bool $exit, bool $return)
    {
        // 如果返回内容
        if ($return) {
            $result = '';
            // 循环打印所有文件
            foreach ($files as $file) {
                $result .= static::dumpFile($type, $file, $data, false, true);
            }
            $exit && exit;
            // 返回对应结果
            return $result;
        } else {
            // 循环打印所有文件
            foreach ($files as $file) {
                static::dumpFile($type, $file, $data, false, false);
            }
            $exit && exit;
        }
    }
    // 打印一个html文件
    public static function htmlFile(string $file, array $data, bool $exit = false, bool $return = true)
    {
        return static::dumpFile('html', $file, $data, $exit, $return);
    }
    // 打印多个html文件
    public static function htmlFiles(array $files, array $data, bool $exit = false, bool $return = true)
    {
        return static::dumpFiles('html', $files, $data, $exit, $return);
    }
    // 错误信息显示
    public static function error(string $message, int $time = 3, string $url = '', bool $exit = true): void
    {
        static::htmlFile(static::getViewPath('error'),
            ['msg' => $message, 'time' => $time, 'url' => $url]
            , $exit, false);
    }
    // 成功信息显示
    public static function success(string $message, int $time = 3, string $url = '', bool $exit = true): void
    {
        static::htmlFile(static::getViewPath('success'),
            ['msg' => $message, 'time' => $time, 'url' => $url]
            , $exit, false);
    }
    // 不可用页面(维护)
    public static function unavailable(bool $exit = true): void
    {
        static::htmlFile(static::getViewPath('success'), [], $exit, false);
    }
    // 不可用页面(维护)
    public static function notFound(bool $exit = true): void
    {
        Header::status(404);
        static::htmlFile(static::getViewPath('notFound'), [], $exit, false);
    }

    // 页面重定向
    public static function redirect(string $url, int $code = 301, bool $exit = true): void
    {
        if (!headers_sent()) {
            // 发送一个重定向header
            Header::header('location:' . $url, true, $code);
        } else {
            echo '<meta http-equiv=\'Refresh\' content=\'' . $time . ';URL=' . $url . '\'>"';
        }
        $exit && exit;
    }

    // 页面跳转
    public static function jump(string $url, int $time = 0, string $message = '', bool $exit = true): void
    {
        // 如果时间大于0
        if ($time > 0) {
            // 发送一个刷新header
            Header::header('refresh:' . $time . ';url=' . $url);
            // 输出跳转页面
            static::htmlFile(static::getViewPath('jump'), ['message' => $message ?: '系统将在' . $time . '秒之后自动跳转到<a href="' . $url . '">' . $url . '</a>！'], false, false);
        } else {
            // 重定向
            static::redirect($url, 301, false);
        }
        $exit && exit;
    }

    // 下载文件
    public static function download(string $file, string $filename = '', string $type = ''): void
    {
        // 发送下载header头(下载header头会检测文件相关信息)
        Header::download($file, $filename, $type);
        // 读取文件
        readfile($file);
        exit;
    }
}
