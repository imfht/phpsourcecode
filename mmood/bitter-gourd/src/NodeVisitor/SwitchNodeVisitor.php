<?php

namespace BitterGourd\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

class SwitchNodeVisitor extends NodeVisitorAbstract
{

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Switch_) {

            if ($node->getAttribute('switch_converted')) {
                return null;
            }

            $isDefaultVarName = '$v' . md5(time());
            $code = <<<EOF
            <?php
            $isDefaultVarName=false;
            if(1==2)
            {
                $isDefaultVarName=true;
            }
EOF;
            $defaultCode = <<<EOF
            <?php
            if($isDefaultVarName==false){
                $isDefaultVarName=true;
            }
EOF;

            $cond = $node->cond;
            $newAst = [];

            /** @var Node\Stmt\Case_ $case */
            foreach ($node->cases as $case) {
                $caseCond = $case->cond;
                $caseStmts = call_user_func(function ($arr) {
                    $r = [];
                    foreach ($arr as $node) {
                        if (($node instanceof Node\Stmt\Break_) == false) {
                            array_push($r, $node);
                        }
                    }
                    return $r;
                }, $case->stmts);
                if ($caseCond != null) {
                    /** @var Node\Stmt\If_ $newIfNode */
                    $ifParser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
                    $ifAst = $ifParser->parse(trim($code));
                    /** @var Node\Stmt\If_ $newIfNode */
                    $newIfNode = $ifAst[1];
                    $newIfNode->cond->left = $cond;
                    $newIfNode->cond->right = clone($caseCond);
                } else {
                    $defaultParser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
                    $defaultAst = $defaultParser->parse(trim($defaultCode));
                    /** @var Node\Stmt\If_ $newIfNode */
                    $newIfNode = $defaultAst[0];

                    $newIfNode->setAttribute('if_converted', true);
                }
                $newIfNode->stmts = array_merge($caseStmts, $newIfNode->stmts);

                array_push($newAst, $newIfNode);
            }

            return $newAst;
        }
    }
}