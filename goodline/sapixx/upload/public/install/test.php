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
        <img src="images/default_icon.png">
        <p>开始安装</p>
    </div>
</div>
<div class="wrap auto">
    <div class="row install">
        <div class="col-s20"></div>
        <div class="col-s60 col-m100">
            <?php $php_version = explode('.', PHP_VERSION); ?>
            <table class="table table-border">
                <thead><tr><th class="w100"></th><th>环境检测</th></tr></thead>
                <tbody>
                <tr>
                    <td class="w100">运行目录</td>
                    <td class="<?php if($_SERVER['PHP_SELF'] != '/install/index.php')echo 'yes'; ?>">
                    <?php if ($_SERVER['PHP_SELF'] == '/install/index.php'): ?>
                <img src="./images/right.png" class="yes"><?php else: ?>
                <img src="./images/fail.png"><?php endif ?>
                <span class="gray fn-f12">域名(<?php echo $_SERVER['HTTP_HOST']?>)必须绑定网站运行根目录到 /public 路径</span>
                </td>
                </tr>
                    <tr>
                        <td class="w100">PHP版本</td>
                        <td class="<?php if(version_compare(PHP_VERSION,'7.2.0', '>='))echo 'yes'; ?>">
                        <?php if (version_compare(PHP_VERSION,'7.2.0', '>=')): ?>
<img src="./images/right.png" class="yes"><?php else: ?>
<img src="./images/fail.png"><?php endif ?>
                        <?php echo PHP_VERSION; ?> <span class="gray fn-f12">必须7.2以上</span>
                    </td>
                    </tr>
                    <tr>
                        <td>pdo</td>
                        <td><?php if (extension_loaded('pdo')): ?>
<img src="./images/right.png" class="yes"><?php else: ?>
<img src="./images/fail.png"><?php endif ?></td>
                    </tr>
                    <tr>
                        <td>pdo_mysql</td>
                        <td><?php if (extension_loaded('pdo_mysql')): ?>
<img src="./images/right.png" class="yes"><?php else: ?>
<img src="./images/fail.png"><?php endif ?></td>
                    </tr>
                    <tr>
                        <td>gd</td>
                        <td><?php if (extension_loaded('gd')): ?>
<img src="./images/right.png" class="yes"><?php else: ?>
<img src="./images/fail.png"><?php endif ?></td>
                    </tr>
                    <tr>
                        <td>dom</td>
                        <td><?php if (extension_loaded('dom')): ?>
<img src="./images/right.png" class="yes"><?php else: ?>
<img src="./images/fail.png"><?php endif ?></td>
                    </tr>
                    <tr class="fn-fb"><td>目录权限</td><td>是否可写</td></tr>
                    <tr>
                        <td>./config</td>
                        <td><?php if (is_writable('../../config')): ?>
<img src="./images/right.png" class="yes"><?php else: ?>
<img src="./images/fail.png"><?php endif ?></td>
                    </tr>
                    <tr>
                        <td>./runtime</td>
                        <td><?php if (is_writable('../../runtime')): ?>
<img src="./images/right.png" class="yes"><?php else: ?>
<img src="./images/fail.png"><?php endif ?></td>
                    </tr>
                    <tr>
                        <td>./public/res</td>
                        <td><?php if (is_writable('../res')): ?>
<img src="./images/right.png" class="yes"><?php else: ?>
<img src="./images/fail.png"><?php endif ?></td>
                    </tr>
                    <tr>
                        <td>./public/static</td>
                        <td><?php if (is_writable('../static')): ?>
<img src="./images/right.png" class="yes"><?php else: ?>
<img src="./images/fail.png"><?php endif ?></td>
                    </tr>
                    <tr>
                        <td>./public/install</td>
                        <td><?php if (is_writable('../install')): ?>
<img src="./images/right.png" class="yes"><?php else: ?>
<img src="./images/fail.png"><?php endif ?></td>
                    </tr>
                    <tr><th colspan="2" class="fn-tac p10"><button type="button" onclick="testClick()" class="button button-violet">同意《用户协议》并开始安装</button></th></tr>
                    <tr><th colspan="2" class="fn-tac p10"><a id="agreement" href="javascript:;">点击查看《用户协议》</a></th></tr>
            </tbody>
            </table>
        </div>
        <div class="col-s20"></div>
    </div>
</div>
<script type="text/javascript">
Do('base','layer','form',function(){
    var area_width = $(document).width() < 860 ? '80%' : '33%';
    $('#agreement').on('click', function () {
        layer.open({type: 2,title: 'SAPI++授权协议',shadeClose: true,shade: 0.6,area: [area_width, '80%'],content: './license.html'});
    })
})
</script>
<script type="text/javascript">
    function testClick(){
        if($('.yes').length != 12){
            layer.alert('您的配置或权限不符合要求');
        }else{
            location.href='index.php?c=create';
        }
    }
</script>
</body>
</html>