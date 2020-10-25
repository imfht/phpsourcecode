<?php
namespace Modules\Node\Library;

use Core\Config;

class Toc
{
    public static $toc = array();
    protected static $tocIndex = 0;

    //文章目录
    public static function toc($node, $key = 'body')
    {
        if (!isset($node->{$key})) {
            return $node;
        }
        $node->{$key} = (string)$node->{$key};
        $node->{$key} = preg_replace_callback('/<h([2-4]{1})>(.*?)<\/h[2-4]{1}>/i', 'self::tocReplace', $node->{$key});
        $node->contentToc = array(
            '#templates' => array('toc', 'toc-' . $node->id),
            'toc' => self::$toc,
        );
        return $node;
    }

    public static function tocReplace($matches)
    {
        self::$tocIndex++;
        $matches[1] = (int)$matches[1];
        self::$toc[self::$tocIndex] = '<a class="margin-'.$matches[1].'" href="#toc' . self::$tocIndex . '">' . $matches[2] . '</a>';
        return '<h' . $matches[1] . '>' . $matches[2] . '<a id="toc' . self::$tocIndex . '"><i class="icon-filter"></i></a></h' . $matches[1] . '>';
    }
}