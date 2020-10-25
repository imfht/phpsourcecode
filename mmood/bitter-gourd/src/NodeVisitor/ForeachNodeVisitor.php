<?php

namespace BitterGourd\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

class ForeachNodeVisitor extends NodeVisitorAbstract
{

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Foreach_) {

            if ($node->getAttribute('foreach_converted')) {
                return null;
            }

            if (!$node->keyVar instanceof Node\Expr\Variable) {
                return null;
            }

            if (!$node->valueVar instanceof Node\Expr\Variable) {
                return null;
            }

            //iterator_to_array()
            $expr = $node->expr;
            $stmts = $node->stmts;

            $arrKeysVarName = '$v' . mt_rand(0, 99999);
            $arrKeysNumVarName = '$v' . mt_rand(0, 99999);
            $arrKeysKeyVarName = $node->keyVar == null ? '$v' . mt_rand(0, 99999) : '$' . $node->keyVar->name;
            $arrItemVarName = '$' . $node->valueVar->name;
            $s = sprintf('%s[%s]', $arrKeysVarName, $arrKeysNumVarName);
            $s2 = sprintf('%s[%s]', '$x', $arrKeysKeyVarName);

            $code = <<<EOF
            <?php
            $arrKeysVarName = array_keys(1);
            for ($arrKeysNumVarName = 0; $arrKeysNumVarName < count(1); $arrKeysNumVarName++) {
                $arrKeysKeyVarName = $s;
                $arrItemVarName = $s2;
            }
EOF;

            $forParser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
            $forAst = $forParser->parse(trim($code));

            $forAst[0]->expr->expr->args[0]->value = $expr;
            $forAst[1]->cond[0]->right->args[0]->value = $expr;
            $forAst[1]->stmts[1]->expr->expr->var = $expr;

            $forAst[1]->stmts = array_merge($forAst[1]->stmts, $stmts);

            $forAst[1]->setAttribute('for_converted', true);

            return $forAst;
        }
    }
}