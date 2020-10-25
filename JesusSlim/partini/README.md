# partini
a php web application framework used pinject.

一个基于[pinject](https://github.com/jesusslim/Pinject)实现的phpweb框架

## usage

使用composer安装

	composer require jesussim/partini
	
参考 [example](https://github.com/jesusslim/partini_example)

## .

基于pinject，php依赖注入实现。思想借鉴golang的injector和martini框架，以及laravel。

此框架只是做了最简单的注入与路由、中间件等实现、以及基本的缓存、DB等实现，不希望过于笨重，做到轻量级，在pinject依赖注入核心思想的基础上自由发挥实现web框架。仅供参考，抛砖引玉。

## v0.6

	路由group
	
	封装http request 与 response 为 Context
	
	session middleware
	
	autoload
	
	