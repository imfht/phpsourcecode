<?php

?>
<div class="h5c_tab_subnav" style="border-bottom: 1px solid #ddd;">
    <!-- <a href="javascript:h5cPluginDataOpen()" class="b0hmqb">
        <img src="/lesscreator/~/fam3/icons/folder.png" class="h5c_icon" />
        Open 
    </a> -->
    <a href="#dataset/new" class="btn h6tnb9" style="margin-left:5px;">
        <img src="/lesscreator/~/fam3/icons/database_add.png" class="h5c_icon" />
        <?php echo $this->T('New DataSet')?>
    </a>
</div>

<div id="ig3w6o" class="less_scroll" style="padding-top:10px;"></div>

<script type="text/javascript">

$(".h6tnb9").click(function(){
    var url = "/lesscreator/plugins/lessdata/create-select-type?proj="+ projCurrent;
    lessModalOpen(url, 1, 700, 450, '<?php echo $this->T('New DataSet')?>', null);
});

function _proj_data_tabopen(uri, force)
{
    if (!$("#ig3w6o").length) {
        return;
    }

    if (force != 1 && $("#ig3w6o").html() && $("#ig3w6o").html().length > 1) {
        $("#ig3w6o").empty();
        return;
    }
    
    $.ajax({
        type    : "GET",
        url     : uri,
        success : function(data) {
            $("#ig3w6o").html(data);
            lcLayoutResize();
        }
    });
}
_proj_data_tabopen('/lesscreator/plugins/lessdata/list?proj='+ projCurrent, 1);
</script>
