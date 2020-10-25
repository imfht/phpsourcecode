<?php

/**
 * 开始的字符串相同.
 *
 * @param string $haystack 可能包含子串的字符串
 * @param string $needle   要查找的子串
 *
 * @return bool
 */
function hp_starts_with($haystack, $needle)
{
    return \Docx\Common::startsWith($haystack, $needle);
}

/**
 * 改写成适合的网址
 */
function hp_slugify($name)
{
    return \Docx\Utility\Word::slugify($name);
}

/**
 * 中文格式化日期
 */
function hp_zhdate($format, $timestamp = false)
{
    $result = date($format, $timestamp);
    if (strpos($format, '星期w') !== false) {
        $weekdays = ['星期0'=>'星期日', '星期1'=>'星期一', '星期2'=>'星期二',
            '星期3'=>'星期三', '星期4'=>'星期四', '星期5'=>'星期五', '星期6'=>'星期六'];
        $result = str_replace(array_keys($weekdays), array_values($weekdays), $result);
    }
    return $result;
}

/**
 * 随机显示一条语录
 */
function hp_rand_greeting($greetings)
{
    static $index = 0;
    if ($index === 0) {
        shuffle($greetings);
        $index = count($greetings);
    }
    return $greetings[--$index];
}
