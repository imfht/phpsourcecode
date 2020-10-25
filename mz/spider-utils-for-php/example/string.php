<?php
/**
 * User: dj
 * Date: 2020/9/11
 * Time: 上午1:29
 * Mail: 199962760@qq.com
 */

include '../src/spider.php';

use \ZV\Spider as spider;

// html2txt (has newline)
echo spider::html2txt('<h1>html2txt</h1>'), PHP_EOL,

// no html(without newline)
spider::noHtml('<h1>noHtml</h1>'), PHP_EOL,

// strip_tags
spider::strip_tags('<h1>strip_tags</h1>'), PHP_EOL,

// cut str
spider::cut('<h1>cut</h1>', '<h1>', '</h1>'), PHP_EOL,

// match with mask
spider::maskMatch('<h1>maskMatch</h1>', '<h1>(*)</h1>'), PHP_EOL,

// match with regexp
spider::regMatch('<h1>regMatch</h1>', '#<h1>([^>]*?)</h1>#is'), PHP_EOL,

// match with multi pattern
print_r(spider::match('<h1>MatchByMultiPattern</h1><h2>Description</h2>', [
    'title' => '#<h1>([^>]*?)</h1>#is',
    'desc'  => '<h2>(*)</h2>'
]), 1), PHP_EOL;