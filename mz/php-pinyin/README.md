#php-pinyin

php 无组件支持中文汉字转拼音单元（支持生僻字）。

之前用的汉字转拼音单元 已经不能满足需求了，自己重新整理+优化了一下。

方式：先用 gbk 判断码表，取不到的字用生僻字字典。


（需要 php-mb_string 扩展支持）


## 方法： ##


class pinyin{

        // $str : 需要转换的汉字（只支持utf-8）
        // $first_char : 是否只取首字母
        // $split_char : 生成每个字间的分隔符
        // $except_char : 排除字符，防止被过滤掉
	static function get($str, $first_char = 0, $split_char = '', $except_char = '');
}



## 例： ##


`$str = '是默认的编码方式。对于英文文件是ASCII编码，对于简体中文文件是GB2312编码,魍魉,交媾,蒯草';`

// 默认模式

`pinyin::get($str);`

// 全拼音+带分隔线

`pinyin::get($str, 0, '-');`


// 拼音字母+带分隔线

`pinyin::get($str, 1, '-');`


## 已知问题 ##

1. 多音字未处理, 重庆 会被转成 zhongqing 



## 词典工具使用 ##

生成无法识别的文字方法：
打开keywords.txt，将字典放至keyword.txt, 保存为 utf-8 编码。
然后，cmd运行: php make.php find
这个候，我们用记事本或者编辑器打开new_dict.txt,
看到无法识别的文字如下，如果没有无法识别的就不会有内容（举例）：
```
阿
啊
...
```

那么，我们手工将对应的拼音写在文字后边，用空格格开：
```
阿 a
啊 a
...
```
最后，运行：php make.php make
这样，pinyin.class.php 就会有新加入的字典了
我们拷贝新的 pinyin.class.php 去项目就可以识别之前不能识别的字了。