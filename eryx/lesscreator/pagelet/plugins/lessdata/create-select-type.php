
<style>

.j4soeo .title {
    margin: 0; padding: 2px 0; font-weight: bold; font-size: 16px; line-height: 100%; color: #333333;
}
.j4soeo .rowlogo {
    width: 48px; height: 48px;
}
.j4soeo .rtset {
    width: 20px; height; 20px;
}
.j4soeo .desc {
    margin: 0; padding: 0; color: #999999; line-height: 100%;
}
.j4soeo :hover {
    background-color: #d9edf7;
}
.j4soeo > table {
    width: 100%;
}
.j4soeo > table td {
    padding: 5px;
}
.j4soeo tr.line {
    border-top: 1px solid #ccc;
}
.j4soeo .imggray {
    -webkit-filter: grayscale(1);
}
.j4soeo .indev {
    color: #dc4437; font-weight: normal;
}
</style>
<div class="alert alert-info"><?php echo $this->T('Select the type of DataSet')?></div>

<a class="j4soeo" href="#create-aliyun-rds">
<table>
    <tr>
        <td width="80px"><img class="rowlogo" src="/lesscreator/static/img/plugins/lessdata/aliyun-rds.png" /></td>
        <td >
            <?php echo $this->T('Aliyun Relational Database Service')?>
        </td>
        <td align="right">
            <i class="icon-chevron-right"></i>
        </td>
    </tr>
</table>
</a>

<a class="j4soeo" href="#create-mysql">
<table width="100%" class="h5c_dialog_listview">
    <tr class="line">
        <td width="80px"><img class="rowlogo" src="/lesscreator/static/img/plugins/lessdata/mysql.png" /></td>
        <td >
            <?php echo $this->T('MySQL Relational Database')?>
        </td>
        <td align="right">
            <i class="icon-chevron-right"></i>
        </td>
    </tr>
</table>
</a>

<a class="j4soeo" href="#create-ts">
<table width="100%" class="h5c_dialog_listview">
    <tr class="line">
        <td width="80px"><img class="rowlogo" src="/lesscreator/static/img/plugins/lessdata/memcache.png" /></td>
        <td >
            <?php echo $this->T('Memcache Database')?> <span class="label label-important"><?php echo $this->T('todo-wait-desc')?></span>
        </td>
        <td align="right">
        </td>
    </tr>
</table>
</a>

<a class="j4soeo" href="#create-ts">
<table width="100%" class="h5c_dialog_listview">
    <tr class="line">
        <td width="80px"><img class="rowlogo" src="/lesscreator/static/img/plugins/lessdata/aliyun-ots.png" /></td>
        <td >
            <?php echo $this->T('Aliyun Open Table Service')?> <span class="label label-important"><?php echo $this->T('todo-wait-desc')?></span>
        </td>
        <td align="right">
        </td>
    </tr>
</table>
</a>

<a class="j4soeo" href="#create-ts">
<table width="100%" class="h5c_dialog_listview">
    <tr class="line">
        <td width="80px"><img class="rowlogo" src="/lesscreator/static/img/plugins/lessdata/aliyun-ocs.png" /></td>
        <td >
            <?php echo $this->T('Aliyun Open Cache Service')?> <span class="label label-important"><?php echo $this->T('todo-wait-desc')?></span>
        </td>
        <td align="right">
        </td>
    </tr>
</table>
</a>

<script>

lessModalButtonAdd("doo8l6", "<?php echo $this->T('Close')?>", "lessModalClose()", "");


$(".j4soeo").click(function(){
        
    var href = $(this).attr('href').substr(1);

    var url = '/lesscreator/plugins/lessdata/';
    var title = "";
    var type = 2;
    switch (href) {
    case "create-aliyun-rds":
        url += 'create-rds';
        title = '<?php echo $this->T('New DataSet')?> - Aliyun RDS';
        type = 3;
        break;
    case "create-mysql":
        url += 'create-rds';
        title = '<?php echo $this->T('New DataSet')?> - MySQL';
        type = 2;
        break;
    default:
        return;
    }
    url += '?proj='+ projCurrent +'&datasettype='+ type;

    lessModalNext(url, title, null);
});

</script>
