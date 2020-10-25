<?php
namespace Modules\Entity\Library;

class Toc
{
    public static $toc = array();
    protected static $tocIndex = 0;

    //文章目录
    public static function toc($body)
    {
        self::$toc = array();
        $body = (string)$body;
        $body = preg_replace_callback('/<h([2-4]{1})>(.*?)<\/h[2-4]{1}>/i', 'self::tocReplace', $body);
        return array(
            'body' => $body,
            'toc' => self::$toc
        );
    }

    public static function tocReplace($matches)
    {
        self::$tocIndex++;
        $matches[1] = (int)$matches[1];
        self::$toc[] = array(
            'level' => $matches[1],
            'title' => $matches[2],
            'tocIndex' => self::$tocIndex
        );
        return '<h' . $matches[1] . '>' . $matches[2] . '<a id="toc' . self::$tocIndex . '"><i class="glyphicon glyphicon-paperclip"></i></a></h' . $matches[1] . '>';
    }
}