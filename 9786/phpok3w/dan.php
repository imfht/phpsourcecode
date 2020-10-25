<?
header("Content-Type:text/html;charset=utf-8");
error_reporting(0);
require_once "AppCode/Conn.php";
if( $_POST['action']=="add")
{

    $username = $_POST["username"];;
    $telephone = $_POST["telephone"];;
    $origin = $_POST["origName"];
    $target = $_POST["destName"];
    $remark = $_POST["remark"];
    $addtime = date("Y-m-d H:i:s");


    $sql = "insert into dt_fahuo(username,telephone,origin,target,remark,addtime)";
    $sql .= " value('$username','$telephone','$origin','$target','$remark','$addtime')";


    $con = GetConn();
    if ($con->query($sql)===false)
        echo "提交失败";
    else
        echo "提交成功";
    CloseConn($con);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>物流发货网 - 首页</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" media="screen" href="/wuliu/style.css">
    <link rel="shortcut icon" type="image/png" href="/public/images/favicon.png" />
</head>
<body>
<div class="header">
    <div class="container">
        <ul class="nav-list">
            <li>
                <a href="/">首页</a>
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

<div class="container">
    <div class="index-content">

        <form action="" method="post" class="clearfix" id="searchForm"/>
        <div class="input-wrapper">

            <input type="text" name="username" class="input-text" id="username" />
            <span class="place-holder">用户名</span>
            <input type="text" name="telephone" class="input-text" id="telephone" />
            <span class="place-holder">电话号码</span>
            <input type="hidden" name="origCode" id="origCode"/>
            <input type="text" name="origName" class="input-text" id="origName" />
            <span class="place-holder">始发地</span>
            <input type="hidden" name="destCode" id="destCode"/>
            <span class="place-holder">目的地</span>

            <input type="text" name="destName" class="input-text" id="destName" />

            <textarea  name="remark" class="input-text" style="height: 127px; width: 578px;"></textarea>
            <input type="hidden" name="action" value="add" />
        </div>

        <button type="submit" class="button search">
            提交
        </button>
        </form>
    </div>
</div>



<div class="footer">
    <div class="container">
        <a href="/help">关于我们</a><span class="comment">|</span>
        <a href="/help/contactus">联系我们</a><span class="comment">|</span>
        <a href="/help/disclaimer">免责声明</a>
        <br />
        <span class="comment">copyright&copy;2009-2012</span>
        <span class="comment">版权所有  </span>
        <span class="comment">备案/许可证号： </span>
    </div>
</div>


<script src="/wuliu/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="/wuliu/js/javascripts/global.js" type="text/javascript"></script>
<script src="/wuliu/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="/wuliu/js/additional-methods.js" type="text/javascript"></script>
<script src="/wuliu/js/messages_cn.js" type="text/javascript"></script>
<script src="/wuliu/js/jquery.locationselect.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function() {
        $("#origName").locationselect({
            nameTarget : "#origName",
            codeTarget : "#origCode",
            selectCity : false
        });
        $("#destName").locationselect({
            nameTarget : "#destName",
            codeTarget : "#destCode",
            selectCity : false
        });

        // 登录文本框提示文字
        $("#searchForm .input-text").focus(function() {
            $(this).next().hide();
        }).change(function() {
            if(this.value !== "") {
                $(this).next().hide();
            } else {
                $(this).next().show();
            }
        }).each(function() {
            var self = $(this);
            if (self.val() !== "") {
                self.next().hide();
            }
        });

        $(".place-holder").click(function(e) {
            $(this).hide().prev().click();
            e.stopPropagation();
        });
    });

</script>
<script type="text/javascript">
    var msg = '';
</script>
</body>
</html>