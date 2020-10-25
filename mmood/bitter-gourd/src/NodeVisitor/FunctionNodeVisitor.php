<?php

namespace BitterGourd\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

class FunctionNodeVisitor extends NodeVisitorAbstract
{

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Expr\FuncCall && $node->getAttribute('converted') != true) {
            $node->setAttribute('parent', null);

            if ($node->name instanceof Node\Name && method_exists($this, 'f_' . $node->name->parts[0])) {
                return $this->{'f_' . $node->name->parts[0]}($node);
            }

            return $node;
        }
    }

    private function f_array_merge(Node\Expr\FuncCall $node)
    {
        $code = <<<EOF
            <?php
            call_user_func(function () {
                \$v = func_get_args();
                \$a = [];
                foreach (\$v as \$vi) {
                    foreach (\$vi as \$k => \$i) {
                        if (is_string(\$k)) {
                            \$a[\$k] = \$i;
                        } else {
                            \$a[] = \$i;
                        }
                    }
                }
                return \$a;
            }, null, null);
EOF;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $ast = $parser->parse(trim($code));

        /** @var Node\Stmt\Expression $newNode */
        $newNode = $ast[0];

        $nodeArgs = $node->args;

        $newNode->expr->args = array_merge([$newNode->expr->args[0]], $nodeArgs);

        $newNode->expr->setAttribute('converted', true);

        return $newNode->expr;
    }

    private function f_count(Node\Expr\FuncCall $node)
    {
        $code = <<<EOF
            <?php
            call_user_func(function(\$v,\$mode=0){
                \$s = 0;
                foreach (\$v as \$i) {
                    \$s++;
                }
                return \$s;
            },null);
EOF;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $ast = $parser->parse(trim($code));

        /** @var Node\Stmt\Expression $newNode */
        $newNode = $ast[0];

        $newNode->expr->args[1] = $node->args[0];

        $newNode->expr->setAttribute('converted', true);

        return $newNode->expr;
    }

    private function f_array_push(Node\Expr\FuncCall $node)
    {
        $var = array_shift($node->args);
        $args = $node->args;
        $varName = $var->value->name;

        if (!is_string($varName)) {
            return null;
        }

        $code = <<<EOF
            <?php
                call_user_func(function () use (&\$a) {
                    \$b =& \$$varName;
                    \$v = func_get_args();
                    foreach (\$v as \$i) {
                        \$b[] = \$i;
                    }
                    return count(\$b);
                }, 1, 2);
EOF;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $ast = $parser->parse(trim($code));

        /** @var Node\Stmt\Expression $newNode */
        $newNode = $ast[0];

        $newNode->expr->args = array_merge([$newNode->expr->args[0]], $args);
        $newNode->expr->args[0]->value->uses[0]->var = $var;

        $newNode->expr->setAttribute('converted', true);


        return $newNode->expr;
    }

    private function f_trim(Node\Expr\FuncCall $node)
    {

        $args = $node->args;

        $code = <<<EOF
            <?php
                call_user_func(function (\$str, \$charlist = " \t\n\r\0\x0B") {
                    \$a = str_split(\$charlist);
                    \$b = \$str;
                    for (\$i = 0; \$i < mb_strlen(\$str); \$i++) {
                        \$s = mb_substr(\$str, \$i, 1);
                        if (array_search(\$s, \$a) === false) {
                            break;
                        } else {
                            \$b = mb_substr(\$b, 1);
                        }
                    }
                    \$b = strrev(\$b);
                    \$str = \$b;
                    for (\$i = 0; \$i < mb_strlen(\$str); \$i++) {
                        \$s = mb_substr(\$str, \$i, 1);
                        if (array_search(\$s, \$a) === false) {
                            break;
                        } else {
                            \$b = mb_substr(\$b, 1);
                        }
                    }
                    \$b = strrev(\$b);
                    return \$b;
                }, \$a);
EOF;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $ast = $parser->parse(trim($code));

        /** @var Node\Stmt\Expression $newNode */
        $newNode = $ast[0];

        $newNode->expr->args = array_merge([$newNode->expr->args[0]], $args);

        $newNode->expr->setAttribute('converted', true);

        return $newNode->expr;
    }

    private function f_strlen(Node\Expr\FuncCall $node)
    {

        $args = $node->args;

        $code = <<<EOF
            <?php
                call_user_func(function (\$v) {
                    \$s = 0;
                    while (true) {
                        if (mb_substr(\$v, \$s, 1) == '') {
                            break;
                        } else {
                            \$s++;
                        }
                    }
                    return \$s;
                }, '');
EOF;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $ast = $parser->parse(trim($code));

        /** @var Node\Stmt\Expression $newNode */
        $newNode = $ast[0];

        $newNode->expr->args = array_merge([$newNode->expr->args[0]], $args);

        $newNode->expr->setAttribute('converted', true);

        return $newNode->expr;
    }

    private function f_mb_strlen(Node\Expr\FuncCall $node)
    {
        return $this->f_strlen($node);
    }

    private function f_str_shuffle(Node\Expr\FuncCall $node)
    {
        $args = $node->args;

        $code = <<<EOF
            <?php
            call_user_func(function (\$v) {
                \$arr = [];
                for (\$i = 0; \$i < mb_strlen(\$v); \$i++) {
                    \$arr[] = mb_substr(\$v, \$i, 1);
                }
                shuffle(\$arr);
                return implode('', \$arr);
            }, '');
EOF;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $ast = $parser->parse(trim($code));

        /** @var Node\Stmt\Expression $newNode */
        $newNode = $ast[0];

        $newNode->expr->args = array_merge([$newNode->expr->args[0]], $args);

        $newNode->expr->setAttribute('converted', true);

        return $newNode->expr;
    }

}
