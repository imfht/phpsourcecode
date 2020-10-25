<?php declare (strict_types = 1);
namespace msqphp\main\template;

use msqphp\base;
use msqphp\core\config\Config;

final class Template
{
    // 左定界符
    private static $left_delimiter = '';
    // 右定界符
    private static $right_delimiter = '';

    // 抛出异常
    private static function exception(string $message): void
    {
        throw new TemplateException('[模版引擎错误]' . $message);
    }

    // 模板引擎初始化
    private static function init(): void
    {
        static $inited = false;
        if ($inited) {
            return;
        }
        $inited = true;

        $config                  = Config::get('template');
        static::$left_delimiter  = $config['left_delimiter'] ?? '<{';
        static::$right_delimiter = $config['right_delimiter'] ?? '}>';
    }
    public static function getDelimiter(): array
    {
        return [static::$left_delimiter, static::$right_delimiter];
    }
    public static function commpileString(string $content, array $data = [], array $language = []): string
    {
        static::init();
        return static::parseCommpileWithIterator(new TemplateStringIterator($content), $data, $language);
    }
    public static function commpileFile(string $file, array $data = [], array $language = []): string
    {
        static::init();
        return static::parseCommpileWithIterator(new TemplateFileIterator($file), $data, $language);
    }
    private static function beginWithDelimiter(string $content): bool
    {
        return 0 === strncmp($content, static::$left_delimiter, strlen(static::$left_delimiter));
    }
    private static function removeBothSideDelimiter(string $content): string
    {
        return trim(substr(substr($content, strlen(static::$left_delimiter)), 0, 0 - strlen(static::$right_delimiter)));
    }

    private static function parseCommpileWithIterator(TemplateIterator $template, array $data = [], array $language = []): string
    {
        $result = '';

        while ($template->valid()) {

            $template->next();

            // 非模版标签,直接忽略.
            if (!static::beginWithDelimiter($template->current())) {
                $result .= $template->current();
            } else {

                $tag = static::removeBothSideDelimiter($template->current());

                if (0 === strncmp($tag, 'foreach', 7)) {
                    $result .= static::parForeach($template, $data, $language);
                } elseif (0 === strncmp($tag, 'if', 2)) {
                    $result .= static::parIf($template, $data, $language);
                } else {
                    $php_value = static::parseTagAsPHPValue($tag, $data, $language);
                    $result .= $php_value['cached']
                    ? static::stringToEchoText((string) $php_value['value'])
                    : '<?php echo (string) ' . $php_value['tag'] . ';?>';
                }
            }
        }

        return $result;
    }

    private static function compare($value_a, $value_b, string $type): bool
    {
        switch ($type) {
            case '===':
                return $value_a === $value_b;
            case '==':
                return $value_a == $value_b;
            case '!=':
            case '<>':
                return $value_a != $value_b;
            case '!==':
                return $value_a !== $value_b;
            case '>=':
                return $value_a >= $value_b;
            case '<=':
                return $value_a <= $value_b;
            case '<':
                return $value_a < $value_b;
            case '>':
                return $value_a > $value_b;
            default:
                static::exception('未知的比较符' . var_export($type, true));
        }
    }

    private static function textToPhpValue(string $value)
    {
        if (isset($value[0]) && $value[0] === '\'') {
            return trim(stripslashes($value), '\'');
        } elseif (isset($value[0]) && $value[0] === '"') {
            return trim(stripslashes($value), '"');
        } elseif ($value === 'true') {
            return true;
        } elseif ($value === 'false') {
            return false;
        } elseif (is_numeric($value)) {
            return is_int($value) ? (int) $value : (float) $value;
        } elseif ($value === 'null') {
            return null;
        } elseif (is_string($value)) {
            return $value;
        } else {
            static::exception('未知的类型值' . var_export($value, true));
        }
    }

    private static function phpValueTotext($value): string
    {
        // 字符串
        if (is_string($value)) {
            if (isset($value[0]) && $value[0] === '$') {
                return $value;
            }
            return '\'' . addslashes($value) . '\'';
            // 数字
        } elseif (is_int($value) || is_float($value)) {
            return (string) $value;
            // 布尔值
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
            // null
        } elseif (is_null($value)) {
            return 'null';
        } else {
            static::exception('未知的类型值' . var_export($value, true));
        }
    }

