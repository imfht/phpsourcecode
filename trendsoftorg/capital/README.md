# Capital

> 金额转中文大写。

[![Build Status](https://travis-ci.org/trendsoft/capital.svg?branch=master)](https://travis-ci.org/trendsoft/capital)
[![Latest Stable Version](https://poser.pugx.org/trendsoft/capital/v/stable)](https://packagist.org/packages/trendsoft/capital)
[![Latest Unstable Version](https://poser.pugx.org/trendsoft/capital/v/unstable)](https://packagist.org/packages/trendsoft/capital)
[![StyleCI](https://styleci.io/repos/113606774/shield?branch=master)](https://styleci.io/repos/113606774)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/trendsoft/capital/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/trendsoft/capital/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/trendsoft/capital/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/trendsoft/capital/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/trendsoft/capital/badges/build.png?b=master)](https://scrutinizer-ci.com/g/trendsoft/capital/build-status/master)
[![Build Status](https://scrutinizer-ci.com/g/trendsoft/capital/badges/build.png?b=master)](https://scrutinizer-ci.com/g/trendsoft/capital/build-status/master)
[![Total Downloads](https://poser.pugx.org/trendsoft/capital/downloads)](https://packagist.org/packages/trendsoft/capital)
[![Monthly Downloads](https://poser.pugx.org/trendsoft/capital/d/monthly)](https://packagist.org/packages/trendsoft/capital)
[![Daily Downloads](https://poser.pugx.org/trendsoft/capital/d/daily)](https://packagist.org/packages/trendsoft/capital)
[![License](https://poser.pugx.org/trendsoft/capital/license)](https://packagist.org/packages/trendsoft/capital)

## 要求
- PHP >= 7.0
- Composer

## 安装

```shell
$ composer require "trendsoft/capital" -vvv
```

## 使用示例

> 如果小数部分是2位以上的，会四舍五入。

```php
( new Money( 0.001 ) )->toCapital(); //零元
( new Money( 0.005 ) )->toCapital(); //壹分
( new Money( 0.01 ) )->toCapital(); //壹分
( new Money( 0.10 ) )->toCapital(); //壹角
( new Money( 0.105 ) )->toCapital(); //壹角壹分
( new Money( 0.11 ) )->toCapital(); //壹角壹分
( new Money( 0.15 ) )->toCapital(); //壹角伍分
( new Money( 1.01 ) )->toCapital(); //壹元零壹分
( new Money( 10.01 ) )->toCapital(); //壹拾元零壹分
( new Money( 0.09 ) )->toCapital(); //玖分
( new Money( 1.0 ) )->toCapital(); //壹元
( new Money( 1.1 ) )->toCapital(); //壹元壹角
( new Money( 2.0 ) )->toCapital(); //贰元
( new Money( 2.1 ) )->toCapital(); //贰元壹角
```


## 算法

### 整数部分

如: 10001000

> 壹仟零佰零拾零万壹仟零佰零拾零元

转换`亿`、`万`、前面的零到后面

> 壹仟零佰零拾万零壹仟零佰零拾零元

去掉`零拾`、`零佰`、`零仟`的单位

> 壹仟零零万零壹仟零零零元

处理`零零`为`零`. `2`次(`拾`、`佰`、`仟`)两次刚好把`零零零`为`零`

> 壹仟零万零壹仟零零元

> 壹仟零万零壹仟零元

处理 `零亿`、`零万`、`零元`的`零`

> 壹仟万零壹仟元

### 小数部分

如: 0.75

`零分`处理为空

`零角`处理

如果有整数部分，转`零角`为`零`

如果没有整数部分，转`零角`为空

### 特殊处理

如: `0`、`0.00` 直接返回`零元`

## Contribution

[Contribution Guide](.github/CONTRIBUTING.md)

## License 

MIT
