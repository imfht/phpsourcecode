<?php

namespace BitterGourd\NodeVisitor;

use BitterGourd\Common;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class StringNodeVisitor extends NodeVisitorAbstract
{
    private $stack;

    public function beforeTraverse(array $nodes)
    {
        $this->stack = [];
    }

    public function enterNode(Node $node)
    {

        if (!empty($this->stack)) {
            $node->setAttribute('parent', $this->stack[count($this->stack) - 1]);
        }
        $this->stack[] = $node;
    }

    public function leaveNode(Node $node)
    {

        array_pop($this->stack);

        $parentNode = $node->getAttribute('parent');

        if ($parentNode instanceof Node\Const_) {
            return null;
        }
        if ($parentNode instanceof Node\Stmt\PropertyProperty) {
            return null;
        }

        if ($parentNode instanceof Node\Expr\ArrayItem && $parentNode->getAttribute('parent')->getAttribute('parent') instanceof Node\Stmt\PropertyProperty) {
            return null;
        }

        if ($node instanceof Node\Scalar\String_ && $node->getAttribute('converted') != true) {

            if ($parentNode instanceof Node\Param && $parentNode->getAttribute('parent') instanceof Node\Stmt\Function_) {
                return null;
            }

            if ($parentNode instanceof Node\Expr\ArrayItem && $parentNode->key instanceof Node\Scalar\String_ && $parentNode->key->value == $node->value) {
                return null;
            }

            if ($node->getAttribute('kind') != 1) {
                return null;
            }

            if (!is_string($node->value)) {
                return null;
            }

            $newNode = Common::stringNToFuncN($node->value);

            if ($newNode != null) {
                $newNode->setAttribute('converted', true);
                return $newNode;
            }
        }
        return null;
    }
}
