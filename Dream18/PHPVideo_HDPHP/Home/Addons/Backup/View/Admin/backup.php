<include file="__PUBLIC__/header"/>
<body>
<div class="hd-menu-list">
    <ul>
        <li class="active"><a href="{|addon_url:'backup',array('app'=>'Addon')}">备份数据</a></li>
        <li><a href="{|addon_url:'index',array('app'=>'Addon')}">备份列表</a></li>
    </ul>
</div>
<form action="{|addon_url:'backup_db',array('app'=>'Addon')}" method="post">
    <table class="hd-table hd-table-form hd-form">
        <thead>
        <tr>
            <td width="50">数据备份</td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <table class="hd-table hd-table-form" width="100%">
                    <tr>
                        <td class="hd-w100">分卷大小</td>
                        <td>
                            <input type="text" class="hd-w150" name="size" value="200"/> KB
                        </td>
                    </tr>
                    <tr>
                        <td class="hd-w100">&nbsp;</td>
                        <td>
                            <input type="submit" class="hd-btn" value="开始备份"/>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
    <table class="hd-table hd-table-list hd-form" id="table_list">
        <thead>
        <tr>
            <td class="hd-w50">
                <label><input type="checkbox" class="s_all_ck" checked=""/> 全选</label>
            </td>
            <td>表名</td>
            <td>类型</td>
            <td>编码</td>
            <td>记录数</td>
            <td>使用空间</td>
            <td>碎片</td>
            <td class="hd-w60">操作</td>
        </tr>
        </thead>
        <tbody>
        <list from="$table.table" name="$t">
            <tr>
                <td>
                    <input type="checkbox" name="table[]" value="{$t.tablename}" checked=""/>
                </td>
                <td>{$t.tablename}</td>
                <td>{$t.engine}</td>
                <td>{$t.charset}</td>
                <td>{$t.rows}</td>
                <td>{$t.size}</td>
                <td>{$t.data_free|default:0}</td>
                <td>
                    <a href="javascript:hd_ajax('{|addon_url:optimize}',{table:['{$t.tablename}']},'__URL__')">优化</a> |
                    <a href="javascript:hd_ajax('{|addon_url:repair}',{table:['{$t.tablename}']},'__URL__')">修复</a>
                </td>
            </tr>
        </list>
        </tbody>
    </table>
</form>
<input type="button" class="hd-btn hd-btn-sm" onclick="optimize()" value="批量优化"/>
<input type="button" class="hd-btn hd-btn-sm" onclick="repair()" value="批量修复"/>
<script>
    //全选与反选
    $(".s_all_ck").click(function () {
        $("[name='table[]']").attr("checked", !!$(this).attr("checked"));
    })

    //检查有没有选择备份目录
    function check_select_table() {
        if ($("[name*='table']:checked").length == 0) {
            alert("你还没有选择表");
            return false;
        }
        return true;
    }

    //优化表
    function optimize() {
        if (check_select_table()) {
            hd_ajax('{|addon_url:"optimize"}', $("[name*='table']:checked").serialize());
        }
    }
    //修复表
    function repair() {
        if (check_select_table()) {
            hd_ajax('{|addon_url:"repair"}', $("[name*='table']:checked").serialize());
        }
    }
</script>
</body>
</html>