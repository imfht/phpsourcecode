<?php declare (strict_types = 1);
namespace msqphp\base\str;

use msqphp\core\traits;

final class Str
{
    use traits\CallStatic;

    use StrRandomTrait;

    // 扔出异常
    private static function exception(string $message): void
    {
        throw new StrException($message);
    }

    /**
     * @param  string       $string    字符串
     * @param  string       $haystack  字符串
     * @param  string|array $needle    查找字符
     */

    // 反转字符
    public static function reverse(string $string): string
    {
        $result = '';
        for ($i = mb_strlen($string); $i > 0; --$i) {
            $result .= mb_substr($string, $i - 1, 1);
        }
        return $result;
    }

    // 是否包含某字符串
    public static function contains(string $haystack, $needle): bool
    {
        foreach ((array) $needle as $target) {
            if ('' !== $target && false !== strpos($haystack, $target)) {
                return true;
            }
        }
        return false;
    }
    // 是否以某些字符开始
    public static function startsWith(string $haystack, $needle): bool
    {
        foreach ((array) $needle as $target) {
            if ('' !== $target && 0 === strncmp($haystack, $target, strlen($target))) {
                return true;
            }
        }
        return false;
    }
    // 是否以某些字符结束
    public static function endsWith(string $haystack, $needle): bool
    {
        foreach ((array) $needle as $target) {
            if ((string) $target === substr($haystack, -strlen($target))) {
                return true;
            }
        }
        return false;
    }
    // 限定多少个的单词
    public static function words(string $value, int $words = 100, string $end = '...'): string
    {
        preg_match('/^\s*+(?:\S++\s*+){1, ' . $words . '}/u', $value, $matches);

        if (!isset($matches[0]) || strlen($value) === strlen($matches[0])) {
            return $value;
        }

        return rtrim($matches[0]) . $end;
    }

    // 得到字符串间的差异值(最少几个字符可以替换)
    public static function levenshtein(string $a, string $b): int
    {
        return levenshtein($a, $b);
    }
    public static function countWords(string $string): int
    {
        return str_word_count($string);
    }
    public static function escapeshellcmd(string $string): string
    {
        return escapeshellcmd($string);
    }
    // 转换一个字符串为snake命名法 ....   ->index_action
    public static function snake(string $value): string
    {
        if (!ctype_lower($value)) {
            return strtolower(preg_replace('/(.)(?=[A-Z])/', '$1' . '_', preg_replace('/\s+/', '', $value)));
        } else {
            return $value;
        }
    }
    // 转换一个字符串为studly命名法 ....   ->IndexAction
    public static function studly(string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $string)));
    }
}
