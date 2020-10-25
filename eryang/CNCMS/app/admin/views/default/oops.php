<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
    <meta charset="utf-8"/>
    <title><?php echo $title; ?></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,IE=8,IE=9,chrome=1">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta name="keywords" content="菜鸟CMS"/>
    <meta name="description" content="菜鸟CMS">
    <meta name="author" content="二阳">

    <link rel="shortcut icon" href="/assets/admin/default/img/favicon.ico">

    <style type=text/css>
        html,body,div,p,span,h3,ol,li {
            padding: 0;
            margin: 0;
        }
        ol,li{
            list-style-type: none;
            list-style-image: none;
        }

        html,body,.main {
            height: 100%;
        }
        .main {
            height: 100%;
            font-family: "微软雅黑", "宋体";
            background: #e8e8e8;
        }

        .con {
            margin: 0 auto;
            width: 565px;
        }

        .result {
            padding:110px 0 80px 0;
            background: #008ead;
        }

        .result h3 {
            border-bottom: #fff 1px solid;
            padding-bottom: 10px;
            width: 100%;
            margin-bottom: 10px;
            color: #fff;
            font-size: 22px;
            font-weight: bold;
        }
        .result div {
            color: #e8e8e8;
        }

        .result p {
            font-size: 18px;
        }

        .result ol {
            padding-left: 25px;
        }

        .result ol li {
            padding-top: 20px;
            list-style-type: decimal;
            font-size: 16px;
        }

        .explain {
            padding: 20px 0;
            background: #e2e2e2;
            color: #666;
            font-size: 16px;
        }

        .explain p{
            margin-top: 10px;
        }

    </style>


    <script src="/assets/admin/default/js/jquery-1.7.2.min.js"></script>

    <script type="text/javascript">

        (function () {
            var ie = !-[1,];
            if(!ie){
                location.href = "<?php echo site_url($this -> config -> item('admin_folder').'login/oops'); ?>";
            }
            if (navigator.userAgent.indexOf("MSIE 8.") > 0) {
                location.href = "<?php echo site_url($this -> config -> item('admin_folder').'login/oops'); ?>";
            }
            if (navigator.userAgent.indexOf("MSIE 9.") > 0) {
                location.href = "<?php echo site_url($this -> config -> item('admin_folder').'login/oops'); ?>";
            }
            if (navigator.userAgent.indexOf("MSIE 10.") > 0) {
                location.href = "<?php echo site_url($this -> config -> item('admin_folder').'login/oops'); ?>";
            }
            if (navigator.userAgent.indexOf("MSIE 11.") > 0) {
                location.href = "<?php echo site_url($this -> config -> item('admin_folder').'login/oops'); ?>";
            }
        })();

    </script>

</head>
<body>

<div class=main>
    <div class=result>
        <div  style="text-align: center;padding: 10px 0 20px 0;">
            <img src="/assets/admin/default/img/oops/ie-old.png" alt="ie-old" style="width:70px;height:70px;">
        </div>
        <div class=con>
            <h3>Oops! 您使用的浏览器版本过于陈旧，我们无法支持！</h3>
            <h4>建议您使用以下浏览器（点击图标下载）</h4>
        </div>

        <div style="text-align: center;margin-top:50px;margin-bottom: 30px;padding-left: 20px;">
            <a href="http://download.firefox.com.cn/releases/webins3.0/official/zh-CN/Firefox-latest.exe"
               style="margin:10px;width:70px;height:70px;">
                <img src="/assets/admin/default/img/oops/firefox.png" title="火狐浏览器" alt="firefox"
                     style="width:70px;height:70px;"> <span>firefox</span>
            </a>
            <a href="https://www.google.com/intl/zh-CN/chrome/browser/?standalone=1"
               style="margin:10px;width:70px;height:70px;">
                <img src="/assets/admin/default/img/oops/Chrome.png" title="谷歌浏览器" alt="chrome"
                     style="width:70px;height:70px;"> <span>Chrome</span>
            </a>
            <a href="http://www.apple.com/safari/download/" style="margin:10px;width:70px;height:70px;">
                <img src="/assets/admin/default/img/oops/Safari.png" title="safari" alt="safari"
                     style="width:70px;height:70px;"> <span>Safari</span>
            </a>
            <a href="http://windows.microsoft.com/zh-CN/internet-explorer/products/ie/home"
               style="margin:10px;width:70px;height:70px;">
                <img src="/assets/admin/default/img/oops/ie9.png" title="ie"alt="ie" style="width:70px;height:70px;">
                <span>更高版本的IE</span>
            </a>
        </div>

        <div class=con>
            <div>
                <p>亲爱的朋友们：</p>
                <ol>
                    <li>我们网站采集用比较先进的技术，相对而言更加简洁、美观、安全</li>
                    <li>微软也鼓励升级，会有更多更炫的特效，给你带来不一般的视觉体验</li>
                    <li>我们不支持ie6浏览器，支持所有非IE内核的浏览器</li>
                    <li>国内浏览器请切换成极速模式或者高速模式</li>
                </ol>
            </div>
        </div>

    </div>

    <div class=explain>
        <div class=con>
            <h3 style="text-align: left;">以下浏览器我们完美支持：</h3>
            <p>1. <a href="http://down.360safe.com/se/360se6_setup.exe">360安全浏览器</a>、<a
                    href="http://down.360safe.com/cse/360cse_7.5.3.126.exe">360极速浏览器</a> &nbsp;&nbsp;<a
                    href="http://www.360.cn/" target="_blank">去官网免费下载</a></p>

            <p>2. <a href="http://download.ie.sogou.com/se/sogou_explorer_4.2_0321.exe">搜狗高速浏览器</a> <a
                    href="http://ie.sogou.com/" target="_blank">去官网免费下载</a></p>

            <p>
                3. <a
                    href="http://wap.uc.cn/index.php?action=PackageDown&do=ByPfid&product=UCBrowser&pfid=101&lang=zh-cn&bid=999&direct=true&from=web_banner">UC浏览器</a>
                &nbsp;&nbsp;<a href="http://www.uc.cn/" target="_blank">去官网免费下载</a></p>

            <p>4. <a href="http://dl.maxthon.cn/mx4/mx4.3.2.1000cn.exe">遨游云浏览器</a>&nbsp;&nbsp; <a href="http://www.maxthon.cn/"
                                                                                                  target="_blank">去官网免费下载</a>
            </p>

            <p>5. <a href="http://cd001.www.duba.net/duba/install/2011/ever/kavsetup140113_99_53.exe">猎豹浏览器</a> &nbsp;&nbsp;<a
                    href="http://www.liebao.cn/" target="_blank">去官网免费下载</a></p>

            <p>6. <a href="http://dl.client.baidu.com/union/getbdbrowser.php?tn=newbdie">百度浏览器</a>&nbsp;&nbsp; <a
                    href="http://liulanqi.baidu.com/" target="_blank">去官网免费下载</a></p>

            <p>
                7. <a
                    href="http://download.browser.taobaocdn.com/client/browser/down.php?spm=0.0.0.0.mBVRqx&pid=0080_1000">淘宝浏览器</a>
                &nbsp;&nbsp;<a href="http://browser.taobao.com/" target="_blank">去官网免费下载</a></p>
        </div>
    </div>

</div>
</body>
</html>