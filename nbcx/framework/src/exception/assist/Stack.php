<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\exception\assist;

/**
 * Stack
 *
 * @package nb\exception\assist
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2018/4/27
 */
class Stack {
    /**
     * @var int maximum number of source code lines to be displayed. Defaults to 19.
     */
    public $maxSourceLines = 10;
    /**
     * @var int maximum number of trace source code lines to be displayed. Defaults to 13.
     */
    public $maxTraceSourceLines = 13;

    public $traceLine = '{html}';

    public $exception = null;

    public function __construct($exception) {
        $this->exception = $exception;
    }

    /**
     * Renders call stack.
     * @param \Exception|\ParseError $exception exception to get call stack from
     * @return string HTML content of the rendered call stack.
     * @since 2.0.12
     */
    public function renderCallStack() {
        $exception = $this->exception;
        //$out = '<ul>';
        echo '<ul>';
        //$out .=
        $this->renderCallStackItem($exception->getFile(), $exception->getLine(), null, null, [], 1);
        //echo $out;
        for ($i = 0, $trace = $exception->getTrace(), $length = count($trace); $i < $length; ++$i) {
            $file = !empty($trace[$i]['file']) ? $trace[$i]['file'] : null;
            $line = !empty($trace[$i]['line']) ? $trace[$i]['line'] : null;
            $class = !empty($trace[$i]['class']) ? $trace[$i]['class'] : null;
            $function = null;
            if (!empty($trace[$i]['function']) && $trace[$i]['function'] !== 'unknown') {
                $function = $trace[$i]['function'];
            }
            $args = !empty($trace[$i]['args']) ? $trace[$i]['args'] : [];
            //$out .=
            $this->renderCallStackItem($file, $line, $class, $function, $args, $i + 2);
        }
        //
        //$out .= '</ul>';
        echo '</ul>';
        //return $out;
    }

    /**
     * Renders a single call stack element.
     * @param string|null $file name where call has happened.
     * @param int|null $line number on which call has happened.
     * @param string|null $class called class name.
     * @param string|null $method called function/method name.
     * @param array $args array of method arguments.
     * @param int $index number of the call stack element.
     * @return string HTML content of the rendered call stack element.
     */
    public function renderCallStackItem($file, $line, $class, $method, $args, $index) {
        $lines = [];
        $begin = $end = 0;
        if ($file !== null && $line !== null) {
            $line--; // adjust line number from one-based to zero-based
            $lines = @file($file);
            if ($line < 0 || $lines === false || ($lineCount = count($lines)) < $line) {
                return '';
            }

            $half = (int) (($index === 1 ? $this->maxSourceLines : $this->maxTraceSourceLines) / 2);
            $begin = $line - $half > 0 ? $line - $half : 0;
            $end = $line + $half < $lineCount ? $line + $half : $lineCount - 1;
        }
        return $this->renderFile('', [
            'file' => $file,
            'line' => $line,
            'class' => $class,
            'method' => $method,
            'index' => $index,
            'lines' => $lines,
            'begin' => $begin,
            'end' => $end,
            'args' => $args,
        ]);
    }

    /**
     * Renders a view file as a PHP script.
     * @param string $_file_ the view file.
     * @param array $_params_ the parameters (name-value pairs) that will be extracted and made available in the view file.
     * @return string the rendering result
     */
    public function renderFile($_file_, $_params_) {
        extract($_params_, EXTR_OVERWRITE);
        $handler = $this;
        include __DIR__.DS.'..'.DS.'html'.DS.'stack.tpl.php';
        return;
        $_params_['handler'] = $this;
        if ($this->exception instanceof ErrorException || !Yii::$app->has('view')) {
            ob_start();
            ob_implicit_flush(false);
            extract($_params_, EXTR_OVERWRITE);
            require Yii::getAlias($_file_);

            return ob_get_clean();
        }

        return Yii::$app->getView()->renderFile($_file_, $_params_, $this);
    }

    /**
     * Determines whether given name of the file belongs to the framework.
     * @param string $file name to be checked.
     * @return bool whether given name of the file belongs to the framework.
     */
    public function isCoreFile($file) {

        return true;
        return $file === null || strpos(realpath($file), YII2_PATH . DIRECTORY_SEPARATOR) === 0;
    }

