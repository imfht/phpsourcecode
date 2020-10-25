<?php

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

$fsdt = $projPath."/lcproj/lessdata/{$datasetid}.{$tableid}.tbl.json";
$rs = lesscreator_fs::FsFileGet($fsdt);
if ($rs->status != 200) {
    die($this->T('Bad Request'));
}
$tableInfo = json_decode($rs->data->body, true);

?>

<div style="padding:0px;">

<div style="padding:10px; background-color:#f6f7f8;">
    <span>
        <strong><?php echo $this->T('Table Settings')?></strong>: <?php echo $tableInfo['tablename']?>
    </span>
</div>

<ul class="h5c_navtabs fc4exa" style="background-color:#f6f7f8;">
  <li class="active">
    <a href="#plugins/lessdata/inlet-table-info" class="sk79ve"><?php echo $this->T('Overview')?></a>
  </li>
  <li><a href="#plugins/lessdata/inlet-table-schema" class="sk79ve"><?php echo $this->T('Structure')?></a></li>
</ul>

<div id="vey476" style="padding:10px;"></div>

</div>

<script>
var data = '<?php echo $this->req->data?>';

$('.sk79ve').click(function() {    
    
    url = $(this).attr('href').substr(1);
    _data_inlet_open("/lesscreator/"+url);

    $(".fc4exa li.active").removeClass("active");
    $(this).parent().addClass("active");
});

function _data_inlet_open(url)
{
    $.ajax({
        url     : url +"?proj="+ projCurrent +"&data="+ data,
        type    : "GET",
        timeout : 30000,
        success : function(rsp) {            
            $("#vey476").empty().html(rsp);
            if (typeof _proj_data_tabopen == 'function') {
                _proj_data_tabopen('/lesscreator/plugins/lessdata/list?proj='+projCurrent, 1);
            }
        },
        error: function(xhr, textStatus, error) {
            alert("ERROR:"+ xhr.responseText);
            hdev_header_alert('error', xhr.responseText);
        }
    });
}

function _data_inlet_schema_edit()
{
    _data_inlet_open("/lesscreator/plugins/lessdata/inlet-table-schema-set");
}

_data_inlet_open("/lesscreator/plugins/lessdata/inlet-table-info");
</script>
