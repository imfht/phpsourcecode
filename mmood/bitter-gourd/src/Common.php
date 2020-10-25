<?php


namespace BitterGourd;

use PhpParser\ParserFactory;
use PhpParser\Node;

class Common
{

    /**
     * @param $string
     * @return Node|null
     */
    public static function stringNToFuncN($string)
    {
        $stringArr = [];
        for ($i = 0; $i <= mb_strlen($string) - 1; $i++) {
            array_push($stringArr, mb_substr($string, $i, 1));
        }

        if (count($stringArr) <= 1) {
            return null;
        }

        $string2Arr = array_unique($stringArr);
        shuffle($string2Arr);

        $c = implode('.', array_map(function ($v) use ($string2Arr) {
            $index = array_search($v, $string2Arr);
            return '$b[' . $index . ']';
        }, $stringArr));

        $string2ArrNode = new Node\Expr\Array_([]);
        foreach ($string2Arr as $s) {
            array_push($string2ArrNode->items, new Node\Expr\ArrayItem(new Node\Scalar\String_($s)));
        }

        $code = <<<EOF
            <?php
            call_user_func(function () {
                \$b=1;
                return $c;
            });
EOF;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $ast = $parser->parse(trim($code));
        /** @var Node\Stmt\Expression $newNode */

        $node = $ast[0];

        $node->expr->args[0]->value->stmts[0]->expr->expr = $string2ArrNode;

        return $node->expr;
    }

    static public function generateVarName()
    {
        return sprintf('var_%s', md5(uniqid()));
    }

}
