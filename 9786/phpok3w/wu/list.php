<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
<?php
header("Content-Type:text/html;charset=utf-8");
require_once "../AppCode/Conn.php";
?>

<style type="text/css">
    table.gridtable {
        font-family: verdana, arial, sans-serif;
        font-size: 11px;
        color: #333333;
        border-width: 1px;
        border-color: #666666;
        border-collapse: collapse;
    }

    table.gridtable th {
        border-width: 1px;
        padding: 8px;
        border-style: solid;
        border-color: #666666;
        background-color: #dedede;
    }

    table.gridtable td {
        border-width: 1px;
        padding: 8px;
        border-style: solid;
        border-color: #666666;
        background-color: #ffffff;
    }
</style>

<!-- Table goes in the document BODY -->
<table class="gridtable">
    <tr>
        <th>编号</th>
        <th>用户名</th>
        <th>电话</th>
        <th>始发地</th>
        <th>目的地</th>
        <th>备注</th>

        <th>添加日期</th>
        <th>添加IP</th>
        <th>状态</th>
        <th>操作</th>
    </tr>

    <?php
    $Sql="select id,username,telephone,origin,target,remark,addtime,ip from dt_fahuo ";
    $mysqli=GetConn();
    $result=$mysqli->query($Sql);
    if ($result) {
        while($Rs =$result->fetch_array() ){
?>
    <tr>
        <td><?=$Rs["id"]?></td>
        <td><?=$Rs["username"]?></td>
        <td><?=$Rs["telephone"]?></td>
        <td><?=$Rs["origin"]?></td>
        <td><?=$Rs["target"]?></td>
        <td><?=$Rs["remark"]?></td>
        <td><?=$Rs["addtime"]?></td>
        <td>已审核</td>
        <td><?=$Rs["id"]?></td>
        <td>审核,删除</td>
    </tr>
<?php
        }

    }
?>
</table>
<script type="application/javascript">
    function pause(id) {
        art.dialog({
            content : '您确定要暂停吗？',
            icon : 'question',
            lock : true,
            ok : function() {
                $.ajax({
                    url : "/User/pauseuser",
                    type : "POST",
                    data : {
                        "userId" : id
                    },
                    cathe : false,
                    dataType : "json",
                    success : function(data) {
                        if (data.status = "true") {
                            msgok(data.Msg);
                            window.parent.location.reload();
                        } else {
                            msgwarn(data.Msg);
                        }
                    }
                });
            },
            cancelVal : '取消',
            cancel : true
        });
    }

    function del(id) {
        art.dialog({
            content : '您确定要删除吗？',
            icon : 'question',
            lock : true,
            ok : function() {
                $.ajax({
                    url : "/User/userdel",
                    type : "POST",
                    data : {
                        "userId" : id
                    },
                    cathe : false,
                    dataType : "json",
                    success : function(data) {
                        if (data.status == "true") {
                            msgok(data.Msg);
                            window.parent.location.reload();
                        } else {
                            msgwarn(data.Msg);
                        }
                    }
                });
            },
            cancelVal : '取消',
            cancel : true
        });
    }

</script>


</body>
</html>
