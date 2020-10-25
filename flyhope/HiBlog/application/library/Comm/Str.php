<?php
/**
 * 字符串操作类
 *
 * @package Helper
 * @author chengxuan <i@chengxuan.li>
 */
namespace Comm;
abstract class Str {

    /**
     * 截取导语
     * 
     * @param string $string 字符串
     * @param int    $width  截取宽度
     * @param string $dot    如果被截取，显示最后的内容
     * 
     * @return string
     */
    static public function truncateSummary($string, $width, $dot = '…') {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->loadHtml('<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><title>截取</title></head><body>' . $string . '</body></html>');
        $body = $dom->getElementsByTagName('body');
        $will_remove_nodes = self::_truncateSummaryDom($body->item(0)->childNodes, $width);

        foreach($will_remove_nodes as $node) {
            $node->parentNode->removeChild($node);
        }
        
        
        //生成数据
        $dom_result = $dom->saveHTML();
        preg_match('#<body>(.*)</body>#is', $dom_result, $result);
        $result = isset($result[1]) ? trim($result[1]) : $string;
        $will_remove_nodes && $result .= '...';
        return $result;
    }
    
    /**
     * 通过DOM截取导语
     * 
     * @param \DOMNodeList $nodes         要处理的DOM节点
     * @param number       $limit_width   限制取多少宽度
     * @param number       $content_width 当前计算到达到多少宽度
     * 
     * @return array                      要删除的DOM节点
     */
    static protected function _truncateSummaryDom(\DOMNodeList $nodes, $limit_width, & $content_width = 0) {
        $will_remove_nodes = array();
        
        foreach($nodes as $node) {
            
            //超长移除节点
            if($content_width > $limit_width) {
                $will_remove_nodes[] = $node;
            }
            
            if($node instanceof \DOMText) {
                //到达文本节点，计算字数
                $content_width += mb_strwidth($node->textContent);
            } elseif($node->hasChildNodes()) {
                //非文本节点，继续遍历
                $will_remove_nodes = array_merge($will_remove_nodes, self::_truncateSummaryDom($node->childNodes, $limit_width, $content_width));
            }
        }
        
        return $will_remove_nodes;
    }
    
}
