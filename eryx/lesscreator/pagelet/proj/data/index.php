<?php

?>
<div class="h5c_tab_subnav" style="border-bottom: 1px solid #ddd;">
    <!-- <a href="javascript:h5cPluginDataOpen()" class="b0hmqb">
        <img src="/fam3/icons/folder.png" class="h5c_icon" />
        Open 
    </a> -->
    <a href="#data/new" class="h6tnb9">
        <img src="/fam3/icons/database_add.png" class="h5c_icon" />
        New DataSet
    </a>
</div>

<div id="ig3w6o" class="less_scroll" style="padding-top:10px;"></div>

<script type="text/javascript">

$(".h6tnb9").click(function(){
    var url = "/lesscreator/data/create-select-type?proj="+ projCurrent;
    lessModalOpen(url, 0, 700, 450, 'New DataSet', null);
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
_proj_data_tabopen('/lesscreator/proj/data/list?proj='+ projCurrent, 1);
</script>
