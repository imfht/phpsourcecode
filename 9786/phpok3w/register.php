<!DOCTYPE html>
<html>
<head>
    <title>物流发货网 - 用户注册</title>
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
        <h1>用户注册</h1>
    </div>
</div>
<div class="container">
    <div class="content signup">
        <form method="post" id="registerForm">
            <ul>
                <li>
                    <label for="signup-username"><span class="required">*</span>用户名</label>
                    <input type="text" class="signup-text" name="username" id="username" value=""/>
                    <span class="error-message"></span>
                    <p class="info-message">
                        长度2-10位，字母、数字或下划线的组合、汉字。
                    </p>
                </li>
                <li>
                    <label for="signup-password"><span class="required">*</span>密码</label>
                    <input type="password" class="signup-text" name="password" value="" id="password"/>
                    <span class="error-message"></span>
                    <p class="info-message">
                        6-16个包含英文字母、数字和下划线的字符，密码不能为纯数字
                    </p>
                </li>
                <li>
                    <label for="signup-confirmPass"><span class="required">*</span>确认密码</label>
                    <input type="password" class="signup-text" name="repassword" value="" id="repassword"/>
                    <span class="error-message"></span>
                </li>
                <li>
                    <label for="signup-mobilephone"><span class="required">*</span>邮箱</label>
                    <input type="text" class="signup-text" name="email" id="email" value=""/>
                    <span class="error-message"></span>
                </li>
                <li>
                    <label for="signup-captcha"><span class="required">*</span>验证码</label>
                    <input type="text" class="signup-validate-text" name="captcha" id="captcha" size="5" maxlength="4"/>
                    <img class="captcha-register-img" src="/captcha.png" title="不区分大小写。看不清点击可换一个" alt="验证码" />
                    <span class="error-message"></span>
                </li>
                <li class="signup-indent">
                    <input type="checkbox" checked="checked" id="approve"/>同意
                    <label for="signup-agree">已阅读并同意</label>
                    <a href="/application/regclause" target="_blank">《物流发货网服务条款》</a>
                </li>
                <li class="signup-indent">
                    <button type="button" class="button submit large" onclick="submitForm();">
                        立即注册
                    </button>
                </li>
            </ul>
        </form>

        <div class="sidebar-right">
            <p class="tips">已有账号？请直接登录</p>
            <a href="/login" class="button highlight large">马上登录</a>
            <img src="/wuliu/images/img-signup.png" alt="用户注册">
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
<input type="hidden" name="authenticityToken" value="e629d09cadf39072b67815f93230e86ab2bcc348">
<script src="/wuliu/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="/wuliu/js/global.js" type="text/javascript"></script>
<script src="/wuliu/js/RegisterApp/register.js" type="text/javascript"></script>
<script src="/wuliu/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="/wuliu/js/additional-methods.js" type="text/javascript"></script>
<script src="/wuliu/js/messages_cn.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function() {
        $(".captcha-register-img").click(function() {
            this.src = "/captcha?id=dbb75798-9ea6-4343-9777-b23bf9d8904f" + "&token=" + Math.random();
        });

        $("#username")[0].focus();
    });
    // 客户端验证变量函数定义
    var checkAction = function(randomId) {
        var url = "/checkValidateCode";
        return url + "?randomId=" + randomId;
    }
</script>
<script type="text/javascript">
    var msg = '';
</script>
</body>
</html>