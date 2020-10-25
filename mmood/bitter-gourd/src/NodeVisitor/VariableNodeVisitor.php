<?php

namespace BitterGourd\NodeVisitor;

use BitterGourd\Common;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class VariableNodeVisitor extends NodeVisitorAbstract
{

    public function leaveNode(Node $node)
    {

        if ($node instanceof Node\Expr\Variable && $node->getAttribute('converted') != true) {

            $parentNode = $node->getAttribute('parent');

            if ($parentNode instanceof Node\Stmt\Foreach_) {
                return null;
            }

            if ($parentNode instanceof Node\Param) {
                return null;
            }

            if ($parentNode instanceof Node\Stmt\Global_) {
                return null;
            }

            /*            if ($parentNode instanceof Node\Expr\MethodCall) {
                            return null;
                        }*/

            if ($parentNode instanceof Node\Expr\PropertyFetch) {
                return null;
            }

            if (!is_string($node->name)) {
                return null;
            }

            $newNode = Common::stringNToFuncN($node->name);

            if ($newNode != null) {
                $newNode->setAttribute('converted', true);
                $node->setAttribute('converted', true);
                $node->name = $newNode;
            }

            return $node;
        }
    }

}
