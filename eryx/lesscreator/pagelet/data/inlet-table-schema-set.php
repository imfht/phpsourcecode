<?php

use LessPHP\Encoding\Json;


$projPath = lesscreator_proj::path($this->req->proj);
$projInfo = lesscreator_proj::info($this->req->proj);
if (!isset($projInfo['projid'])) {
    die($this->T('Bad Request'));
}

if (!isset($this->req->data) || strlen($this->req->data) == 0) {
    die("The instance does not exist");
}
list($datasetid, $tableid) = explode("/", $this->req->data);

$fsd = $projPath."/data/{$datasetid}.ds.json";
$rs = lesscreator_fs::FsFileGet($fsd);
if ($rs->status != 200) {
    die($this->T('Bad Request'));
}
$dataInfo = json_decode($rs->data->body, true);
if ($projInfo['projid'] != $dataInfo['projid']) {
    die("Permission denied");
}

$fst = $projPath."/data/{$datasetid}.{$tableid}.tbl.json";
$rs = lesscreator_fs::FsFileGet($fst);
if ($rs->status != 200) {
    die($this->T('Bad Request'));
}
$tableInfo = json_decode($rs->data->body, true);

$fieldtypes = array(
    'int' => 'Integer',
    'uint' => 'Integer - Unsigned',
    'uinti' => 'Integer - Auto Increment',
    'varchar' => 'Varchar',
    'string' => 'Text',
    'timestamp' => 'Unix Timestamp',
    //'blob' => 'blob',
);

$fieldidxs = array(
    '0' => '---',
    '1' => 'Index',
    '2' => 'Primary',
    '3' => 'Unique',
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $schema = array();
    foreach ($this->req->fsname as $k => $v) {

        $v = str_replace(':', '_', $v);
        
        if (strlen($v) == 0) {
            continue;
        }

        //if ($v == "id") {
        //    $this->req->fstype[$k] = 'varchar';
        //}
        
        if (in_array($this->req->fstype[$k], array('int', 'uint', 'uinti', 'varchar'))
            && $this->req->fslen[$k] == 0) {
            die("`$v` Can not be null");
        }
        
        if (!isset($this->req->fsidx[$k])) {
            $this->req->fsidx[$k] = 0;
        }
        
        $schema[] = array(
            'name'  => "{$v}",
            'type'  => "{$this->req->fstype[$k]}",
            'len'   => "{$this->req->fslen[$k]}",
            'idx'   => "{$this->req->fsidx[$k]}",
        );
    }

    $tableInfo['schema']  = $schema;
    $tableInfo['updated'] = time();
    
    lesscreator_fs::FsFilePut($fst, Json::prettyPrint($tableInfo));
    
    die("OK");
}
?>

<form id="bhw2j1" action="/lesscreator/data/inlet-table-schema-set">
    
<input type="hidden" name="data" value="<?php echo $this->req->data?>" />

<table class="table table-condensed" width="100%">
<thead>
    <tr>
        <th>Name</th>
        <th>Type</th>
        <th>Length (varchar、integer)</th>
        <th>Index</th>
        <th>Option</th>
    </tr>
</thead>
<tbody id="field_list">
    <?php
    foreach ($tableInfo['schema'] as $v) {
        $checked = '';
        if ($v['idx'] == 1) {
            $checked = 'checked';
        }
        ?>
        <tr>
            <td>
                <input name="fsname[<?php echo $v['name']?>]" type="text" value="<?php echo $v['name'] ?>" class="input-medium"/>
            </td>
            <td>
                <select name="fstype[<?php echo $v['name']?>]" class="input-large">
                <?php
                foreach ($fieldtypes as $k2 => $v2) {
                    $select = $v['type'] == $k2 ? 'selected' : '';
                    echo "<option value='{$k2}' {$select}>{$v2}</option>";
                }
                ?>
                </select>
            </td>
            <td>
                <input name="fslen[<?php echo $v['name']?>]" type="text" value="<?php echo  $v['len'] ?>" class="input-mini"/>
            </td>
            <td>
                <select name="fsidx[<?php echo $v['name']?>]" class="input-medium">
                <?php
                foreach ($fieldidxs as $k2 => $v2) {
                    $idxSelected = $v['idx'] == $k2 ? 'selected' : '';
                    echo "<option value='{$k2}' {$idxSelected}>{$v2}</option>"; 
                }
                ?>
                </select>
            </td>
            <td>
                <a href="javascript:void(0)" onclick="_data_field_del(this)">Delete</a>
            </td>
        </tr>
        <?php
    }
    ?>            
</tbody>
</table>

<input type="submit" class="btn" value="<?php echo $this->T('Save')?>" />
<a href="javascript:_data_field_append()" >New Field</a>

</form>

<script>

var data = '<?php echo $this->req->data?>';

function _data_field_del(field)
{
    $(field).parent().parent().remove();
}

function _data_field_append()
{
    sid = Math.random() * 1000000000;
    
    entry = '<tr> \
      <td><input name="fsname['+sid+']" type="text" value="" class="input-medium"/></td> \
      <td> \
        <select name="fstype['+sid+']" class="input-medium"> \
        <?php
        foreach ($fieldtypes as $k => $v) {
            echo "<option value=\"{$k}\">{$v}</option> \\\n";
        }
        ?>
        </select> \
      </td> \
      <td><input name="fslen['+sid+']" type="text" value="" class="input-mini"/></td>\
      <td><input name="fsidx['+sid+']" type="checkbox" value="1" /> </td>\
      <td><a href="javascript:void(0)" onclick="_data_field_del(this)">Delete</a></td> \
    </tr>';
    $("#field_list").append(entry);
}

$("#bhw2j1").submit(function(event) {

    event.preventDefault();
    
    var fs = $('input[name^="fsname"]');
    fs.each(function (i,f) {
        var fn = $(f).val();
        var reg = /^[a-zA-Z][a-zA-Z0-9_:]+$/; 
        if(!reg.test(fn)){
            hdev_header_alert("alert-error", fn+" is invalid");
            return;
        }
    });

    var time = new Date().format("yyyy-MM-dd HH:mm:ss");   
    $.ajax({ 
        type    : "POST",
        url     : $(this).attr('action') +"?_="+ Math.random(),
        data    : $(this).serialize() +'&proj='+ projCurrent,
        success : function(rsp) {
            if (rsp == "OK") {
                hdev_header_alert("alert-success", time +" OK");
            } else {
                alert(rsp);
                hdev_header_alert("alert-error", time +" "+ rsp);
            }
        }
    });
});

</script>
