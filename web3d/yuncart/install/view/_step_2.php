<form action="index.php?step=3" method="post" name="submitform" id="submitform" onsubmit="return checkform()">
    <div class="install">
        <h1>系统配置</h1>
        <?php if ($errors) { ?>
            <div class="installerr">
                <ul>
                    <?php foreach ($errors as $error) { ?>
                        <li><?php echo $error ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } else { ?>
            <div class="installipt">
                <p>
                    <label><em>*</em>数据库驱动：</label>
                    <select name="driver" class="left">
                        <option value="pdo">pdo</option>
                    </select>
                </p>
                <p>
                    <label><em>*</em>数据库主机：</label>
                    <input class="short-input2" name="dbhost" type="text" value="localhost" />
                    <span class="left" style="margin-left:5px;">安装有问题？<a href="http://help.yuncart.com" target="_blank">请查看文档</a></span>
                </p>
                <p>
                    <label><em>*</em>数据库端口号：</label>
                    <input class="short-input2" name="dbport" type="text" value="3306"	/>
                </p>
                <p>
                    <label><em>*</em>数据库用户名：</label>
                    <input class="short-input2" name="dbuser" type="text" id="dbuser"	onfocus="focusipt(this)"/>
                </p>
                <p>
                    <label>数据库密码：</label>
                    <input class="short-input2" name="dbpass" type="text" id="dbpass"	onfocus="focusipt(this)"/>
                </p>
                <p>
                    <label><em>*</em>数据库名：</label>
                    <input class="short-input2" name="dbname" type="text" id="dbname"	onfocus="focusipt(this)"/>
                </p>
                <p>
                    <label><em>*</em>数据库表前缀：</label>
                    <input class="short-input2" name="dbprefix" type="text" value="cart_"/>
                </p>
            </div>

            <div class="installipt">
                <p>
                    <label><em>*</em>商城名称：</label>
                    <input class="short-input2" name="mallname" type="text" id="mallname" onfocus="focusipt(this)" value="我的网店"/>
                </p>
                <p>
                    <label><em>*</em>管理员帐号：</label>
                    <input class="short-input2" name="adminname" type="text" id="adminname" onfocus="focusipt(this)"/>
                </p>
                <p>
                    <label><em>*</em>管理员密码：</label>
                    <input class="short-input2" name="adminpass" type="text" id="adminpass" onfocus="focusipt(this)"/>
                </p>
                <p>
                    <label><em>*</em>确认密码：</label>
                    <input class="short-input2" name="adminpass2" type="text" id="adminpass2" onfocus="focusipt(this)"/>
                </p>
                <p>
                    <label><em>*</em>Email：</label>
                    <input class="short-input2" name="email" type="text" id="email" onfocus="focusipt(this)" value="admin@admin.com"/>
                </p>
                <p>
                    <label><em>*</em>后台入口：</label>
                    <input class="short-input2" name="adminfile" type="text" id="adminfile" onfocus="focusipt(this)" value="admin"
                           onkeyup="checkkey()"
                           />
                    <span class="left">（安全考虑，请修改并牢记，格式只允许英文，数字，区分大小写）</span>
                </p>
                <p>
                    <label>后台地址：</label>
                    <span class="left" id="adminloc"></span>
                </p>
                <p>
                    <label><em>*</em>安装体验数据：</label>
                    <input type="checkbox" value="1" name="test" checked class="short-check"/>
                </p>
            </div>
            <div class="btn">
                <input type="submit" value="填完了，下一步" id="next" />
            </div>
        <?php } ?>
    </div>
</form>
<script type="text/javascript">
    function checkkey() {
        var value = $("#adminfile").val();
        if (!/^[0-9a-zA-Z]+$/.test(value)) {
            value = value.replace(/[^0-9a-zA-Z]/g, '');
            $("#adminfile").val(value);
        }
        $("#adminloc").text(value + ".php");
    }

    function checkform() {
        var $obj = $("#dbuser");
        if ($.trim($obj.val()) == "") {
            $obj.addClass("border");
            return false;
        }

        $obj = $("#dbname");
        if ($.trim($obj.val()) == "") {
            $obj.addClass("border");
            return false;
        }

        $obj = $("#mallname");
        if ($.trim($obj.val()) == "") {
            $obj.addClass("border");
            return false;
        }

        $obj = $("#adminname");
        if ($.trim($obj.val()) == "") {
            $obj.addClass("border");
            return false;
        }

        $obj = $("#adminpass");
        if ($obj.val().length < 2) {
            $obj.addClass("border");
            return false;
        }

        $obj2 = $("#adminpass2");
        if ($obj.val() != $obj2.val()) {
            $obj2.addClass("border");
            return false;
        }

        $obj = $("#adminfile");
        var adminfile = $obj.val();
        if ($.trim(adminfile) == "" || !/^[a-zA-Z0-9]+$/.test(adminfile)) {
            $obj.addClass("border");
            return false;
        }

        $obj = $("#email");
        if ($obj.val() == "") {
            $obj.addClass("border");
            return false;
        }
        return true;
    }
    function focusipt(obj) {
        var $obj = $(obj);
        $obj.removeClass("border");
    }
</script>
