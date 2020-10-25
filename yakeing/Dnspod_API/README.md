# Dnspod_API

The [DNSPod User API](https://www.dnspod.com/docs/index.html) OR 
[DNSPod中文文档](https://www.dnspod.cn/docs/index.html) is restricted to individual users, making it easier and more flexible for users to manage their own domain names and records.

https://www.dnspod.com China Hong Kong

https://www.dnspod.cn China Shandong Province

`Need to cooperate with Curl extension`

### Github badge

[![Downloads](https://img.shields.io/github/downloads/yakeing/dnspod_api/total?color=dfb317&logo=github)](../../)
[![Size](https://img.shields.io/github/size/yakeing/dnspod_api/src/dnspod.php?color=b36d41&logo=github)](src/dnspod.php)
[![tag](https://img.shields.io/github/v/tag/yakeing/dnspod_api?color=28a745&logo=github)](../../releases)
[![license](https://img.shields.io/github/license/yakeing/dnspod_api?color=FE7D37&logo=github)](LICENSE)
[![languages](https://img.shields.io/badge/languages-php-007EC6?logo=github)](../../search?l=php)

### Installation

Use [Composer](https://getcomposer.org) to install the library.
Of course, You can go to [Packagist](https://packagist.org/packages/yakeing/dnspod_api) to view.

```
    $ composer require yakeing/dnspod_api
```

### Initialization parameter

- [x] Sample：
```php
    $uid = 12345;
    $token = X12345;
    $DP = new Dnspod($uid, $token);
```

### Add or modify records

- [x] Sample：
```php
    $domain = 'example.com';
    $value = array(
        '255.255.255.1',
        '255.255.255.2',
        '255.255.255.3',
        );
    $name = 'www';
    $type = 'A';
    $DP->Records($domain, $value, $name, $type, true);
```

### Copy A record

- [x] Sample：
```php
    $domain = 'example.com';
    $DP->copyArecord($domain);
```

### Get domain information

- [x] Sample：
```php
    $copyDomain = 'google.com';
    $toDomain = 'example.com';
    echo $DP->getDomainInfo($copyDomain, $toDomain);
```

### Get a list of records

- [x] Sample：
```php
    $domain = 'example.com';
    echo $DP->getRecordList($domain);
```

### Get details of batch tasks

- [x] Sample：
```php
    $job_id = 'j12345';
    echo $DP->getBatchDetail($job_id);
```

### Add a single record 

- [x] Sample：
```php
    $domain = 'example.com';
    $name = 'www';
    $value = '255.255.255.0';
    $type = 'A';
    echo $DP->addRecord($domain, $name, $value, $type);
```

### Add records in bulk

- [x] Sample：
```php
    $domain_id = '12345';
    $record[0] = array('name'=>'WWW', 'type'=>'A', 'value'='255.255.255.0', 'mx'=>1);
    echo $DP->batchAddRecord($domain_id, $record);
```

### Modify record

- [x] Sample：
```php
    $domain = 'example.com';
    $record_id = 'E12345';
    $name = 'WWW2';
    $value = '255.255.255.0';
    $type = 'A';
    $mx = 1;
    echo $DP->recordModify($domain, $record_id, $name, $value, $type, $mx);
```

### Modify record

- [x] Sample：
```php
    $domain = 'example.com';
    $record_id = 'E12345';
    echo $DP->recordRemove($domain, $record_id);
```


### Other functions

- [x] Sample：
```php
    //Get the API version number
    echo $DP->getVersion();
    
    //Get the level allowed line
    $domain = 'example.com';
    echo $DP->getRecordLine($domain);
    
    //Get a list of domain names
    echo $DP->getDomainList();
    
    //Construct a new record table
    $name = 'example.com';
    $type = 'A';
    $value = '255.255.255.0';
    $DP->newRecords($name, $type, $value);
```

[Sponsor](https://github.com/yakeing/Documentation/blob/master/Sponsor/README.md)
---
If you've got value from any of the content which I have created, then I would very much appreciate your support by payment donate.

[![Sponsor](https://img.shields.io/badge/-Sponsor-EA4AAA?logo=google%20fit&logoColor=FFFFFF)](https://github.com/yakeing/Documentation/blob/master/Sponsor/README.md)

Author
---

weibo: [yakeing](https://weibo.com/yakeing)

twitter: [yakeing](https://twitter.com/yakeing)
