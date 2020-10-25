<?php

namespace BitterGourd\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

class ForNodeVisitor extends NodeVisitorAbstract
{

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\For_) {

            if ($node->getAttribute('for_converted')) {
                return null;
            }

            if (count($node->init) <= 0) {
                return null;
            }

            $code = <<<EOF
            <?php
            \$x = 0;
            while(true){
                if (1==1) {
                } else {
                    break;
                }                
                \$x++;
            }
EOF;
            $whileParser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
            $whileAst = $whileParser->parse(trim($code));
            $node->setAttribute('parent', null);

            $whileAst[0]->expr = $node->init[0];
            if (isset($node->cond[0])) {
                $whileAst[1]->stmts[0]->cond = $node->cond[0];
            }
            if (isset($node->loop[0])) {
                $whileAst[1]->stmts[1]->expr = $node->loop[0];
            }
            $whileAst[1]->stmts = array_merge([$whileAst[1]->stmts[0]], $node->stmts, [$whileAst[1]->stmts[1]]);

            $whileAst[1]->setAttribute('while_converted', true);

            return $whileAst;
        }
    }
}
