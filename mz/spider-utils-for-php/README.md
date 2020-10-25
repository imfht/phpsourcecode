# spider
PHP simple http client/spider class/简单的 PHP 的网络库

特色：

- 简单易用
- HTTP 抓取和匹配
- 自动识别 HTML/XML 等文档编码为 utf-8
- 支持多 IP 出口设置
- 支持各类代理

### Installation

```
composer require zv/spider
```

### basic

```

$spider = new \ZV\Spider('https://www.baidu.com/s?wd=爱情&pn=50&rn=50&tn=json', [
    //'User-Agent' => 'mobile',
]);

$spider->GET();
print_r($spider->getResponseCode());
print_r($spider->getResponseHeader());
print_r($spider->getBody());
print_r($spider->getUrl());
print_r($spider->getJson());

```

### POST 

```

use \ZV\Spider as spider;

$spider = new spider('http://127.0.0.1/post', [
]);

$spider->POST([
    'query' => 1,
    // upload
    'file1' => '@' . __FILE__,
    // upload file with MIME
    'file2' => '@' . __FILE__ . ';text/plain'
]);

print_r($spider->getBody());


```

### string utils

```

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

```

