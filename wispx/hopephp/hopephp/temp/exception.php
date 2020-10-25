<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>出现异常</title>
</head>
<style>
    .body {
        font-family: 'Droid Sans', sans-serif;
        font-size: 10pt;
        color: #555;
        line-height: 25px;
    }
    .container {
        padding: 20px;
        margin: 50px auto;
        max-width: 80%;
        /*border: 1px solid #ddd;
        box-shadow: 0 1px 1px rgba(0,0,0,.03);*/
        background: #fff;
        word-wrap: break-word;
    }
    .header {
        font-size: 30px;
        font-weight: normal;
    }
    .body .item {
        margin-top: 15px;
        padding-top: 8px;
        border-top: 1px #dadcdd solid;
    }
    .body .item p {
        margin: 2px 5px;
        color: #2e3437;
    }
    .body .item p:hover {
        text-decoration: underline;
        cursor: pointer;
    }
    .main {
        overflow: hidden;
        color: #555;
    }
    .footer {
        margin-top: 10px;
        border-top: 1px #dadcdd solid;
        color: #636b6f;
    }
</style>
<body>
<div class="container">
    <?php if(\hope\App::$debug): ?>
    <div class="header"><?php echo $e->getMessage(); ?></div>
    <div class="body">
        <div class="item">
            <b>错误位置：</b>
            <p>FILE：<?php echo $e->getFile(); ?>　　line：<?php echo $e->getLine(); ?> 行</p>
        </div>
        <div class="item">
            <b>TRACE：</b>
            <?php foreach ($e->getTrace() as $item => $value): ?>
                <p>#<?php echo $item; ?>
                    <?php echo isset($value['file']) ? $value['file'] : ''; ?>
                    (<?php echo isset($value['line']) ? $value['line'] : ''; ?>)：
                    <?php echo isset($value['class']) ? $value['class'] : ''; ?>
                </p>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
        <div class="main">
            <h1><?php echo $e->getMessage(); ?></h1>
            <h2>Internal Server Error</h2>
        </div>
    <?php endif; ?>
    <div class="footer">
        <p>— HopePHP <?php echo HOPE_VERSION; ?></p>
    </div>
</div>
</body>
</html>
