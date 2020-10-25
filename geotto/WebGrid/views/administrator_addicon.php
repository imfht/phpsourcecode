<?php
include(INCLUDES."/header.php");
include(INCLUDES."/nav.php");
?>

<div class="main">
    <div class="main-content">
        <div class="page-wraper">
            <div class="page">
                
                <!-- 显示错误 -->
                <div class="pane">
                    <?php
                    if(isset($errors)){
                            foreach($errors as $error){
                                echo "<div class=\"text-warning\">$error</div>";
                            }
                    }
                    ?>
                </div>
                
                <!-- 表单 -->
                <form action="index.php?file=administrator_controller&class=AdministratorController&fun=addIcon" method="post" enctype="multipart/form-data">
                    <div class="line">
                        <label>图标名称</label>
                    </div>
                    <div class="line">
                        <input class="textbox" type="text" name="icon_name" value="<?php echo isset($_POST['icon_name'])?$_POST['icon_name']:""; ?>" maxlength="64" />
                    </div>
                    <div class="line">
                        <label>选择图标</label>
                    </div>
                    <div class="line">
                        <input type="file" name="icon_file" />
                    </div>
                    <div class="line">
                        <input type="hidden" name="submitted" value="TRUE" />
                        <button class="button">上传</button>
                    </div>
                </form>
                
            </div>
        </div>
        <div class="sidebar"></div>
    </div>
</div>
<?php
include(INCLUDES."/footer.php");
?>