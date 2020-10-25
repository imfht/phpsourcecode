<?php
$header = <<<'EOF'
    代码格式化命令
    php-cs-fixer fix ./web/source/utility/resource.ctrl.php
EOF;
$config = PhpCsFixer\Config::create()
    ->setIndent("\t")
    ->setLineEnding("\n")
    ->setRules([
            '@Symfony' => true,
            'braces'=> ['position_after_functions_and_oop_constructs' => 'same']
        ])
;
return $config;