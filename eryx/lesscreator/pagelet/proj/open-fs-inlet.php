<?php

$path = "/";

if (strlen($this->req->path)) {
    $path = $this->req->path;
}

$path = preg_replace("/\/\/+/", "/", $path);
$path = rtrim($path, '/');

$pathl = trim(strrchr($path, '/'), '/');
$paths = explode("/", $path);
?>

<ul class="breadcrumb" style="margin:5px 0;">
    <li>
        <a href="javascript:_proj_open_fs_inlet('/', 1)">
            <i class="icon-folder-open"></i>
        </a> 
        <span class="divider">/</span>
    </li>
    <?php
    $sl = '';
    foreach ($paths as $v) {
        if (strlen($v) == 0) {
            continue;
        }
        $sl .= "/{$v}";
        if ($v == $pathl) {
            echo "<li><a href=\"javascript:_proj_open_fs_inlet('{$sl}', 1)\">{$v}</a> </li>";
        } else {
            echo "<li><a href=\"javascript:_proj_open_fs_inlet('{$sl}', 1)\">{$v}</a> <span class=\"divider\">/</span></li>";
        }
    }
    ?>
</ul>

<div id="_proj_fs_body" class="less_scroll">
<table width="100%" sclass="table table-condensed">
<?php
$rs = lesscreator_fs::FsList($path."/*");
//echo "<pre>";
//print_r($rs);
//echo "</pre>";
$dirFound = 0;
foreach ($rs->data as $v) {

    if ($v->isdir != 1) {
        continue;
    }

    $dirFound++;

?>
<tr>
  <td valign="middle" width="18">
    <img src="/lesscreator/static/img/folder.png" align="absmiddle" />
  </td>
  <td><a href="#<?php echo $v->name?>" class="_proj_open_fs_href"><?=$v->name?></a></td>
</tr>
<?php
}
if ($dirFound == 0) {
    echo "<tr><td>". sprintf($this->T('`%s` Not Found'), $this->T('Directory'))."</td></tr>";
}
?>
</table>
</div>
<script type="text/javascript">
$('._proj_open_fs_href').dblclick(function() {
    p = $(this).attr('href').substr(1);
    _proj_open_fs(_path +'/'+ p, 1);
    lessModalButtonClean("phtswc");
});

$('._proj_open_fs_href').click(function() {

    _path_click = $(this).attr('href').substr(1);

    $('._proj_open_fs_href').removeClass('_proj_open_fs_href_click');
    $(this).addClass('_proj_open_fs_href_click');
    
    lessModalButtonAdd("phtswc", "<?php echo $this->T('Open Project')?>", "_proj_open_fs_open()", "pull-left btn-inverse");
});
</script>
