<?php

/**
 * 模板引擎调试
 * @author 暮雨秋晨
 * @copyright 2014
 */

class TemplateCheck
{
    private static $file = ''; //模板

    public function setFile($file)
    {
        self::$file = $file;
    }

    /**
     * 语法检查
     */
    public function syntax_check($res)
    {
        $res = explode("\n", $res);
        $res = array_filter($res);
        if (empty($res)) {
            throw new TemplateException('This template file is empty.', 2, 0, self::$file);
        }
        $this->chk_from($res);
        $this->chk_if($res);
        $this->chk_loop($res);
        $this->chk_switch($res);
    }


    private function chk_loop($res)
    {
        $foreach = 0;
        $endforeach = 0;
        $line = 0;
        foreach ($res as $key => $src) {
            if (preg_match('!\{loop\s+(.+)\}!Ui', $src)) {
                $foreach++;
                $line = $key;
            }
            if (preg_match('!\{\/loop\}!Ui', $src)) {
                $endforeach++;
                $line = $key;
            }
        }
        if ($foreach != $endforeach) {
            throw new TemplateException('Grammar <b>loop</b> no normally closed.', 2, $line,
                self::$file);
        }
    }

    private function chk_switch($res)
    {
        $switch = 0;
        $endswitch = 0;
        $line = 0;
        foreach ($res as $key => $src) {
            if (preg_match('!\{switch\s+(.+)\}!Ui', $src)) {
                $switch++;
                $line = $key;
            }
            if (preg_match('!\{\/switch\}!Ui', $src)) {
                $endswitch++;
                $line = $key;
            }
            $line = $key;
        }
        if ($switch != $endswitch) {
            throw new TemplateException('Grammar <b>switch</b> no normally closed.', 2, $line,
                self::$file);
        }
    }

    private function chk_from($res)
    {
        $from = 0;
        $endfrom = 0;
        $line = 0;
        foreach ($res as $key => $src) {
            if (preg_match('!\{from\s+(.+)\s+to\s+(.+)\s+[\-|\+][\d|\+|\-]\}!Ui', $src)) {
                $from++;
                $line = $key;
            }
            if (preg_match('!\{\/from\}!Ui', $src)) {
                $endfrom++;
                $line = $key;
            }
        }
        if ($from != $endfrom) {
            throw new TemplateException('Grammar <b>from</b> no normally closed.', 2, $line,
                self::$file);
        }
    }

    private function chk_if($res)
    {
        $if = 0;
        $endif = 0;
        $line = 0;
        foreach ($res as $key => $src) {
            if (preg_match('!\{if\(.*\)\}!Ui', $src)) {
                $if++;
                $line = $key;
            }
            if (preg_match('!\{\/if\}!Ui', $src)) {
                $endif++;
                $line = $key;
            }
        }
        if ($if != $endif) {
            throw new TemplateException('Grammar <b>if</b> no normally closed.', 2, $line,
                self::$file);
        }
    }

}

?>