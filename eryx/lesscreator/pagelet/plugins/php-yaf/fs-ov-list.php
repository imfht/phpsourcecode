<?php

use LessPHP\Util\Directory;
    
$info = lesscreator_proj::info($this->req->proj);
if (!isset($info['projid'])) {
    die($this->T('Bad Request'));
}
$projPath = lesscreator_proj::path($this->req->proj);

?>

<style>
.rpmzfe {
    width: auto;
}
.rpmzfe td {
    min-width: 200px;
    line-height: 30px;
}
.rpmzfe .badge {
    margin: 5px 0; padding: 3px 10px;
    font-style: 18px;
    clear: both; 
    font-family: monospace !important;
}
.rpmzfe .badge:hover {
    background-color: #f5b400;
}
.rpmzfe a.btn {
    margin: 0;  
}
</style>

<table width="" class="table rpmzfe">
<thead>
    <tr>
    <th>Controller</th>
    <th><span class="pull-right">Action</span></th>
    <th>View</th>
    <th><a class="btn btn-mini pull-right" href="#fs/refresh" onclick="_plugin_yaf_cvlist()"><i class="icon-refresh"></i> <?php echo $this->T('Refresh')?></a></th>
    </tr>
</thead>
<tbody>
<?php
//$fs = Directory::listFiles($projPath ."/application/controllers");
$fs = lesscreator_fs::FsList($projPath ."/application/controllers");
foreach ($fs->data as $v) {
    

    $file = $v->name;


    //$file = trim($file, "/");
    //$rs = lesscreator_fs::FsFileGet($projPath ."/application/controllers/". $file);
    $rs = lesscreator_fs::FsFileGet($v->path);
    if ($rs->status != 200) {
        continue;
    }

    echo "<tr>";
    echo "<td><a class=\"badge badge-important rr20fx\" href='#fs/{$file}/'>$file</a></td>";

    $ctln = strtolower(strstr($file, '.', true));

    $rs2 = lesscreator_fs::FsList($projPath ."/application/views/". $ctln);
    
    $vs = array();
    foreach ($rs2->data as $v) {
        $ns = strtolower(strstr($v->name, '.', true));
        $vs[] = $ns;
    }
    echo "<td>";

    $pat = array("%(#|;|(//)).*%", "%/\*(?:(?!\*/).)*\*/%s");
    $str = preg_replace($pat, "", $rs->data->body);

    $vs2 = array();
    if (preg_match_all('/function(.*?)\s(.*?)Action/', $str, $mat)) {

        foreach ($mat[2] as $v) {

            echo "<a class='badge badge-info pull-right rr20fx' href='#fs/{$file}/{$v}'>{$v}Action</a>";

            if (in_array($v, $vs)) {
                $vs2[$v] = $v;
            } else {
                $vs2[$v] = null;
            }
        }
    }

    echo "<a class='badge pull-right rr20fx-new' href='#fs/{$file}/new'>
        <i class='icon-plus-sign icon-white'></i> 
        ".$this->T('New')." Action
        </a>";
    echo "</td>";

    echo "<td>";
    foreach ($vs2 as $k => $v) {

        $k = strtolower(substr($k, 0, 1)) . substr($k, 1);

        if ($v == null) {
            echo "<a class='badge pull-left tyaery-new' href='#fs/{$ctln}/{$k}.phtml'><i class='icon-plus-sign icon-white'></i>  ".$this->T('New')." View</a>";
        } else {
            echo "<a class='badge badge-success pull-left tyaery' href='#fs/{$ctln}/{$v}.phtml'>{$v}.phtml</a>";
        }
    }
    echo "</td>";
    echo "<td></td>";
    echo "</tr>";

    //echo $file;
}
?>
<tr>
<td>
    <a class='badge rcifxb-new' href='#fs/new'>
        <i class='icon-plus-sign icon-white'></i> 
        <?php echo $this->T('New')?> Controller
    </a>
</td>
<td></td>
<td></td>
<td></td>
</tr>
</tbody>
</table>

<script type="text/javascript">

$(".rcifxb-new").click(function(event) {

    var tit = "<?php echo $this->T('New')?> Controller";
    var url = "/lesscreator/plugins/php-yaf/fs-ov-controller-new";
    url += "?proj="+ lessSession.Get("ProjPath");

    lessModalOpen(url, 1, 550, 180, tit, null);
});

$(".rr20fx").click(function(event) {

    var uri = $(this).attr("href").split("/");

    var opt = {
        "img": "/lesscreator/static/img/page_white_php.png",
        "close": "1",
        "editor_strto": uri[2],
    }

    h5cTabOpen('application/controllers/'+ uri[1],'w0','editor', opt);
});

$(".rr20fx-new").click(function(event) {

    var uri = $(this).attr("href").split("/");


    var tit = "<?php echo $this->T('New')?> Action";
    var url = "/lesscreator/plugins/php-yaf/fs-ov-action-new";
    url += "?ctl="+ uri[1];
    url += "&proj="+ lessSession.Get("ProjPath");


    lessModalOpen(url, 1, 550, 180, tit, null);
});

$(".tyaery").click(function(event) {

    var uri = $(this).attr("href").split("/");

    var opt = {
        "img": "/lesscreator/static/img/page_white_world.png",
        "close": "1",
    }

    h5cTabOpen('application/views/'+ uri[1] +"/"+ uri[2],'w0','editor', opt);
});

$(".tyaery-new").click(function(event) {

    var uri = $(this).attr("href").split("/");

    _fs_file_new_modal("file", "/application/views/"+ uri[1], uri[2], 1);
});


</script>
