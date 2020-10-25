<?php

if ($this->req->proj == null) {
    die($this->T('Internal Error'));
}
$proj = preg_replace("/\/+/", "/", rtrim($this->req->proj, '/'));

$projPath = lesscreator_proj::path($this->req->proj);
$projInfo = lesscreator_proj::info($this->req->proj);


$datasets = array();

$glob = $projPath."/lcproj/lessdata/*.ds.json";

$rs = lesscreator_fs::FsList($glob);

foreach ($rs->data as $v) {
    
    $v = $v->path;

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

    $globsub = $projPath."/lcproj/lessdata/{$json['id']}.*.tbl.json";
    $rs2 = lesscreator_fs::FsList($globsub);
    
    foreach ($rs2->data as $v2) {

        $v2 = $v2->path;
        
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

    $img = '/lesscreator/~/fam3/icons/database.png';

    if ($v['type'] == 1) {
        $typename = 'BigTable';
    } else if ($v['type'] == 2) {
        $typename = $this->T('MySQL Relational Database');
        $img = '/lesscreator/static/img/plugins/lessdata/mysql.png';
    } else if ($v['type'] == 3) {
        $typename = $this->T('Aliyun Relational Database Service');
        $img = '/lesscreator/static/img/plugins/lessdata/aliyun-rds.png';
    }

    echo "<tr>
        <td width='5px'></td>
        <td width='70px'>
            <img src='{$img}' class='' width='48' height='48' /> 
        </td>
        <td>
            <a href='#{$k}' class='g2hvtz' title='{$v['name']}'>
            {$v['name']}
            </a>
            <em>{$typename}</em>
        </td>
        <td align='right'> 
            <span>
            <img src='/lesscreator/~/fam3/icons/table_add.png' class='h5c_icon' /> 
            <a href='#{$k}' class='weovcr'>". $this->T('New Table') ."</a>
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
            <img src='/lesscreator/~/fam3/icons/database_table.png' class='h5c_icon' /> 
            <a href='#{$k}/{$v2['tableid']}' class='p9532p' title='{$v2['tablename']}'>
            {$v2['tablename']}
            </a>
        </td>
        <td align='right'>
            <img src='/lesscreator/~/fam3/icons/cog.png' class='h5c_icon' />
            <a href='#{$k}/{$v2['tableid']}' class='p9532p' title='{$v2['tablename']}'>". $this->T('Settings') ."</a></td>
        <td></td>
        </tr>";
    }
}
echo "</table>";
?>

<script type="text/javascript">

$('.g2hvtz').click(function() {
    var uri = $(this).attr('href').substr(1);
    var url = "/lesscreator/plugins/lessdata/dataset-set?proj="+projCurrent+"&id="+uri;
    lessModalOpen(url, 0, 500, 260, "<?php echo $this->T('DataSet Settings')?>", null);
});

$('.weovcr').click(function() {
    var uri = $(this).attr('href').substr(1);
    var url = "/lesscreator/plugins/lessdata/table-new?proj="+projCurrent+"&id="+uri;
    lessModalOpen(url, 0, 400, 260, "<?php echo $this->T('New Table')?>", null);
});

$('.p9532p').click(function() {
    var uri = $(this).attr('href').substr(1);
    var tit = $(this).attr('title');
    var url = "/lesscreator/plugins/lessdata/inlet-table?proj="+projCurrent+"&data="+uri;
    h5cTabOpen(url, 'w0', 'html', {'title': tit, 'close':'1', 'img': '/fam3/icons/database_table.png'});
});

</script>
