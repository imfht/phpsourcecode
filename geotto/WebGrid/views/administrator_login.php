<?php
include(INCLUDES."/header.php");
include(INCLUDES."/nav.php");
?>
<div class="main">
    <div class="main-content">
        <div class="page-wraper">
            <div class="page">
                <!-- 显示错误 -->
                <?php
                if(isset($errors)){
                    foreach($errors as $error){
                        echo "<div class=\"text-warning\">$error</div>";
                    }
                }
                ?>
            <form action="index.php?file=administrator_controller&class=AdministratorController&fun=login" method="post">
                <div class="line">
                    <label>管理员名称</label>
                </div>
                <div class="line">
                    <input class="textbox" id="txt-name" type="text" name="name" value="<?php echo isset($_POST['name'])?$_POST['name']:"" ?>" maxlength="64" />
                </div><div class="line">
                    <label>密码</label>
                </div>
                <div class="line">
                    <input class="textbox" id="txt-password" type="password" name="password" maxlength="18" />
                </div>
                <div class="line">
                    <button class="button">登录</button>
                </div>
                <input type="hidden" name="submitted" value="TURE" />
            </form>
            </div>
        </div>
    </div>
    <div class="sidebar"></div>
</div>

<!-- 脚本区域 -->
<link href="<?php echo INCLUDES."/administrator_login.css"; ?>" rel="stylesheet" />
<?php
include(INCLUDES."/footer.php");
?>