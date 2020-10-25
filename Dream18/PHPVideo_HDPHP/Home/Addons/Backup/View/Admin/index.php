<include file="__PUBLIC__/header"/>
<body>
<div class="hd-menu-list">
    <ul>
        <li><a href="{|addon_url:'backup',array('app'=>'Addon')}">备份数据</a></li>
        <li class="active"><a href="{|addon_url:'index',array('app'=>'Addon')}">备份列表</a></li>
    </ul>
</div>
<form action="{|addon_url:'delBackupDir'}" method="post">
    <table class="hd-table hd-table-list hd-form">
        <thead>
        <tr>
            <td>备份目录</td>
            <td>备份时间</td>
            <td>大小</td>
            <td class="hd-w80">操作</td>
        </tr>
        </thead>
        <tbody>
        <list from="$dir" name="$d">
            <tr>
                <td>{$d.filename}</td>
                <td>{$d.filemtime|date:'Y-m-d h:i:s',@@}</td>
                <td>{$d.size|get_size}</td>
                <td>
                    <a href="javascript:" onclick="confirm('确定还原吗？')?location.href='{|addon_url:'recovery',array('dir'=>$d['filename'])}':false;">还原</a>
                    |
                    <a href="javascript:del('{$d.filename}')">删除</a>
                </td>
            </tr>
        </list>
        </tbody>
    </table>
</form>
<script>
    //删除目录
    function del(dir) {
        hd_modal({
            width: 400,//宽度
            height: 200,//高度
            title: '提示',//标题
            content: '确定删除吗',//提示信息
            button: true,//显示按钮
            button_success: "确定",//确定按钮文字
            button_cancel: "关闭",//关闭按钮文字
            timeout: 0,//自动关闭时间 0：不自动关闭
            shade: true,//背景遮罩
            shadeOpacity: 0.1,//背景透明度
            success: function () {//点击确定后的事件
                hd_ajax('{|addon_url:"del"}', {dir: dir}, '__URL__');
            }
        });
    }
</script>
</body>
</html>