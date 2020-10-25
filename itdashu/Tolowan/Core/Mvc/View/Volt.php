<?php
namespace Core\Mvc\View;

use Phalcon\Mvc\View\Engine\Volt as PhalconVolt;
use Core\Mvc\View\Volt\CoreExtensions;
use Core\Config;


class Volt extends PhalconVolt{

    public function __construct($view, $dependencyInjector)
    {
        parent::__construct($view, $dependencyInjector);
        $this->setOptions([
            'compiledPath' => ROOT_DIR.'Web/' . WEB_CODE . '/cache/volt/',
        ]);
        $viewstags = Config::cache('viewTags');
        $compiler = $this->getCompiler();
        $compiler->addExtension(new CoreExtensions());
        if ($viewstags) {
            foreach ($viewstags as $tagName => $tagInfo) {
                if ($tagInfo['type'] == 'extension') {
                    $compiler->addExtension(new $tagInfo['function']());
                } elseif ($tagInfo['type'] == 'function') {
                    $compiler->addFunction($tagName, function ($resolvedArgs, $exprArgs) use ($tagInfo) {
                        return $tagInfo['fun'] . '(' . $resolvedArgs . ')';
                    });
                } elseif ($tagInfo['type'] == 'anonymous_filter') {
                    $compiler->addFunction($tagName, function ($resolvedArgs, $exprArgs) use ($tagInfo) {
                        return $tagInfo['function']($resolvedArgs, $exprArgs);
                    });
                } elseif ($tagInfo['type'] == 'filter') {
                    $compiler->addFilter($tagName, $tagInfo['function']);
                }
            }
        }
    }
}