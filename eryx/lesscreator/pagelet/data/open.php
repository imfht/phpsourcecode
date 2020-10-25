<table class="h5c_dialog_header" width="100%">
    <tr>
        <td width="20px"></td>
        <td style="font-size:14px;font-weight:bold;">Double-click to open the Data Instance</td>
    </tr>
</table>

<div style="padding:10px;">
<?php

use LessPHP\LessKeeper\Keeper;
$kpr = new Keeper();

$ls = $kpr->NodeList("/h5db/info");

$ids = array();
foreach ($ls as $v) {
    $ids[] = "/h5db/info/{$v['P']}";
}
$ids = implode(" ", $ids);
$ls = $kpr->NodeGets($ids);

$ls = json_decode($ls, true);

echo "<div class='h5c_row_fluid'>";
foreach ($ls as $v) {
    $id = end(explode("/", $v['P']));
    $info = json_decode($v['C'], true);
    if (!isset($info['name'])) {
        $info['name'] = $id;
    }
    echo '
        <a style="width:90px;" class="span href aklw5v" href="#'.$id.'">
            <div class="center"><img src="/lesscreator/static/img/data/rds.png" align="absmiddle" /></div>
            <div class="center title">'.$info['name'].'</div>
        </a>
    ';
}
echo "</div>";
?>
</div>

<table id="_proj_fs_open_foo" class="h5c_dialog_footer" width="100%">
    <tr>
        <td align="right">            
            <button class="btn" onclick="lessModalClose()">Close</button>
        </td>
        <td width="20px"></td>
    </tr>
</table>
<script type="text/javascript">

$('.aklw5v').dblclick(function() {
    
    p = $(this).attr('href').substr(1);
    t = $(this).find(".title").text();

    var opt = {
        "img": "database",
        "title": t,
        "close": 1
    }

    h5cTabOpen("/lesscreator/data/inlet?proj="+projCurrent+"&id="+ p, "w0", 'html', opt);
    lessModalClose();
});
</script>
