<?php

use LessPHP\Encoding\Json;

$basedir = $this->req->basedir;

$pjc = $basedir .'/conf/lesscreator/projlist.json';


if ($this->req->func == 'del') {

    $rs = lesscreator_fs::FsFileGet($pjc);
    if ($rs->status != 200) {
        die(sprintf($this->T('`%s` not exist'), $pjc));
    }

    $pjs = json_decode($rs->data->body, true);
    if (!is_array($pjs)) {
        $pjs = array();
    }

    if (isset($pjs[$this->req->projid])) {
        unset($pjs[$this->req->projid]);
        $pjs = Json::prettyPrint($pjs);
        $rs = lesscreator_fs::FsFilePut($pjc, $pjs);
        if ($rs->status !== 200) {
            die($rs->message);
        }
    }

    die("OK");
}
?>
<ul class="nav nav-tabs" style="margin: 5px 0;">
  <li class="active">
    <a href="#"><?php echo $this->T('Recent Projects')?></a>
  </li>
  <li>
    <a href="javascript:_proj_open_fs('', 0)"><?php echo $this->T('From Directory')?></a>
  </li>
</ul>


<table id="_proj_open_recent" width="100%" class="table table-condensed">

<?php

$pjs = lesscreator_fs::FsFileGet($pjc);
$pjs = json_decode($pjs->data->body, true);
if (!is_array($pjs)) {
    $pjs = array();
}
foreach ($pjs as $projid => $val) {

    $noinfo = "";

    $rs = lesscreator_fs::FsFileGet($val['path']."/lcproject.json");
    if ($rs->status != 200) {
        $noinfo = '<font color="red">'. sprintf($this->T('The `%s` no longer exists'), $this->T('Project')) .'</font>';
    }
?>
<tr id="_proj_<?php echo $projid?>">
  <td valign="middle" width="18">
    <img src="/lesscreator/static/img/app-t3-16.png" align="absmiddle" />
  </td>
  <td>
    <strong><a href="javascript:_proj_open_recent_open('<?=$val['path']?>')"><?=$val['name']?></a></strong>
    <font color="gray">( <?=$val['path']?> ) <?=$noinfo?></font>
  </td>
  <td align="right">
    <button type="button" class="close" title="Clean out" onclick="_proj_open_recent_del('<?php echo $projid?>')">&times;</button>
  </td>
</tr>
<?php
}
?>
</table>

<script type="text/javascript">
if (lessModalPrevId() != null) {
    lessModalButtonAdd("jwyztd", "<?php echo $this->T('Back')?>", "lessModalPrev()", "pull-left h5c-marginl0");
}

function _proj_open_recent_open(path)
{
    h5cProjectOpen(path);
    lessModalClose();
}


function _proj_open_recent()
{
    var url = "/lesscreator/proj/open-recent?basedir="+ lessSession.Get("basedir");

    if (lessModalPrevId() == lessCryptoMd5("modal"+url)) {
        lessModalPrev();
    } else {
        lessModalNext(url, "<?php echo $this->T('Open Project')?>", null);
    }
}

function _proj_open_fs(path, force)
{
    var url = "/lesscreator/proj/open-fs";
    url += "?basedir="+ lessSession.Get("basedir");

    if (lessModalPrevId() == lessCryptoMd5("modal"+url)) {
        lessModalPrev();
    } else {
        lessModalNext(url, "<?php echo $this->T('Open Project')?>", null);
    }
}

function _proj_open_recent_del(projid)
{
    $.ajax({
        type: "POST",
        url: '/lesscreator/proj/open-recent?basedir='+ lessSession.Get("basedir"),
        data: {'func':'del', 'projid':projid},
        success: function(data) {
            if (data == "OK") {
                $("#_proj_"+ projid).remove();
            } else {
                alert(data);
            }
        }
    });
}
</script>
