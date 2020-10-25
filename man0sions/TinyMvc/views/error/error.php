<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>用户列表</title>

    <!-- Core CSS - Include with every page -->
    <link href="/static/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/static/font-awesome/css/font-awesome.css" rel="stylesheet">


</head>

<body>
<div class="row" style="width: 60%;margin: 10% auto 0 auto;">
    <div class="col-sx-4 ">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo isset($code)?$code : 404 ?></h3>
            </div>
            <div class="panel-body">
                <?php echo isset($message) ? $message : '页面未找到' ;?>
            </div>
        </div>

    </div>

</div>

</body>
</html>