    private static function isCachedValue(string $name, array $data): bool
    {
        return (isset($data[$name]) && $data[$name]['cache'])
            || (in_array(strtolower($name), ['language', 'lang', 'constant', 'const']));
    }

    private static function parseFunctionWithNameAndArgsList(string $func_name, string $args_list, array $data, array $language)
    {
        if (empty($args_list)) {
            $tag = $func_name . '()';
            return ['cached' => false, 'value' => $tag, 'tag' => $tag];
        }
        // 获取函数参数
        $args_list = array_map('trim', explode(',', $args_list));
        $args_tag  = [];
        $args_val  = [];
        $cached    = true;
        // 得到参数列表,可以为空,若果参数缓存则直接替换
        foreach ($args_list as $arg) {
            if (static::textToPhpValue($arg) === $arg) {
                $args_result = static::parseTagAsPHPValue($arg, $data, $language);
                $args_tag[]  = $args_result['tag'];
                $args_val[]  = $args_result['cached'] ? $args_result['value'] : $args_result['tag'];
                $cached      = $cached && $args_result['cached'];
            } else {
                $args_val[] = static::textToPhpValue($arg);
                $args_tag[] = $arg;
            }
        }

        $tag = $func_name . '(' . implode(',', $args_tag) . ')';

        if (!$cached) {
            $value = $tag;
        } else {
            switch (strtolower($func_name)) {
                case 'isset':
                    $value = base\arr\Arr::isset($data, $args_list[0]);
                    break;
                default:
                    $value = call_user_func_array($func_name, $args_val);
                    break;
            }
        }

        return ['cached' => $cached, 'value' => $value, 'tag' => $tag];
    }
    private static function stringToEchoText(string $content): string
    {
        return '<?php echo \'' . str_replace('\'', '\\\'', $content) . '\';?>';
    }

