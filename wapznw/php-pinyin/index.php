<?php
 /**
 * @package default
 * @copyright php-pinyin.
 * @author 自娱自乐自逍遥 <wapznw@qq.com>
 */

require_once 'PinYin/PinYin.class.php';

echo join(' ', PinYin::toPinyin('带着希望去旅行，比到达终点更美好')); # dai zhuo xi wang qu lv xing , bi dao da zhong dian geng mei hao
echo PHP_EOL;
echo join(' ', PinYin::toPinyin('重庆是一个很重要的城市', true)); # chóng qìng shì yí gè hěn zhòng yào dí chéng shì