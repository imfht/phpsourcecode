<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title> {$title} </title>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <meta name="Keywords" content="">
    <meta name="Description" content="">
</head>

<body>
<h1>This is a test !</h1>
当前Action：{$this->id}
<br />
模块访问示例：{literal}{$MODULE_BASE_URL}{/literal}/xxx
效果：{$MODULE_BASE_URL}/xxx
<br />
模块静态资源访问示例：{literal}{$MODULE_STATIC_BASE_URL}{/literal}/css/style.css
效果：{$MODULE_STATIC_BASE_URL}/css/style.css
</body>
</html>