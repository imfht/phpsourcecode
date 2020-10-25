<?php

namespace BitterGourd\NodeVisitor;

use BitterGourd\Common;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class PropertyFetchNodeVisitor extends NodeVisitorAbstract
{

    public function leaveNode(Node $node)
    {

        if ($node instanceof Node\Expr\PropertyFetch && $node->getAttribute('converted') != true) {

            if (!is_string($node->name->name)) {
                return null;
            }

            $newNode = Common::stringNToFuncN($node->name->name);

            if ($newNode != null) {
                $newNode->setAttribute('converted', true);
                $node->setAttribute('converted', true);

                $node->name = $newNode;
                return $node;
            }
        }
        return null;
    }
}