    private static function parseTagAsPHPValue(string $tag, array $data, array $language): array
    {
        $left_  = strpos($tag, '(');
        $right_ = strpos($tag, ')');
        if ($left_ !== false && $right_ !== false) {
            $result = [
                'type'      => 'func',
                'func_name' => substr($tag, 0, $left_),
                'args_list' => substr($tag, $left_ + 1, $right_ - strlen($tag)),
            ];
        } elseif ((($right_ !== false) && ($left_ === false)) || (($right_ === false) && ($left_ !== false))) {
            $this->exception('错误的函数');
        } else {
            if (false !== $pos_ = strpos($tag, '.')) {
                $result = [
                    'type'     => 'array',
                    'arr_name' => substr($tag, 0, $pos_),
                    'arr_key'  => substr($tag, $pos_ + 1),
                ];
            } else {
                $result = ['type' => 'var', 'name' => $tag];
            }
        }

        switch ($result['type']) {
            case 'var':
                if (isset($data[$result['name']])) {
                    return [
                        'tag'    => '$' . $result['name'],
                        'value'  => $data[$result['name']]['value'],
                        'cached' => static::isCachedValue($result['name'], $data),
                    ];
                } else {
                    return [
                        'tag'    => '$' . $result['name'],
                        'value'  => '$' . $result['name'],
                        'cached' => false,
                    ];
                }
            case 'array':
                return static::parseArrayWithNameAndKey($result['arr_name'], $result['arr_key'], $data, $language);
            case 'func':
                return static::parseFunctionWithNameAndArgsList($result['func_name'], $result['args_list'], $data, $language);
            default:
                static::exception('错误的标签内容');
        }
    }
    private static function parseArrayWithNameAndKey(string $arr_name, string $arr_key, array $data, array $language)
    {
        // 拼接成php格式
        if (false !== strpos($arr_key, '.')) {
            $arr_true_key = array_map('static::phpValueTotext', explode('.', trim($arr_key, '.')));
            $arr_true_key = '[' . implode('][', (array) $arr_key) . ']';
        } else {
            $arr_true_key = '[' . static::phpValueTotext($arr_key) . ']';
        }

        $arr_tag = '$' . $arr_name . $arr_true_key;

        if (in_array(strtolower($arr_name), ['language', 'lang'])) {
            isset($language[$arr_key]) || static::exception($arr_key . '对应语言不存在');
            $cached  = true;
            $arr_val = $language[$arr_key];
        } elseif (in_array(strtolower($arr_name), ['constant', 'const'])) {
            defined($arr_key) || static::exception($arr_key . '常量未定义');
            $cached  = true;
            $arr_val = constant($arr_key);
        } else {
            // 分割字符串
            $arr_key_list = explode('.', trim($arr_key, '.'));
            // 获取对应值
            $arr_key = array_map('static::textToPhpValue', $arr_key_list);
            // 获取值
            if (isset($data[$arr_name]['value']) && $data[$arr_name]['cache']) {
                $cached  = true;
                $arr_val = $data[$arr_name]['value'];
                for ($i = 0, $l = count($arr_key); $i < $l; ++$i) {
                    if (!isset($arr_val[$arr_key[$i]])) {
                        $arr_val = null;
                    } else {
                        $arr_val = $arr_val[$arr_key[$i]];
                    }
                }
            } else {
                $cached  = false;
                $arr_val = $arr_tag;
            }
        }

        return [
            'tag'    => $arr_tag,
            'value'  => $arr_val,
            'cached' => $cached,
        ];
    }
    private static function parForeach(TemplateIterator $template, array $data, array $language): string
    {
        $tag = static::removeBothSideDelimiter($template->current());
        $tag = trim(substr($tag, 7));
        if (false === $pos = strpos($tag, ' as ')) {
            static::exception('错误的foreach语句');
        }
        $arr_name = trim(substr($tag, 0, $pos));

        ['cached' => $foreach_arr_cached, 'value' => $foreach_arr_value, 'tag' => $foreach_arr_tag] = static::parseTagAsPHPValue($arr_name, $data, $language);

        $foreach_after = trim(substr($tag, $pos + 3));
        if (false !== $pos = strpos($foreach_after, '=>')) {
            $foreach_key = trim(substr($foreach_after, 0, $pos));
            $foreach_val = trim(substr($foreach_after, $pos + 2));
        } else {
            $foreach_key = null;
            $foreach_val = $foreach_after;
        }

        // 深度
        $deep = 1;
        // foreach 头
        $begin = $template->current();
        // 循环内容
        $content = '';
        // 结果
        $result = '';
        // 左限定符
        $left_delimiter = static::$left_delimiter;

        // 获取foreach循环内容
        while ($template->valid()) {
            $template->next();
            // 包括一个foreach循环
            if (base\str\Str::startsWith($template->current(), $left_delimiter . 'foreach')) {
                ++$deep;
            }
            // 跳出一个foreach循环
            if (base\str\Str::startsWith($template->current(), [$left_delimiter . 'endforeach', $left_delimiter . '/endforeach'])) {
                --$deep;
            }
            // 深度为0,则当前foreach闭合,返回
            if ($deep === 0) {
                break;
            }
            // 将内容添加值foreach中间,即循环内容中
            $content .= $template->current();
        }

        // 如果深度不为0,即未闭合,异常
        0 === $deep || static::exception('未闭合的foreach标签');

        // 如果foreach所遍历的数组缓存
        if ($foreach_arr_cached) {
            // 数组循环
            foreach ($foreach_arr_value as $key => $value) {
                // 编译,数据添加对应值,并将循环结果添加至结果中
                $result .= static::commpileString($content, array_merge($data,
                    $foreach_key === null
                    ? [$foreach_val => ['cache' => true, 'value' => $value]]
                    : [$foreach_key => ['cache' => true, 'value' => $key], $foreach_val => ['cache' => true, 'value' => $value]]
                ), $language);
            }
            // 拼接并直接返回
        } else {
            // 开头
            $result .= $foreach_key === null
            ? '<?php foreach (' . $foreach_arr_tag . ' as $' . $foreach_val . '): ?>'
            : '<?php foreach (' . $foreach_arr_tag . ' as $' . $foreach_key . '=>$' . $foreach_val . ') : ?>';
            // 循环内容编译
            $result .= static::commpileString($content, $data, $language);
            // 结尾
            $result .= '<?php endforeach;?>';
        }
        return $result;
    }

