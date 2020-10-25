<?php
namespace Modules\Node\Library;

use Modules\Node\Entity\Node as NodeModel;

class Common
{
    public static function idToTitle($id){
        $node = NodeModel::findFirst($id);
        if($node && $node->title){
            return $node->title->value;
        }
        return '';
    }
    // 渲染一个节点
    public static function node($node)
    {
        if ((is_string($node) || is_int($node)) && (int) $node == intval($node)) {
            $node = intval($node);
            $node = NodeModel::findFirst($node);
            if (!$node) {
                return '';
            }
        }
        return array(
            '#templates' => array('node', 'node-' . $node->type),
            'node' => $node,
        );
    }
}
