<?php
include(INCLUDES."/header.php");
include(INCLUDES."/nav.php");

$background = ($background == null || $background == "")?
    STORAGE."/backgrounds/default.png":
    STORAGE."/backgrounds/$background";
?>
<div class="main">
    <div class="main-content">
        <div class="page-wraper">
            <div class="page">
            
                <!-- 基本信息 -->
                <div class="pane">
                    <div class="line">
                        <h4>基本信息</h4>
                    </div>
                    <div class="line">
                        <label>用户名</label>
                    </div>
                    <div class="line">
                        <?php echo $name;?>
                    </div>
                </div>
                
                <!-- 修改密码 -->
                <div class="pane">
                    <div class="line">
                        <h4>修改密码</h4>
                    </div>
                    <div class="line">
                        <label>请输入原密码</label>
                    </div>
                    <div class="line">
                        <input class="textbox" type="password" name="old_password" maxlength="18" />
                    </div>
                    <div class="line">
                        <label>请输入新密码</label>
                    </div>
                    <div class="line">
                        <input class="textbox" type="password" name="new_password" maxlength="18" />
                    </div>
                    <div class="line">
                        <label>请再次输入新密码</label>
                    </div>
                    <div class="line">
                        <input class="textbox" type="password" name="confirm_password" maxlength="18" />
                    </div>
                    <div class="line">
                        <button class="button" id="btn-changepassword">修改</button>
                    </div>
                </div>
                
                <!-- 更换背景 -->
                <div class="pane">
                    <div class="line">
                        <h4>更换背景</h4>
                    </div>
                    <div class="line">
                        <form action="index.php?fun=execChangeBackground" method="post" enctype="multipart/form-data">
                            <input type="file" name="background_file" />
                            <button class="button">更换</button>
                        </form>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="sidebar"></div>
    </div>
</div>

<!-- 脚本区域 -->
<script src="<?php echo INCLUDES."/user_me.js"; ?>"></script>
<input type="hidden" name="background" value="<?php echo $background; ?>" />

<?php
include(INCLUDES."/footer.php");
?>