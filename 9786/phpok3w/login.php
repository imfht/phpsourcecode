<!DOCTYPE html>
<html>
<head>
    <title>物流发货网 - 用户登录</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" media="screen" href="/wuliu/style.css">
</head>
<body>
<div class="header">
    <div class="container">
        <ul class="nav-list">
            <li>
                <a href="/">首页</a>
            </li>
            <li>
                <a href="/mobile-app">手机客户端</a>
            </li>
        </ul>
        <div class="top-tools">
            <a href="/login" class="">登录</a>
            <a href="/register" class="">免费注册</a>
        </div>
    </div>
</div>
<div class="top-tipbar" style="display:none;">
    <span class="tip-content"></span>
    <a href="#" class="close" title="关闭">╳</a>
</div>


<div class="banner">
    <div class="container">
        <div class="logo"></div>
        <div class="separator"></div>
        <h1>会员登录</h1>
    </div>
</div>
<div class="container">
    <div class="content login">
        <div class="login-form">
            <form action="" method="post" id="loginForm">
                <input type="hidden" name="randomId" id="randomId" value="1e96059b-8db1-4496-a487-843874c054c1" />
                <p class="error-message login-indent">

                </p>
                <ul>
                    <li>
                        <label for="login-username">用户名</label>
                        <input type="text" name="username" class="login-text" id="username"/>
                    </li>
                    <li>
                        <label for="login-password">密码</label>
                        <input type="password" name="password" class="login-text" id="password"/>
                    </li>
                    <li>
                        <label for="login-captcha">验证码</label>
                        <input type="text" name="captcha" class="login-text captcha" id="captcha" maxlength="4"/>
                        <img class="captcha-img" src="/captcha.png" title="不区分大小写。看不清点击可换一个" alt="验证码" />
                    </li>
                    <li class="login-indent">
                        <button class="button submit large" type="submit">
                            登录
                        </button>
                        <a class="retrieve-pass" href="/member/retrievePwd1">找回密码</a>
                    </li>
                </ul>
            </form>
            <div class="signup">
                <span>还没有账号？</span>
                <a href="/register.php">立即免费注册</a>
            </div>
        </div>
    </div>
</div>        <div class="footer">
    <div class="container">
        <a href="/help">关于物流发货网</a><span class="comment">|</span>
        <a href="/help/contactus">联系我们</a><span class="comment">|</span>
        <a href="/help/disclaimer">免责声明</a>
        <br />
        <span class="comment">copyright&copy;2009-2012</span>
        <span class="comment">版权所有 山东大成软件有限公司</span>
        <span class="comment">备案/许可证号：鲁ICP备10200028号-2</span>
    </div>
</div>

<script src="/wuliu/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="/wuliu/js/global.js" type="text/javascript"></script>
<script src="/wuliu/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="/wuliu/js/additional-methods.js" type="text/javascript"></script>
<script src="/wuliu/js/messages_cn.js" type="text/javascript"></script>
<script src="/wuliu/js/login.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function() {
        $(".captcha-img").click(function(e) {
            this.src = "/captcha?id=1e96059b-8db1-4496-a487-843874c054c1" + "&token=" + Math.random();
        });
        $("#username")[0].focus();
    });

</script>
<script type="text/javascript">
    var msg = '';
</script>
</body>
</html>