<style>

.b9nxc5 .title {
    margin: 0; padding: 2px 0; font-weight: bold; font-size: 16px; line-height: 100%; color: #333333;
}
.b9nxc5 .desc {
    margin: 0; padding: 0; color: #999999; line-height: 100%;
}
.b9nxc5 :hover {
    background-color: #d9edf7;
}
.b9nxc5 > table {
    width: 100%;
}
.b9nxc5 > table td {
    padding: 10px 0;
}
.b9nxc5 tr.line {
    border-top: 1px solid #ccc;
}
</style>

<div class="alert alert-info"><?php echo $this->T('Create Project from Version Control')?></div>

<a class="b9nxc5" href="#proj/fs/vs/git-clone">
<table>
    <tr>
        <td width="64px"><img src="/lesscreator/static/img/vs/git.png" /></td>
        <td >
            <div class="title">Git</div>
            <div class="desc"><?php echo $this->T('Clone a project from a Git repository')?></div>
        </td>
        <td align="right">
            <i class="icon-chevron-right"></i>
        </td>
    </tr>
</table>
</a>

<a class="b9nxc5" href="#">
<table>
    <tr class="line">
        <td width="64px"><img src="/lesscreator/static/img/vs/svn.png" /></td>
        <td >
            <div class="title"><?php echo $this->T('Subversion')?> ( in Development, Currently unavailable <i class="icon-time"></i> )</div>
            <div class="desc"><?php echo $this->T('Checkout a project from a Subversion repository')?></div>
        </td>
        <td align="right">
        </td>
    </tr>
</table>
</a>


<script>

//if (lessModalPrevId() != null) {
    lessModalButtonAdd("kl09d9", "<?php echo $this->T('Back')?>", "lessModalPrev()", "pull-left h5c-marginl0");
//}

lessModalButtonAdd("f2eqa7", "<?php echo $this->T('Close')?>", "lessModalClose()", "");

$(".b9nxc5").click(function(){
        
    var href = $(this).attr('href').substr(1);

    var url = '/lesscreator/';
    var title = "";
    switch (href) {
    case "proj/fs/vs/git-clone":
        url += href;
        title = '<?php echo $this->T('Create Project')?>';
        break;
    default:
        return;
    }
    url += "?basedir="+ lessSession.Get("basedir");

    lessModalNext(url, title, null);
});

function _data_create_resize()
{
    bp = $("#_data_create_body").position();
    fp = $("#_data_create_foo").position();
    $("#_data_create_body").height(fp.top - bp.top);
}
//_data_create_resize();


</script>
