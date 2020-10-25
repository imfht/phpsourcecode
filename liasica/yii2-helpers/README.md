[![Latest Stable Version][image-1]][1] [![Total Downloads][image-2]][2] [![Latest Unstable Version][image-3]][3] [![License][image-4]][4]

[1]:	https://packagist.org/packages/liasica/yii2-helpers
[2]:	https://packagist.org/packages/liasica/yii2-helpers
[3]:	https://packagist.org/packages/liasica/yii2-helpers
[4]:	https://packagist.org/packages/liasica/yii2-helpers

[image-1]:	https://poser.pugx.org/liasica/yii2-helpers/v/stable
[image-2]:	https://poser.pugx.org/liasica/yii2-helpers/downloads
[image-3]:	https://poser.pugx.org/liasica/yii2-helpers/v/unstable
[image-4]:	https://poser.pugx.org/liasica/yii2-helpers/license

## ArrayTOXml Usage
```php
$xml = new ArrayToXML();
print $xml->buildXML($array);
```

## SimpleArrayToXml Usage
```php
$xml = new SimpleArrayToXml($redpack->redpackData);
var_dump($xmlmodel->buildXML());
```

## Curl Usage
# 1.curl_get
```php
$curl = new Curl($url);
var_dump($curl->Get());
```
# 2.curl_post
```php
$curl = new Curl($url);
$curl->setData($data);
var_dump($curl->Post());
```
# 3.curl_post_ssl
```php
$certs = [
    CURLOPT_SSLCERT => 'CURLOPT_SSLCERT.pem',
    CURLOPT_SSLKEY  => 'CURLOPT_SSLKEY.pem',
];
$curl = new Curl($url);
$curl->setData($data)->setCerts($certs);
var_dump($curl->postSSL());
```

##Unicode Usage
#1.encode
```php
$unicode = new Unicode(null, $unicodeStr);
var_dump($unicode->encode());
```
#2.decode
```php
$unicode = new Unicode($encodeUnicodeStr);
var_dump($unicode->decode());
```

##Radom Usage
#1.Generate an radom str
```php
$radom = new Radom();
var_dump($radom->RadomChars(32));
```
#2.Get lottery
```php
$proArr = [1 => 1, 2 => 10, 3 => 40];
$radom = new Radom();
var_dump($radom->lottery($proArr));
```

##Url
#1.Get real url
```php
$Url = new Url($uri);
var_dump($Url->realurl());
```
or
```php
$Url = Url::setUri($uri);
var_dump($Url->realurl());
```

##Time
#1.Get formated microtime
```php
$Time = new Time();
var_dump($Time->microtime_format);
```
#2. Get timestamp of microtime
```php
$Time = new Time();
var_dump($Time->microtime_float());
```