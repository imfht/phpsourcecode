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
    <th><a class="btn btn-mini pull-right" href="#fs/refresh" onclick="_plugin_go_beego_cvlist()"><i class="icon-refresh"></i> <?php echo $this->T('Refresh')?></a></th>
    </tr>
</thead>
<tbody>
<?php
//$fs = Directory::listFiles($projPath ."/application/controllers");

$rs2 = lesscreator_fs::FsListAll($projPath ."/views/");
$vs = array();
foreach ($rs2->data as $v) {
    
    if ($v->isdir == 1) {
        continue;
    }

    $ns = strtolower(strstr($v->name, '.', true));
    $vs[] = $v->name;
}

$fs = lesscreator_fs::FsList($projPath ."/controllers");

$trs = array();

foreach ($fs->data as $v) {

    $file = $v->name;

    $rs = lesscreator_fs::FsFileGet($v->path);
    if ($rs->status != 200) {
        continue;
    }

    $trs[$file] = array();

    $pat = array("%(#|;|(//)).*%", "%/\*(?:(?!\*/).)*\*/%s");
    $str = preg_replace($pat, "", $rs->data->body);

    $str = str_replace("\n", "NNN", $str);

    if (preg_match_all('/type\s+(.*?)Controller\s+struct\s+\{/', $str, $mat)) {
        
        foreach ($mat[1] as $v) {
            if (!isset($trs[$file][$v])) {
                $trs[$file][$v] = array();
            }
        }
    }

   
    if (preg_match_all('/\*(.*?)Controller(.*?)\ (Get|Post|Put|Delete|Head|Patch|Options|Finish)\((.*?)\}/', $str, $mat)) {

        foreach ($mat[3] as $k => $v) {

            $ctrl = $mat[1][$k];

            if (!isset($trs[$file][$ctrl])) {
                $trs[$file][$ctrl] = array();
            }

            $trs[$file][$ctrl][$v] = null;
            
            if (isset($mat[4][$k]) && preg_match('/this.TplNames(.*?)\"(.*?)\"NNN/', $mat[4][$k], $mat2)) {

                if (in_array($mat2[2], $vs)) {
                    $trs[$file][$ctrl][$v] = $mat2[2];
                }
            }

        }
    }
}
?>
<tr>

<?php

foreach ($trs as $file => $v) {

    foreach ($v as $ctrl => $v2) {
        echo "<tr>";
        echo "<td><a class=\"badge badge-important rr20fx\" href='#fs/{$file}/{$ctrl}Controller'>{$ctrl}Controller ({$file})</a></td>";
    
        $actions = "";
        $views = "";
        foreach ($v2 as $action => $view) {
            
            $actions .= "<a class='badge badge-info pull-right rr20fx' href='#fs/{$file}/{$ctrl}Controller/{$action}'>{$action}()</a>";

            if ($view == null) {
                $views .= "<a class='badge pull-left tyaery-new' href='#fs/views/".strtolower($ctrl)."-".strtolower($action).".tpl'><i class='icon-plus-sign icon-white'></i>  ".$this->T('New')." View</a>";
            } else {
                $views .= "<a class='badge badge-success pull-left tyaery' href='#fs/views/{$view}'>{$view}</a>";
            }
        }

        echo "<td>{$actions}";
        echo "<a class='badge pull-right rr20fx-new' href='#fs/{$file}/{$ctrl}/new'>
        <i class='icon-plus-sign icon-white'></i> 
        ".$this->T('New')." Action
        </a>";
        echo "</td>";

        echo "<td>{$views}</td>";
        
        echo "<td></td>";
        echo "</tr>";
    }
}
?>

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
    var url = "/lesscreator/plugins/go-beego/fs-ov-controller-new";
    url += "?proj="+ lessSession.Get("ProjPath");

    lessModalOpen(url, 1, 550, 180, tit, null);
});

$(".rr20fx").click(function(event) {

    var uri = $(this).attr("href").split("/");

    var opt = {
        "img": "/lesscreator/static/img/ht-page_white_golang.png",
        "close": "1",
        "editor_strto": uri[2],
    }
    
    if (uri.length == 4) {
        opt.editor_strto = uri[2] +') '+ uri[3];
    }
    //console.log(opt);
    h5cTabOpen('controllers/'+ uri[1],'w0','editor', opt);
});

$(".rr20fx-new").click(function(event) {

    var uri = $(this).attr("href").split("/");

    var tit = "<?php echo $this->T('New')?> Action";
    var url = "/lesscreator/plugins/go-beego/fs-ov-action-new";
    url += "?file="+uri[1]+"&ctl="+ uri[2];
    url += "&proj="+ lessSession.Get("ProjPath");
    //console.log(url);
    lessModalOpen(url, 1, 550, 180, tit, null);
});

$(".tyaery").click(function(event) {

    var uri = $(this).attr("href").substr(4);

    var opt = {
        "img": "/lesscreator/static/img/page_white_world.png",
        "close": "1",
    }
    //console.log(uri);
    h5cTabOpen(uri,'w0','editor', opt);
});

$(".tyaery-new").click(function(event) {

    var uri = $(this).attr("href").split("/");
    //console.log(uri);
    _fs_file_new_modal("file", "/views/", uri[2], 0);
});


</script>
