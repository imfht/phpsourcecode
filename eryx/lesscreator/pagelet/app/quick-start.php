<style>

.dcrhlv .itembox {
    padding: 5px;
    text-decoration: none;
    font-size: 12px;
    background-color: #e5e5e5;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    margin: 0 ;
}
.dcrhlv .title {
    margin: 0; padding: 2px 0; font-weight: bold; font-size: 12px; line-height: 100%; color: #333333;
}
.dcrhlv .desc {
    margin: 0; padding: 0; color: #999999; line-height: 100%;
}
.dcrhlv .itembox:hover {
    background-color: #bed6fc;
    border: 1px solid #bed6fc;
    box-shadow: 1px 1px 2px #999;
}
.dcrhlv > table td {
    padding: 10px; text-align: center;
}
.dcrhlv .itembox img {
    position: relative; left: 1px; top: 1px; margin:0;
    width: 40px; height: 40px;
}
.dcrhlv .itembox2 {
    position: relative; left: 1px; top: 1px;
    width: 20px; height: 20px;
}
</style>

<div class="page-header" style="margin:10px;font-size:24px;">
  <span><?php echo $this->T('Quick Start')?> <small></small></span>
</div>

<div class="alert alert-info" style="margin:0 10px;font-size:20px;">
    <?php echo $this->T('quick-start-desc')?> <br /><br />
    <span class="label label-inverse"><?php echo $this->T('quick-start-desc2')?></span>
</div>

<div class="dcrhlv">
<table width="">
<tr>
<td class="">
    <div onclick="lcProjNew()" ="#" class="itembox">
       <img src="/lesscreator/static/img/proj/proj-new0.png" />
    </div>
    <?php echo $this->T('New Project')?><br/>
</td>
<td>
    <img class="itembox2" src="/lesscreator/static/img/proj/arrow-right-64.png" />
</td>
<td>
    <div onclick="_proj_quickstart_phpyaf()" class="itembox">
    <img src="/lesscreator/static/img/plugins/php-yaf/yaf-y-48.png" />
    </div>
    <?php echo $this->T('Auto Initialize Project files')?> (PHP Yaf Framework)
    <div onclick="_proj_quickstart_beego()" class="itembox">
    <img src="/lesscreator/static/img/plugins/go-beego/beego-ico-48.png" />
    </div>
    <?php echo $this->T('Auto Initialize Project files')?> (Go Beego Framework)
</td>
<td valign="middle">
    <img class="itembox2" src="/lesscreator/static/img/proj/arrow-right-64.png" />
</td>
<td>
    <div onclick="_proj_quickstart_launch()" class="itembox">
    <img src="/lesscreator/static/img/proj/play-128.png" />
    </div>
    <?php echo $this->T('Run and Deploy')?>
</td>
</td>

</tr>
<!--
<tr>
<td align="left">
    <?php echo $this->T('New Project')?><br/>
</td>
<td>
</td>
<td>
    <?php echo $this->T('Auto Initialize Project files')?> (Yaf)
</td>
<td>
</td>
<td>
    <?php echo $this->T('Run and Deploy')?>
</td>

</tr>
-->


</table>
</div>


<script>

function _proj_quickstart_phpyaf()
{
    if (!projCurrent) {
        alert('<?php echo $this->T('New Project First')?>');
        return;
    }

    var opt = {
        'title': 'Yaf Framework (PHP)',
        'close':'1',
        'img': '/lesscreator/static/img/plugins/php-yaf/yaf-s2-48.png',
    }

    var url = '/lesscreator/plugins/php-yaf/index?proj='+ lessSession.Get("ProjPath");

    h5cTabOpen(url, 'w0', 'html', opt);
}
function _proj_quickstart_beego()
{
    if (!projCurrent) {
        alert('<?php echo $this->T('New Project First')?>');
        return;
    }

    var opt = {
        'title': 'Beego Framework (Go)',
        'close':'1',
        'img': '/lesscreator/static/img/plugins/go-beego/beego-ico-48.png',
    }

    var url = '/lesscreator/plugins/go-beego/index?proj='+ lessSession.Get("ProjPath");

    h5cTabOpen(url, 'w0', 'html', opt);
}
function _proj_quickstart_launch()
{
    if (!projCurrent) {
        alert('<?php echo $this->T('New Project First')?>');
        return;
    }

    lcProjLaunch('<?php echo $this->T('Run and Deply')?>')
}
</script>
