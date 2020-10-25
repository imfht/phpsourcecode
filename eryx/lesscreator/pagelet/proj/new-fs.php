<?php

$basedir = lesscreator_proj::path("");

if (strlen($this->req->path)) {
    $basedir = $this->req->path;
}

$basedir = rtrim(preg_replace("/\/\/+/", "/", $basedir), '/');


$pathl = trim(strrchr($basedir, '/'), '/');
$paths = explode("/", $basedir);
?>
<style>
a._proj_new_href {
    padding: 3px; width: 100%;
    text-decoration: none;
}
a._proj_new_href:hover {
    background-color: #999;
    color: #fff;
}
a._proj_new_href_click {
    background-color: #0088cc;
    color: #fff;
}
</style>

<ul class="breadcrumb" style="margin:5px 0;">
    <li><a href="javascript:_proj_new_dir('/')"><i class="icon-folder-open"></i></a> <span class="divider">/</span></li>
    <?php
    $sl = '';
    foreach ($paths as $v) {
        if (strlen($v) == 0) {
            continue;
        }
        $sl .= "/{$v}";
        if ($v == $pathl) {
            echo "<li><a href=\"javascript:_proj_new_dir('{$sl}')\">{$v}</a> </li>";
        } else {
            echo "<li><a href=\"javascript:_proj_new_dir('{$sl}')\">{$v}</a> <span class=\"divider\">/</span></li>";
        }
    }
    ?>
</ul>

<div id="_proj_new_dir_body" class="less_scroll" style="border:1px solid #ccc;">
<table width="100%" sclass="table table-condensed">
<?php
foreach (glob($basedir."/*", GLOB_ONLYDIR) as $st) {

  $st = trim(strrchr($st, '/'), '/');
?>
<tr>
  <td valign="middle" width="18">
    <img src="/lesscreator/static/img/folder.png" align="absmiddle" />
  </td>
  <td><a href="#<?php echo $st?>" class="_proj_new_href"><?=$st?></a></td>
</tr>
<?php } ?>
</table>
</div>



<script type="text/javascript">

$('._proj_new_href').dblclick(function() {
    var p = $(this).attr('href').substr(1);
    _basedir = _basedir +'/'+ p;
    _proj_new_dir(_basedir);
});

$('._proj_new_href').click(function() {
    var p = $(this).attr('href').substr(1);   
    p = _basedir +'/'+ p;
    $("._proj_new_basedir").val(p);
    $("._proj_new_basedir_dp").text(p +'/');
});
</script>