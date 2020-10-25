<?php
namespace PhaSvc\Base;

use JakubOnderka\PhpConsoleColor\ConsoleColor;

/**
 * Base task
 */
class WebSocketBase extends \Phalcon\Cli\Task
{
    const APP_SERVICE_NAME = "PhaServiceWebSocket";

    public $consoleColor;

    public static $fd;
    public        $params;

    /**
     * initialize
     */
    public function initialize()
    {
        $this->consoleColor = new ConsoleColor();
    }//end


    public function parseArguments($arguments = NULL)
    {
        $v = $arguments ?? $this->router->getParams();
        if (!isset($v['fd']) || !isset($v['data'])) {
            throw new Exception('BAD PARAMS');
        }
        self::$fd     = $v['fd'];
        $this->params = $v['data'];
    }//end


    /**
     * Console Color Print
     *
     * @DoNotCover
     *
     * @param string $text
     * @param string $styles
     * @param bool   $newLine
     */
    public function cout(string $text, string $styles = 'f255', $newLine = FALSE)
    {
        $_style = [];
        if ('f255' == $styles) {
            $_style[] = 'color_255';
        } else {
            $styleAr = explode(',', $styles);
            foreach ($styleAr as $style) {
                $style = trim($style);
                if ($style{0} == 'f' && is_numeric($style{1})) {
                    $_style[] = 'color_' . substr($style, 1);
                } elseif ($style{0} == 'b' && is_numeric($style{1})) {
                    $_style[] = 'bg_color_' . substr($style, 1);
                } else {
                    $_style[] = $style;
                }
            }
        }
        echo $this->consoleColor->apply($_style, $text);
        if (TRUE == $newLine) echo PHP_EOL;
    }//end


    /**
     * 传送消息到客户端
     *
     * @param mixed $data
     */
    public function send($data)
    {
        $trace  = debug_backtrace(0, 3);
        $class  = $trace[1]['class'] ?? 'nil';
        $func   = $trace[1]['function'] ?? 'nil';
        $action = strtolower(str_replace('Task', '', $class)
            . '.' . str_replace('Action', '', $func));

        if (is_string($data) || is_array($data) || is_int($data)) {
            $ret = ['cmd' => $action, 'ret' => $data];
        } elseif (is_object($data)) {
            $ret      = new stdClass();
            $ret->cmd = $action;
            $ret->ret = $data;
        }else{
            $ret = ['cmd' => $action, 'ret' => $data];
        }
        $this->ws->push(self::$fd, json_encode($ret));
    }//end

}//end
