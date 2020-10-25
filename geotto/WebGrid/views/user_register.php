<?php
include(INCLUDES."/header.php");
include(INCLUDES."/nav.php");
?>
<div class="main">
    <div class="main-content">
        <!-- 页面主体 -->
        <div class="page-wraper">
            <div class="page">
                <!-- 注册表单 -->
                <form role="form">
                    <div class="form-group">
                        <label>名称</label>
                        <input class="form-control" type="text" name="name" maxlength="64" />
                    </div>
                    <div class="form-group">
                        <label>密码</label>
                        <input class="form-control" type="password" name="password" maxlength="16" />
                    </div>
                    <div class="form-group">
                        <label>确认密码</label>
                        <input class="form-control" type="password" name="confirm_password" maxlength="16" />
                    </div>
                </form>
                <div class="line">
                    <div class="button" id="register">注册</div>
                </div>
            </div>
        </div>
        <!-- 侧边栏 -->
        <div class="sidebar">
            <p>尊敬的用户，欢迎您使用WebGrid。在注册之前请先浏览我们注册条款，一旦注册成功即表示您接收注册条款的约束，祝您体验愉快！</p>
        </div>
    </div>
</div>

<!-- 脚本区域 -->
<script src="<?php echo INCLUDES."/user_register.js"; ?>"></script>

<?php
include(INCLUDES."/footer.php");
?>
