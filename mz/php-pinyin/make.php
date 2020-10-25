<?php
/**
 * Date: 2016-10-09
 * Time: 0:21
 */
//生成无法识别的文字方法：
//打开keywords.txt，将字典放至keyword.txt, 保存为 utf-8 编码。
//然后，cmd运行: php make.php find
// 这个候，我们用记事本或者编辑器打开new_dict.txt,
/*
看到无法识别的文字如下，如果没有无法识别的就不会有内容（举例）：
阿
啊
...


那么，我们手工将对应的拼音写在文字后边，用空格格开：
阿 a
啊 a
...
*/
// 最后，运行：php make.php make
// 这样，pinyin.class.php 就会有新加入的字典了。
include 'pinyin.class.php';
pinyin::init_data();

if ($argv[1] == 'find') {
// load keywords
    $content    = file_get_contents('keywords.txt');
    $len        = mb_strlen($content, 'utf-8');
    $dict_array = array();
    for ($i = 0; $i < $len; $i++) {
        $word = trim(mb_substr($content, $i, 1, 'utf-8'));
        if (!$word || is_numeric($word) || strlen($word) == 1) {
            continue;
        }
        $pinyin = pinyin::get($word);
        if (!$pinyin) {
            $dict_array[$word] = 1;
        }
    }
    // 将无法识别的文字，写入 new_dict.txt
    file_put_contents('new_dict.txt', implode("\r\n", array_keys($dict_array)));
} else if ($argv[1] == 'make') {
    $lines     = file('new_dict.txt');
    $is_change = 0;
    foreach ($lines as $line) {
        $line = trim(preg_replace('#\s+#is', ' ', $line));
        if (!$line) {
            continue;
        }
        list($word, $pinyin) = explode(' ', $line);
        if (!$word || !$pinyin) {
            continue;
        }
        $pinyin = strtolower($pinyin);
        $index  = -1;
        foreach (pinyin::$data['pinyin'] as $i => $exists) {
            if ($exists == $pinyin) {
                $index = $i;
                break;
            }
        }
        // 跳过不存在的拼音
        if ($index == -1) {
            continue;
        }
        // 已经存在的，跳过
        if(isset(pinyin::$data['word'][$word])){
            continue;
        }
        //在词典中记录文字对应的拼音
        pinyin::$data['word'][$word] = $index;
        $is_change++;
    }
    if (!$is_change) {
        exit('Dict Is Not Change');
    }
    $pinyin_str  = 'self::$data = json_decode(\'' . json_encode2(pinyin::$data) . '\', 1);';
    $php_content = file_get_contents('pinyin.class.php');
    $php_content = preg_replace('#self\:\:\$data\s*=[\s\S]*?;#is', $pinyin_str, $php_content);
    file_put_contents('pinyin.class.php', $php_content);
    exit('addNewWord=' . $is_change);

}


/**
 * json encode 2
 *
 * @param $data
 *
 * @return string
 */
function json_encode2($data) {
    if (is_array($data) || is_object($data)) {
        $is_list = is_array($data) && (empty($data) || array_keys($data) === range(0, count($data) - 1));
        if ($is_list) {
            $json = '[' . implode(',', array_map('json_encode2', $data)) . ']';
        } else {
            $items = Array();
            foreach ($data as $key => $value) $items[] = json_encode2("$key") . ':' . json_encode2($value);
            $json = '{' . implode(',', $items) . '}';
        }
    } elseif (is_string($data)) {
        $string = '"' . addcslashes($data, "\\\"\n\r\t/" . chr(8) . chr(12)) . '"';
        $json   = '';
        $len    = strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $char = $string[$i];
            $c1   = ord($char);
            if ($c1 < 128) {
                $json .= ($c1 > 31) ? $char : sprintf("\\u%04x", $c1);
                continue;
            }
            $json .= $char;
        }
    } else {
        $json = strtolower(var_export($data, true));
    }
    return $json;
}
?>