    /**
     * Converts special characters to HTML entities.
     * @param string $text to encode.
     * @return string encoded original text.
     */
    public function htmlEncode($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Adds informational links to the given PHP type/class.
     * @param string $code type/class name to be linkified.
     * @return string linkified with HTML type/class name.
     */
    public function addTypeLinks($code) {
        if (preg_match('/(.*?)::([^(]+)/', $code, $matches)) {
            $class = $matches[1];
            $method = $matches[2];
            $text = $this->htmlEncode($class) . '::' . $this->htmlEncode($method);
        }
        else {
            $class = $code;
            $method = null;
            $text = $this->htmlEncode($class);
        }

        $url = null;

        $shouldGenerateLink = true;
        if ($method !== null && substr_compare($method, '{closure}', -9) !== 0) {
            $reflection = new \ReflectionClass($class);
            if ($reflection->hasMethod($method)) {
                $reflectionMethod = $reflection->getMethod($method);
                $shouldGenerateLink = $reflectionMethod->isPublic() || $reflectionMethod->isProtected();
            } else {
                $shouldGenerateLink = false;
            }
        }

        if ($shouldGenerateLink) {
            $url = $this->getTypeUrl($class, $method);
        }

        if ($url === null) {
            return $text;
        }

        return '<a href="' . $url . '" target="_blank">' . $text . '</a>';
    }

    /**
     * Returns the informational link URL for a given PHP type/class.
     * @param string $class the type or class name.
     * @param string|null $method the method name.
     * @return string|null the informational link URL.
     * @see addTypeLinks()
     */
    protected function getTypeUrl($class, $method) {
        if (strncmp($class, 'nb\\', 4) !== 0) {
            return null;
        }

        $page = $this->htmlEncode(strtolower(str_replace('\\', '-', $class)));
        $url = "http://www.yiiframework.com/doc-2.0/$page.html";
        if ($method) {
            $url .= "#$method()-detail";
        }

        return $url;
    }


    /**
     * Converts arguments array to its string representation.
     *
     * @param array $args arguments array to be converted
     * @return string string representation of the arguments array
     */
    public function argumentsToString($args) {
        $count = 0;
        $isAssoc = $args !== array_values($args);

        foreach ($args as $key => $value) {
            $count++;
            if ($count >= 5) {
                if ($count > 5) {
                    unset($args[$key]);
                } else {
                    $args[$key] = '...';
                }
                continue;
            }

            if (is_object($value)) {
                $args[$key] = '<span class="title">' . $this->htmlEncode(get_class($value)) . '</span>';
            }
            elseif (is_bool($value)) {
                $args[$key] = '<span class="keyword">' . ($value ? 'true' : 'false') . '</span>';
            }
            elseif (is_string($value)) {
                $fullValue = $this->htmlEncode($value);
                if (mb_strlen($value, 'UTF-8') > 32) {
                    $displayValue = $this->htmlEncode(mb_substr($value, 0, 32, 'UTF-8')) . '...';
                    $args[$key] = "<span class=\"string\" title=\"$fullValue\">'$displayValue'</span>";
                }
                else {
                    $args[$key] = "<span class=\"string\">'$fullValue'</span>";
                }
            }
            elseif (is_array($value)) {
                $args[$key] = '[' . $this->argumentsToString($value) . ']';
            }
            elseif ($value === null) {
                $args[$key] = '<span class="keyword">null</span>';
            }
            elseif (is_resource($value)) {
                $args[$key] = '<span class="keyword">resource</span>';
            }
            else {
                $args[$key] = '<span class="number">' . $value . '</span>';
            }

            if (is_string($key)) {
                $args[$key] = '<span class="string">\'' . $this->htmlEncode($key) . "'</span> => $args[$key]";
            }
            elseif ($isAssoc) {
                $args[$key] = "<span class=\"number\">$key</span> => $args[$key]";
            }
        }

        return implode(', ', $args);
    }

    /**
     * @return string the user-friendly name of this exception
     */
    public function getName() {
        $names = [
            E_COMPILE_ERROR => 'PHP Compile Error',
            E_COMPILE_WARNING => 'PHP Compile Warning',
            E_CORE_ERROR => 'PHP Core Error',
            E_CORE_WARNING => 'PHP Core Warning',
            E_DEPRECATED => 'PHP Deprecated Warning',
            E_ERROR => 'PHP Fatal Error',
            E_NOTICE => 'PHP Notice',
            E_PARSE => 'PHP Parse Error',
            E_RECOVERABLE_ERROR => 'PHP Recoverable Error',
            E_STRICT => 'PHP Strict Warning',
            E_USER_DEPRECATED => 'PHP User Deprecated Warning',
            E_USER_ERROR => 'PHP User Error',
            E_USER_NOTICE => 'PHP User Notice',
            E_USER_WARNING => 'PHP User Warning',
            E_WARNING => 'PHP Warning',
            //self::E_HHVM_FATAL_ERROR => 'HHVM Fatal Error',
        ];

        return isset($names[$this->exception->getCode()]) ? $names[$this->exception->getCode()] : 'Error';
    }

    public static function debug_array($date,$pre = true, $t='') {
        if($pre) echo '<pre>';
        echo "[\n";
        $newt = $t. "   ";
        $i = 1;
        foreach ($date as $k=>$v) {
            is_string($k) and $k = "'{$k}'";
            if(is_array($v)) {
                $i++;
                echo "{$newt}{$k} => ";
                self::debug_array($v,false,$newt);
            }
            elseif(is_object($v))  {
                echo "{$newt}{$k} => object()\n";
            }
            else {
                is_string($v) and $v = "'{$v}'";
                echo "{$newt}{$k} => {$v}\n";
            }
        }
        echo "{$t}]\n";
        if($pre) echo '</pre>';
    }
}
?>