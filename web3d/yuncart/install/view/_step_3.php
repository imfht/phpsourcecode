<div class="install">
    <h1>安装</h1>
    <div class="installipt">
        <?php if ($insret) { ?>
            <p class="psucc">安装成功
                <input type="button" value="删除安装目录" id="delins"/>
            </p>
            <p class="pview">
                <a href="../<?php echo $adminfile; ?>.php" target="_blank">访问后台</a>
                <a href="../index.php" target="_blank">访问前台</a>
            </p>
            <script type="text/javascript">
                $("#delins").click(function () {
                    var $this = $(this);
                    $this.prop("disabled", true);
                    $.post("index.php?step=4", {}, function (data) {
                        if (data == "success") {
                            $this.val("删除安装目录成功");
                        } else {
                            alert("删除安装目录失败，请检查目录权限");
                            $this.prop("disabled", false);
                        }
                    })
                });
            </script>
        <?php } else { ?>
            <p class="perr"><?php echo $errors ?></p>
        <?php } ?>
    </div>
</div>