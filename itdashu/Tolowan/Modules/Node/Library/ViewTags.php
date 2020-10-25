<?php
namespace Modules\Node\Library;
use Core\Config;

class ViewTags
{
    public static $tocParams = array(
        'index' => 0,
        'upTag' => null,
        'upTagId' => null,
        'toc' => array(),
        'tocCache' => array(),
        'tocHtml' => array(),
    );
    //文章目录
    public static function toc($node, $key = 'body')
    {
        if (!isset($node->{$key})) {
            return $node;
        }
        if (!is_string($node->{$key})) {
            return $node;
        }
        $node->{$key} = preg_replace_callback('/<h([2-4]{1})>(.*?)<\/h[2-4]{1}>/i', 'self::tocReplace', $node->{$key});
        $node->toc = array(
            '#templates' => array('toc', 'toc-' . $node->node_id),
            'data' => self::$tocParams,
            'toc' => self::$tocParams['toc'],
            'tocHtml' => self::$tocParams['tocHtml'],
        );
        self::$tocParams = null;
        return $node;
    }

    public static function tocReplace($matches)
    {
        $matches[1] = (int)$matches[1];
        self::$tocParams['index']++;
        if (self::$tocParams['upTagId'] == null) {
            self::$tocParams['upTagId'] = self::$tocParams['index'];
        }
        if (self::$tocParams['upTag'] == null) {
            self::$tocParams['upTag'] = $matches[1];
        }
        self::$tocParams['tocCache'][self::$tocParams['index']] = array();
        if (self::$tocParams['upTag'] < $matches[1]) {
            if (!isset(self::$tocParams['tocCache'][self::$tocParams['upTagId']]['children'])) {
                self::$tocParams['tocCache'][self::$tocParams['upTagId']]['children'] = array();
            }
            self::$tocParams['tocCache'][self::$tocParams['upTagId']]['children'][self::$tocParams['index']] = &self::$tocParams['tocCache'][self::$tocParams['index']];
        } else {
            self::$tocParams['toc'][self::$tocParams['index']] = &self::$tocParams['tocCache'][self::$tocParams['index']];
        }
        self::$tocParams['upTagId'] = self::$tocParams['index'];
        self::$tocParams['upTag'] = $matches[1];
        self::$tocParams['tocHtml'][self::$tocParams['index']] = '<a href="#toc' . self::$tocParams['index'] . '">' . $matches[2] . '</a>';
        return '<h' . $matches[1] . '>' . $matches[2] . '<a id="toc' . self::$tocParams['index'] . '"><i class="icon-filter"></i></a></h' . $matches[1] . '>';
    }
}