<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="utf-8">
<title>Demo</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="renderer" content="webkit" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<style type="text/css">*{margin:0;padding:0;}html {font-size: 62.5%;}html, body {max-width: 100%;width: 100%;}body {font-size: 1.4rem;font-family: "Microsoft Yahei", Helvetica, Tahoma, STXihei, arial,verdana,sans-serif;background:#fff;color:#555;}img {border: none;}pre, pre code {font-family:Consolas, arial,verdana,sans-serif;}#layout {padding: 4rem 6rem 0rem 6rem;}#main {margin: 0 auto;line-height: 1.5;}#main > h1 {font-size: 10rem;margin-bottom: 1rem;}#main > h3 {margin-bottom: 2rem; font-size: 1.8rem;}#main > h4 {margin-bottom: 1rem; font-size: 1.8rem;}#main pre {margin: 1.5rem 0;white-space: pre-wrap;word-wrap: break-word;}#main > p {white-space: pre-wrap;word-wrap: break-word;line-height: 1.3;margin-bottom: 0.6rem;}#main > p > strong {width: 7rem;display:inline-block;}.logo {text-align: left;border-top:1px solid #eee;margin-top: 0rem;padding: 3rem 0 0;color: #ccc;}.logo > h1 {font-weight: normal;font-size: 5rem;}.logo img {margin-left: -2rem;}.trace-line {padding: 0.3rem 0.6rem;margin-left:-0.6rem;-webkit-transition: background-color 300ms ease-out;transition: background-color 300ms ease-out;}.trace-line:hover {background:#fffccc;}a:hover{color:red;}a{color: black;border: solid 1px #ccc;padding: 10px 20px;text-decoration:none;margin-right: 30px;}</style>
</head>
<body>
<div id="layout">
<div id="main">
<a href="<?php echo URL('/');?>" target='_blank' title='不经过控制器方法直接加载视图（实战应用：关于我们，帮助中心等单页）'>单页</a>
<a href="<?php echo URL('/demo2');?>" target='_blank' title='Index控制器，index方法（无分组） '>无分组</a>
<a href="<?php echo URL('/demo3');?>" target='_blank' title='Index控制器，index方法（admin分组）'>有分组</a>
<a href="<?php echo URL('/article2-1-2-3');?>" target='_blank' title='自定义匹配路由'>自定义匹配路由</a>
<a href="<?php echo URL('/article-1-2');?>" target='_blank' title='自定义匹配路由（回调函数）不经过控制器方法直接做业务处理'>自定义匹配路由（回调函数）</a>
<br /><br /><br />
<a href="<?php echo URL('/sdfasfsdf');?>" target='_blank' title='未匹配路由404，直接加载404视图'>404 (:</a>
<a href="<?php echo URL('/page-1');?>" target='_blank' title='分页'>分页</a>
<a href="<?php echo URL('/yzmShow');?>" target='_blank' title='验证码'>验证码</a>
<a href="<?php echo URL('/config');?>" target='_blank' title='数据缓存'>数据缓存</a>
<a href="<?php echo URL('/cache');?>" target='_blank' title='配置文件'>配置文件</a>
<br /><br /><br />
<h3>数据库操作</h3>
<b>SKPHP：</b><br /><br />
<a href="<?php echo URL('/db1');?>" target='_blank' title='CURD'>数据库CURD操作</a>
<br /><br />
<b>Eloquent ORM（Laravel）：</b><br /><br />
<a href="<?php echo URL('/db2');?>" target='_blank' title='Eloquent ORM 中文文档'>Eloquent@SKPHP 实战应用</a>
<a href="http://laravel-china.org/docs/5.0/eloquent" target='_blank' title='Eloquent ORM 中文文档'>Eloquent ORM 中文文档</a>
<a href="http://blog.ja168.net/laravel-eloquent-orm-guide-902.html" target='_blank' title='Laravel Eloquent Orm 使用指南'>Laravel Eloquent Orm 使用指南</a>
<br /><br />
<b>Yii2 ORM：</b><br /><br />
<a href="javascript:alert('开发中...');" target='_blank' title='Yii2'>Yii2@SKPHP 实战应用</a>
<br /><br />
<div class="logo">
<h1>SKPHP, Hello Future</h1>
<p> { Share knowledge change you and me } -- [ 为web梦想家创造的PHP框架。 ]</p>
</div>
</div>
</div>
</body>
</html>