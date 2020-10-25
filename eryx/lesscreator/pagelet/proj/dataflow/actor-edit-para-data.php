<?php

if ($this->req->proj == null) {
    die($this->T('Internal Error'));
}
$proj = preg_replace("/\/+/", "/", rtrim($this->req->proj, '/'));

$projPath = lesscreator_proj::path($this->req->proj);
$projInfo = lesscreator_proj::info($this->req->proj);

$datasets = array();

$glob = $projPath."/data/*.ds.json";

foreach (glob($glob) as $v) {
    
    $json = lesscreator_fs::FsFileGet($v);
    $json = json_decode($json->data->body, true);
    
    if (!isset($json['id'])) {
        continue;
    }

    if ($projInfo['projid'] != $json['projid']) {
        continue;
    }

    $datasets[$json['id']] = $json;
    $datasets[$json['id']]['_tables'] = array();

    $globsub = $projPath."/data/{$json['id']}.*.tbl.json";
    
    foreach (glob($globsub) as $v2) {
        
        $json2 = lesscreator_fs::FsFileGet($v2);
        $json2 = json_decode($json2->data->body, true);
    
        if (!isset($json2['tableid'])) {
            continue;
        }

        $datasets[$json['id']]['_tables'][] = $json2;
    }
}

echo "<table width=\"100%\" class='table-hover'>";
foreach ($datasets as $k => $v) {
    
    if ($v['type'] == 1) {
        $typename = 'BigTable';
    } else if ($v['type'] == 2) {
        $typename = 'Relational Database';
    }

    echo "<tr>
        <td width='5px'></td>
        <td width='20px'>
            <img src='/fam3/icons/database.png' class='h5c_icon' /> 
        </td>
        <td>
            {$v['name']} <em>{$typename}</em>
        </td>
        <td></td>
        <td width='5px'></td>
    </tr>";

    foreach ($v['_tables'] as $v2) {
        echo "<tr>
        <td></td>
        <td></td>
        <td>
            <img src='/fam3/icons/database_table.png' class='h5c_icon' /> 
            {$v2['tablename']} <em>({$v2['tableid']})</em>
        </td>
        <td align='right'><a href='#{$v2['datasetid']}_{$v2['tableid']}' class='a5ypb6' title='{$v2['tablename']}'>Select</a></td>
        <td></td>
        </tr>";
    }
}
echo "</table>";
?>

<script type="text/javascript">

$('.a5ypb6').click(function() {
    var uri = $(this).attr('href').substr(1);
    var tit = $(this).attr('title');
    $('input[name=para_data]').val(uri);
    $('input[name=para_data_display]').val(tit);
    lessModalClose();
});

</script>
