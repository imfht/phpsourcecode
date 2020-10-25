<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-04-27
 * Time: 14:28
 */

namespace inhere\gearman;

/**
 * Class Helper
 * @package inhere\gearman
 */
class Helper
{
    /**
     * @return bool
     */
    public static function isMac()
    {
        return stripos(PHP_OS, 'Darwin') !== false;
    }

    /**
     * @return string
     */
    public static function formatMicroTime($mTime)
    {
        list($ts, $ms) = explode('.', sprintf('%.4f', $mTime));

        return date('Y/m/d H:i:s', $ts) . '.' . $ms;
    }

    /**
     * Parses $GLOBALS['argv'] for parameters and assigns them to an array.
     *
     * Supports:
     * -e
     * -e <value>
     * --long-param
     * --long-param=<value>
     * --long-param <value>
     * <value>
     *
     * @link http://php.net/manual/zh/function.getopt.php#83414
     * @param array $noOpts List of parameters without values
     * @return array
     */
    public static function parseOptArgs($noOpts = [])
    {
        $result = [];
        $params = $GLOBALS['argv'];
        reset($params);

        while (list(, $p) = each($params)) {
            if ($p{0} === '-') {
                $pName = substr($p, 1);
                $value = true;

                if ($pName{0} === '-') {
                    // long-opt (--<param>)
                    $pName = substr($pName, 1);

                    if (strpos($p, '=') !== false) {
                        // value specified inline (--<param>=<value>)
                        list($pName, $value) = explode('=', substr($p, 2), 2);
                    }
                }

                // check if next parameter is a descriptor or a value
                $nxParam = current($params);

                if (!in_array($pName, $noOpts) && $value === true && $nxParam !== false && $nxParam{0} !== '-') {
                    list(, $value) = each($params);
                }

                $result[$pName] = $value;
            } else {
                // param doesn't belong to any option
                $result[] = $p;
            }
        }

        return $result;
    }

    /**
     * like print_r
     */
    public static function printR()
    {
        $args = func_get_args();

        ob_start();

        foreach ($args as $arg) {
            print_r($arg);
        }

        $string = ob_get_clean();

        return preg_replace("/Array\n\s+\(/", 'Array (', $string);
    }

    /**
     * some styles
     * @var array
     */
    public static $styles = [
        'light_red' => '1;31',
        'light_green' => '1;32',
        'yellow' => '1;33',
        'light_blue' => '1;34',
        'magenta' => '1;35',
        'light_cyan' => '1;36',
        'white' => '1;37',
        'normal' => '0',
        'black' => '0;30',
        'red' => '0;31',
        'green' => '0;32',
        'brown' => '0;33',
        'blue' => '0;34',
        'cyan' => '0;36',
        'bold' => '1',
        'underscore' => '4',
        'reverse' => '7',
    ];

    /**
     * @param $text
     * @param string $style
     * @return bool|string
     */
    public static function color($text, $style = '0')
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            $result = $text;
        } else {
            if (!is_numeric($style{0})) {
                $style = isset(self::$styles[$style]) ? self::$styles[$style] : '0';
            }

//            $result = chr(27). "$out$text" . chr(27) . chr(27) . "[0m". chr(27);
            $result = "\033[{$style}m{$text}\033[0m";
        }

        return $result;
    }

    public static function clearColor($text)
    {
        // return preg_replace('/\033\[(?:\d;?)+m/', '' , "\033[0;36mtext\033[0m");
        return preg_replace('/\033\[(?:\d;?)+m/', '', $text);
    }

    /**
     * @return bool
     */
    public static function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    public static function get($name, $default = null)
    {
        return isset($_GET[$name]) ? $_GET[$name]: $default;
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    public static function getServerValue($name, $default = null)
    {
        return isset($_SERVER[$name]) ? $_SERVER[$name]: $default;
    }

    /**
     * @param string $view
     * @param array $data
     * @param bool $quit
     */
    public static function render($view, array $data = [], $quit = true)
    {
        if ($data) {
            extract($data);
        }

        require $view;

        if ($quit) {
            exit;
        }
    }

    /**
     * @param array $data
     * @param int $code
     * @param string $msg
     */
    public static function outJson(array $data = [], $code = 0, $msg = 'successful')
    {
        if (!headers_sent()) {
            header("Content-type: application/json;charset=utf-8");
        }

        exit(json_encode([
            'code' => (int)$code,
            'msg' => $msg ?: 'successful',
            'data' => $data,
        ]));
    }
}
