<?php
    $appid = 'wxb6d74fdd670c8910';
    $appsecret = '1429a684d8c8407c684c29f404d4c379';
    $scope = 'snsapi_userinfo';
    $proxy_scope = '';
    $oldQuaryString = $_SERVER['QUERY_STRING'];
    parse_str($oldQuaryString, $quaryStringArr);
    $ignoreKey = array('code', 'state', 'access_token', 'openid');
    foreach ($quaryStringArr as $key=>$value) {
        if (in_array($key, $ignoreKey)) unset($quaryStringArr[$key]);
    }
    $newQuaryString = http_build_query($quaryStringArr);
    $redirect_uri = urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $newQuaryString);
    $code = '';
    isset($_GET['code']) && $code = $_GET['code'];
    $access_token = '';
    isset($_GET['access_token']) && $access_token = $_GET['access_token'];
    $openid = '';
    isset($_GET['openid']) && $openid = $_GET['openid'];

    $resinfo = '暂无结果';

    if (!empty($code)) {
        $resinfo = '获取成功!<br><br>code = ' . $code;
    }

    if (!empty($access_token)) {
        $userinfo = json_decode(http_request('https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN'),true);
        $resinfo = '获取成功!<br><br>access_token = ' . $access_token . '<br><br>openid = ' . $openid;
        $resinfo .= '<br><br>nickname = ' . $userinfo['nickname'];
        $resinfo .= '<br><br>sex = ' . $userinfo['sex'];
        $resinfo .= '<br><br>province = ' . $userinfo['province'];
        $resinfo .= '<br><br>city = ' . $userinfo['city'];
        $resinfo .= '<br><br>country = ' . $userinfo['country'];
        $resinfo .= '<br><br>headimgurl = ' . $userinfo['headimgurl'];
    }

    function http_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>微信授权登录代理案例页</title>
    <link rel="shortcut icon" href="./image/favicon.ico">
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        .wrapper {
            width: 100vw;
            height: 100vh;
            position: relative;
            background: #eee;
            overflow: auto;
            padding-bottom: 18vw;
            box-sizing: border-box;
        }

        .btn-frame {
            width: 100%;
            position: fixed;
            left: 0;
            bottom: 0;
            z-index: 100;
            text-align: center;
            font-size: 0;
            background: #fff;
        }

        .code-btn,.accesstoken-btn {
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            display: inline-block;
            vertical-align: top;
            width: 44vw;
            font-size: 4vw;
            line-height: 3;
            margin: 3vw 2vw;
            background: #36a82e;
            text-align: center;
            color: #fff;
            border-radius: 1vw;
            cursor: pointer;
        }

        .qrcode-frame {
            width: 100%;
            text-align: center;
        }
        .qrcode-frame img{
            width: 60%;
        }
        .qrcode-frame p{
            font-size: 3.5vw;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .info-frame {
            width: 100%;
            padding: 4vw;
            box-sizing: border-box;
        }
        .info-title {
            width: 100%;
            color: #36a82e;
            font-size: 3.5vw;
            font-weight: bold;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .info-content {
            width: 100%;
            height: auto;
            min-height: 40vw;
            background: #fff;
            margin: 2vw 0;
            box-sizing: border-box;
            padding: 2vw;
            font-size: 3.5vw;
            overflow: auto;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="info-frame">
            <p class="info-title">—— 结果信息 ——</p>
            <div class="info-content">
                <?php echo $resinfo; ?>
            </div>
        </div>
        <div class="qrcode-frame">
            <img src="./image/qrcode.jpeg" alt="">
            <p>（长按识别二维码关注测试号）</p>
        </div>
    </div>
    <div class="btn-frame">
        <div class="code-btn">获取code</div>
        <div class="accesstoken-btn">获取access_token</div>
    </div>
    
    <script>
        var appid = "<?php echo $appid ?>",
            appsecret = "<?php echo $appsecret ?>",
            scope = "<?php echo $scope ?>",
            proxy_scope = "<?php $proxy_scope = 'code'; echo $proxy_scope ?>",
            redirect_uri = "<?php echo $redirect_uri ?>",
            code = "<?php echo $code ?>",
            access_token = "<?php echo $access_token ?>",
            openid = "<?php echo $openid ?>",
            proxy_domain = "wxoauth.hillpy.com";

        var codeUrl = "http://" + proxy_domain + "/index.php?app_id=" + appid + "&scope=" + scope + "&proxy_scope=" + proxy_scope + "&redirect_uri=" + redirect_uri;
            proxy_scope = "<?php $proxy_scope = 'access_token'; echo $proxy_scope ?>";
        var accessTokenUrl = "http://" + proxy_domain + "/index.php?app_id=" + appid + "&scope=" + scope + "&proxy_scope=" + proxy_scope + "&app_secret=" + appsecret + "&redirect_uri=" + redirect_uri;

        var codeBtn = document.getElementsByClassName('code-btn')[0],
            accessTokenBtn = document.getElementsByClassName('accesstoken-btn')[0];

        codeBtn.addEventListener('click', function() {
            window.location.href = codeUrl;
        });
        accessTokenBtn.addEventListener('click', function() {
            window.location.href = accessTokenUrl;
        });
    </script>
</body>
</html>