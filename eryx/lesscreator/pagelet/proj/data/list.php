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

    $globsub = $projPath."/data/{$json['id']}.*.tbl.json"; // TODO
    
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
            <a href='#{$k}' class='g2hvtz' title='{$v['name']}'>
            {$v['name']}
            </a>
            <em>{$typename}</em>
        </td>
        <td align='right'> 
            <span>
            <img src='/fam3/icons/table_add.png' class='h5c_icon' /> 
            <a href='#{$k}' class='weovcr'>New Table</a>
            </span>
        </td>
        <td width='5px'></td>
    </tr>";

    foreach ($v['_tables'] as $v2) {
        if (!isset($v2['tablename']) || !$v2['tablename']) {
            $v2['tablename'] = $v2['tableid'];
        }
        echo "<tr>
        <td></td>
        <td></td>
        <td>
            <img src='/fam3/icons/database_table.png' class='h5c_icon' /> 
            <a href='#{$k}/{$v2['tableid']}' class='p9532p' title='{$v2['tablename']}'>
            {$v2['tablename']}
            </a>
        </td>
        <td align='right'><a href='#{$k}/{$v2['tableid']}' class='p9532p' title='{$v2['tablename']}'>Open</a></td>
        <td></td>
        </tr>";
    }
}
echo "</table>";
?>

<script type="text/javascript">

$('.g2hvtz').click(function() {
    var uri = $(this).attr('href').substr(1);
    var url = "/lesscreator/data/dataset-set?proj="+projCurrent+"&id="+uri;
    lessModalOpen(url, 0, 500, 350, "DataSet Setting", null);
});

$('.weovcr').click(function() {
    var uri = $(this).attr('href').substr(1);
    var url = "/lesscreator/data/table-new?proj="+projCurrent+"&id="+uri;
    lessModalOpen(url, 0, 400, 260, "New Table", null);
});

$('.p9532p').click(function() {
    var uri = $(this).attr('href').substr(1);
    var tit = $(this).attr('title');
    var url = "/lesscreator/data/inlet-table?proj="+projCurrent+"&data="+uri;
    h5cTabOpen(url, 'w0', 'html', {'title': tit, 'close':'1', 'img': '/fam3/icons/database_table.png'});
});

</script>
