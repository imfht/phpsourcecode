<?php

namespace BitterGourd\NodeVisitor;

use BitterGourd\Common;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

class IfNodeVisitor extends NodeVisitorAbstract
{

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\If_ && count($node->elseifs) <= 0 && !(isset($node->getAttributes()['parent']) && $node->getAttributes()['parent'] instanceof Node\Stmt\Else_)) {

            if ($node->getAttribute('if_converted')) {
                return null;
            }

            $endName = Common::generateVarName();

            $code = <<<EOF
            <?php
            switch (\$cond)
            {
            case false:
                goto $endName;
            case true:
                goto $endName;
            default:
                goto $endName;
            }
            $endName:
EOF;

            $switchParser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
            $switchAst = $switchParser->parse(trim($code));
            /** @var Node\Stmt\Switch_ $switchNode */
            $switchNode = $switchAst[0];

            $cond = $node->cond;
            $stmts = $node->stmts;
            if ($node->else != null) {
                $elseStmts = $node->else->stmts;
                $switchNode->cases[0]->stmts = array_merge($elseStmts, $switchNode->cases[0]->stmts);
                $switchNode->cases[2]->stmts = array_merge($elseStmts, $switchNode->cases[2]->stmts);
            }
            $switchNode->cond = $cond;
            $switchNode->cases[1]->stmts = array_merge($stmts, $switchNode->cases[1]->stmts);

            $switchNode->setAttribute('switch_converted', true);

            return [$switchNode, $switchAst[1]];
        }
    }
}
