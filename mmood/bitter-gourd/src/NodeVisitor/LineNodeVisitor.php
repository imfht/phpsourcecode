<?php

namespace BitterGourd\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

class LineNodeVisitor extends NodeVisitorAbstract
{

    public function leaveNode(Node $node)
    {

        if (($node instanceof Node\Stmt\Expression
                || $node instanceof Node\Stmt\If_
                || $node instanceof Node\Stmt\Switch_
                || $node instanceof Node\Stmt\For_
                || $node instanceof Node\Stmt\Foreach_) && $node->getAttribute('eval') != true) {

            $code = <<<EOF
            <?php
            eval(gzuncompress('a'));
EOF;
            $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
            $ast = $parser->parse(trim($code));
            /** @var Node\Stmt\Expression $newNode */
            $newNode = $ast[0];

            $prettyPrinter = new \PhpParser\PrettyPrinter\Standard();
            $nodeCode = $prettyPrinter->prettyPrint([$node]);

            $newNode->expr->expr->args[0]->value->value = gzcompress($nodeCode);

            $newNode->setAttribute('converted', true);
            $newNode->setAttribute('eval', true);

            return $newNode;
        }
    }
}