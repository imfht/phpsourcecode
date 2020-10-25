<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Yuncart安装后台</title>
        <link rel="stylesheet" href="install.css" type="text/css" />
        <script type="text/javascript" src="../template/jslib/jquery.js"></script>
    </head>
    <body>

        <div class="installdiv">
            <?php
            if ($step == 1) {
                require '_step_1.php';
            } elseif ($step == 2) {
                require '_step_2.php';
            } elseif ($step == 3) {
                require '_step_3.php';
            } else {
                ?>
                <div class="install">
                    <h1>安装提示</h1>
                    <div class="installipt">
                        <?php if ($errors) { ?>
                            <p class="perr"><?php echo $errors ?></p>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="footer">Copyright &copy; 2012 版权所有 Powered By <a href="http://www.yuncart.com" target="_blank">yuncart</a></div>
    </body>
</html>