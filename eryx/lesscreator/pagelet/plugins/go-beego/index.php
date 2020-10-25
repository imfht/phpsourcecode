<?php

use LessPHP\Util\Directory;
    
$info = lesscreator_proj::info($this->req->proj);
if (!isset($info['projid'])) {
    die($this->T('Bad Request'));
}
$projPath = lesscreator_proj::path($this->req->proj);

?>

<div style="background:#f6f7f8;padding: 10px 5px;border-bottom:1px solid #ccc;">
    <img src="/lesscreator/static/img/plugins/go-beego/beego-ico-90.png" class="h5c_ico" width="48" height="48" />
    <span class="inline"><strong>Beego Framework</strong> </span>
</div>


<div style="padding:5px;">

<div id="jxaebr" class="alert alert-success hide">

<h3>Beego Framework</h3>

<?php echo $this->T('plugins-go-beego-mvc-desc')?><br/><br/>
<p><?php echo $this->T('Project site')?>: <a href="http://beego.me" target="_blank">http://beego.me</a></p>
<p><?php echo $this->T('Project source')?>: <a href="https://github.com/astaxie/beego" target="_blank">https://github.com/astaxie/beego</a></p>

<p style="color:#dc4437;margin-top:15px;font-size:16px;">
<?php echo $this->T('rt-quickstart-fsinit-desc')?>!</p><br/>

<ul>
<?php
$fsini = lesscreator_fs::FsFileGet("{$projPath}/conf/app.conf");

if ($fsini->status != 200) {

    $fs = Directory::listFiles(LESSCREATOR_DIR ."/pagelet/plugins/go-beego/fs-init-tpl");
    foreach ($fs as $v) {
        echo "<li>$v</li>";
    }
}
?>
</ul><br/>

<a class="btn btn-success" href="#go-beego/start" onclick="_plugin_go_beego_start()"><?php echo $this->T('Confirm and Start Beego Framework MVC layer')?></a>

</div>

<div id="qwq3rw"></div>

<script type="text/javascript">

function _plugin_go_beego_start()
{
	var req = {
        "access_token" : lessCookie.Get("access_token"),
        "data" : {
            "projdir": lessSession.Get("ProjPath")
        }
    }

    var uri = '/lesscreator/plugins/go-beego/fs-init?_='+ Math.random();

	$.ajax({
        type    : "POST",
        url     : uri,
        data    : JSON.stringify(req),
        success : function(rsp) {
            _fs_file_new_callback("/");
            $("#jxaebr").hide();
            _plugin_go_beego_cvlist();
        },
        error   : function(xhr, textStatus, error) {
            //
        }
    });
}

function _plugin_go_beego_cvlist()
{
    var uri = '/lesscreator/plugins/go-beego/fs-ov-list?_='+ Math.random();
    uri += "&proj="+ lessSession.Get("ProjPath");

    $.ajax({
        type    : "GET",
        url     : uri,
        success : function(rsp) {
            $("#qwq3rw").html(rsp);
        },
        error   : function(xhr, textStatus, error) {
            //
        }
    });
}

<?php
if ($fsini->status != 200) {
    echo '$("#jxaebr").show();';
} else {
    echo "_plugin_go_beego_cvlist();";
}
?>

</script>
