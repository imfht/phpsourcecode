<?php
    if(!defined('IN_INSTALL')) {
        exit('Access Denied');
    }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>SAPI++应用安装</title>
<meta name="renderer" content="webkit">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes">
<link rel="stylesheet" type="text/css" href="css/install.css" />
<script type="text/javascript" src="/common/js/do.js"></script>
<script type="text/javascript" src="/common/js/package.js"></script>
</head>
<body>
<div class="header">
    <div class="wrap auto title fn-tac">
        <img src="images/init_data.png">
        <p>配置数据库</p>
    </div>
</div>
<div class="wrap auto">
    <div class="row install">
        <div class="col-s20"></div>
        <div class="col-s60 col-m100">
            <form class="form-horizontal" action="index.php?c=success" method="post">
            <table class="table table-border">
                <thead><tr><th class="w100"></th><th>数据库配置</th></tr></thead>
                <tbody>
                    <tr><td class="fn-tar">数据库类型:</td><td><input type="text" class="input readonly" name="DB_TYPE" value="mysql" id="DB_TYPE" disabled="disabled"></td></tr>
                    <tr><td class="fn-tar">数据库主机:</td><td><input type="text" class="input" name="DB_HOST" value="localhost" id="DB_HOST"> <span class="gray">一般为 localhost</span></td></tr>
                    <tr><td class="fn-tar">数据库端口:</td><td><input type="text" class="input" name="DB_PORT" value="3306" id="DB_PORT"> <span class="gray">一般为 3306</span></td></tr>
                    <tr><td class="fn-tar">数据库名称:</td><td><input type="text" class="input" value="sapixx" name="DB_NAME"  id="DB_NAME"> <span class="gray">您创建的数据库名称、仅支持英文</span></td></tr>
                    <tr><td class="fn-tar">数据表前缀:</td><td><input type="text" class="input readonly" name="DB_PREFIX" value="ai_" id="DB_PREFIX" readonly="readonly"> <span class="gray">暂不支持更改表前缀</span></td></tr>
                    <tr><td class="fn-tar">链接用户名:</td><td><input type="text" class="input" name="DB_USER" value="root" id="DB_USER"></td></tr>
                    <tr><td class="fn-tar">数据库密码:</td><td><input type="password" class="input" name="DB_PWD" id="DB_PWD"></td></tr>
                    <tr><th colspan="2" class="fn-tac p10"><button type="submit" class="button button-red">开始安装</button></th></tr>
                </tbody>
            </table>
            </form>
        </div>
        <div class="col-s20"></div>
    </div>
</div>
</body>
</html>
