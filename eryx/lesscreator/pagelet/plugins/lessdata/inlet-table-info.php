<?php

use LessPHP\Encoding\Json;


$projPath = lesscreator_proj::path($this->req->proj);
$projInfo = lesscreator_proj::info($this->req->proj);
if (!isset($projInfo['projid'])) {
    die($this->T('Bad Request'));
}

if (!isset($this->req->data) || strlen($this->req->data) == 0) {
    die($this->T('Bad Request'));
}
list($datasetid, $tableid) = explode("/", $this->req->data);

$fsd = $projPath."/lcproj/lessdata/{$datasetid}.ds.json";
$rs = lesscreator_fs::FsFileGet($fsd);
if ($rs->status != 200) {
    die($this->T('Bad Request'));
}
$dataInfo = json_decode($rs->data->body, true);
if ($projInfo['projid'] != $dataInfo['projid']) {
    die($this->T('Access denied'));
}


$fst = $projPath."/lcproj/lessdata/{$datasetid}.{$tableid}.tbl.json";
$rs = lesscreator_fs::FsFileGet($fst);
if ($rs->status != 200) {
    die($this->T('Bad Request'));
}
$tableInfo = json_decode($rs->data->body, true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!isset($this->req->tablename) || strlen($this->req->tablename) < 1) {
        die($this->T('Bad Request'));
    }
    
    if ($this->req->tablename == $tableInfo['tablename']) {
        die("OK");
    }

    $tableInfo['tablename'] = $this->req->tablename;
    $tableInfo['updated']   = time();
    
    lesscreator_fs::FsFilePut($fst, Json::prettyPrint($tableInfo));

    die("OK");
}
?>

<form id="qtv9gs" action="/lesscreator/plugins/lessdata/inlet-table-info">
  <input type="hidden" name="data" value="<?php echo $this->req->data?>" />
  <table width="100%">
    <tr>
        <td width="120px"><strong><?php echo $this->T('DataSet ID')?></strong></td>
        <td><?php echo $tableInfo['datasetid']?></td>
    </tr>
    <tr>
        <td width="120px"><strong><?php echo $this->T('Table ID')?></strong></td>
        <td><?php echo $tableInfo['tableid']?></td>
    </tr>
    <tr>
        <td><strong><?php echo $this->T('Name')?></strong></td>
        <td>
            <div class="c29yan"><?php echo $tableInfo['tablename']?></div>
            <div class="rdqmtg hide"><input type="text" name="tablename"  value="<?php echo $tableInfo['tablename']?>" /></div>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <a href="#edit" class="btn c29yan"><?php echo $this->T('Edit')?></a>
            <input type="submit" class="btn rdqmtg hide" value="<?php echo $this->T('Save')?>" />
        </td>
    </tr>
  </table>
</form>

<script>
var data = '<?php echo $this->req->data?>';

$(".c29yan").click(function(event) {
    $(".c29yan").hide();
    $(".rdqmtg").show();
});

$("#qtv9gs").submit(function(event) {

    event.preventDefault();
    
    //var time = new Date().format("yyyy-MM-dd HH:mm:ss");
    $.ajax({ 
        type    : "POST",
        url     : $(this).attr('action') +"?_="+ Math.random(),
        data    : $(this).serialize() +"&proj="+ projCurrent +"&data="+data,
        success : function(rsp) {
            if (rsp == "OK") {
                hdev_header_alert("alert-success", " OK");
                if (typeof _proj_data_tabopen == 'function') {
                   _proj_data_tabopen('/lesscreator/plugins/lessdata/list?proj='+projCurrent, 1);
                }
                $(".c29yan").show();
                $(".rdqmtg").hide();
            } else {
                hdev_header_alert("alert-error", rsp);
            }
        }
    });
});

</script>