    private static function parIf(TemplateIterator $template, array $data, array $language): string
    {
        // 深度
        $deep = 1;
        // 分支
        $branch = 0;
        // if数据数组
        $if = [];
        // if0,即的一个if语句
        $if[0] = ['tag' => static::removeBothSideDelimiter($template->current()), 'content' => '', 'type' => 'if'];
        // 左定界符
        $left_delimiter = static::$left_delimiter;
        // 当有内容时
        while ($template->valid()) {
            $template->next();

            if (static::beginWithDelimiter($template->current())) {
                $tag = static::removeBothSideDelimiter($template->current());
                // 包括一个if循环
                if (base\str\Str::startsWith($tag, 'if')) {
                    ++$deep;
                }
                // 跳出一个if循环
                if (base\str\Str::startsWith($tag, ['endif', '/endif'])) {
                    --$deep;
                }

                // 深度为0,此时整个if语句段结束
                if (0 === $deep) {
                    // 跳出
                    break;
                }

                // 如果深度为1,且以else开头,则为另一个分支,即新的elseif段或者else段
                if (1 === $deep && base\str\Str::startsWith($tag, 'else')) {
                    ++$branch;
                    // 赋值
                    $if[$branch] = [
                        'tag'     => $tag,
                        'content' => '',
                    ];
                    if (base\str\Str::startsWith($tag, ['else if', 'elseif'])) {
                        $if[$branch]['type'] = 'elseif';
                    } else {
                        $if[$branch]['type'] = 'else';
                    }
                    continue;
                }
            }
            $if[$branch]['content'] .= $template->current();
        }

        // 深度不为0
        0 === $deep || static::exception('未闭合的if语句');

        // 结果数组
        $result_if = [];

        while (isset($if[0])) {
            $cached       = false;
            $if_tag       = '';
            $cached_value = null;

            if ($if[0]['type'] === 'if' || $if[0]['type'] === 'elseif') {
                $tag = trim(substr($if[0]['tag'], $if[0]['type'] === 'if' ? 2 : 6));
                // 比较符
                $compare_array = ['===', '!==', '<=', '>=', '!=', '==', '<>', '<', '>'];

                foreach ($compare_array as $compare_value) {
                    if (false !== $pos = strpos($tag, $compare_value)) {
                        $compare_tag   = $compare_value;
                        $compare_left  = trim(substr($tag, 0, $pos));
                        $compare_right = trim(substr($tag, $pos + strlen($compare_value)));
                        break;
                    }
                }

                if (isset($compare_tag)) {
                    if (static::textToPhpValue($compare_left) === $compare_left) {
                        ['cached' => $left_cached, 'value' => $compare_left_value, 'tag' => $compare_left_tag] = static::parseTagAsPHPValue($compare_left, $data, $language);
                    } else {
                        $left_cached        = true;
                        $compare_left_value = static::textToPhpValue($compare_left);
                        $compare_left_tag   = $compare_left;
                    }
                    if (static::textToPhpValue($compare_right) === $compare_right) {
                        ['cached' => $right_cached, 'value' => $compare_right_value, 'tag' => $compare_right_tag] = static::parseTagAsPHPValue($compare_right, $data, $language);
                    } else {
                        $right_cached        = true;
                        $compare_right_value = static::textToPhpValue($compare_right);
                        $compare_right_tag   = $compare_right;
                    }
                    if ($left_cached && $right_cached) {
                        $cached       = true;
                        $cached_value = static::compare($compare_left_value, $compare_right_value, $compare_tag);
                    } else {
                        $cached = false;
                        $if_tag = $compare_left_tag . $compare_tag . $compare_right_tag;
                    }
                } else {
                    ['cached' => $cached, 'value' => $cached_value, 'tag' => $if_tag] = static::parseTagAsPHPValue($tag, $data, $language);
                }

                if ($cached) {
                    if ((bool) $cached_value === true) {
                        $result_if[] = empty($result_if)
                        ? static::commpileString($if[0]['content'], $data, $language)
                        : '<?php else: ?>' . static::commpileString($if[0]['content'], $data, $language) . '<?php endif;?>';
                        break;
                    }
                } else {
                    $result_if[] = '<?php ' . (empty($result_if) ? 'if' : 'elseif') . '(' . $if_tag . ') : ?>' . static::commpileString($if[0]['content'], $data, $language);
                }
            } else {
                $result_if[] = empty($result_if)
                ? static::commpileString($if[0]['content'], $data, $language)
                : '<?php else: ?>' . static::commpileString($if[0]['content'], $data, $language) . '<?php endif;?>';
                break;
            }

            array_shift($if);
            // 如果执行到最后,则添加一个endif结尾
            empty($if) && !empty($result_if) && $result_if[] = '<?php endif;?>';
        }

        return implode('', $result_if);
    }
}
