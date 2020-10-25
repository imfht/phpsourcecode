<style>

.j4soeo .title {
    margin: 0; padding: 2px 0; font-weight: bold; font-size: 16px; line-height: 100%; color: #333333;
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
    padding: 10px 10px 10px 0;
}
.j4soeo tr.line {
    border-top: 1px solid #ccc;
}
</style>


<a class="j4soeo" href="#project/new">
<table>
    <tr>
        <td width="64px"><img src="/lesscreator/~/lesscreator/img/proj/proj-new0.png" /></td>
        <td >
            <div class="title">{{T . "New Project"}}</div>
            <div class="desc">{{T . "Start a project in a new directory"}}</div>
        </td>
        <td align="right">
            <i class="icon-chevron-right"></i>
        </td>
    </tr>
</table>
</a>

<a class="j4soeo" href="#project/open-recent">
<table>
    <tr class="line">
        <td width="64px"><img src="/lesscreator/~/lesscreator/img/proj/proj-new1.png" /></td>
        <td >
            <div class="title">{{T . "Existing Directory"}}</div>
            <div class="desc">{{T . "Open a project from an existing working directory"}}</div>
        </td>
        <td align="right">
            <i class="icon-chevron-right"></i>
        </td>
    </tr>
</table>
</a>

<!-- <a class="j4soeo" href="#project/fs/vs/well">
<table>
    <tr class="line">
        <td width="64px"><img src="/lesscreator/~/lesscreator/img/vs/git-100.png" /></td>
        <td >
            <div class="title">{{T . "Version Control"}}</div>
            <div class="desc">{{T . "Checkout a project from a version control repository"}}</div> 
        </td>
        <td align="right">
            <i class="icon-chevron-right"></i>
        </td>
    </tr>
</table>
</a> -->

<script>

lessModalButtonAdd("ctw26z", "{{T . "Close"}}", "lessModalClose()", "");

$(".j4soeo").click(function(){

    var href = $(this).attr('href').substr(1);

    var url = '/lesscreator/';
    var title = "";
    switch (href) {
    case "project/new":
        url += href;
        title = '{{T . "Create New Project"}}';
        break;
    case "project/open-recent":
        url += href;
        title = '{{T . "Open Project"}}';
        break;
    // case "project/fs/vs/well":
    //     url += href;
    //     title = '{{T . "Create Project"}}';
    //     break;
    default:
        return;
    }

    lessModalNext(url, title, null);
});

function _data_create_resize()
{
    bp = $("#_data_create_body").position();
    fp = $("#_data_create_foo").position();
    $("#_data_create_body").height(fp.top - bp.top);
}
//_data_create_resize();
// lcWebTerminal(1); // TODO

</script>
