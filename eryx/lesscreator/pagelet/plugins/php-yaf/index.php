<?php

use LessPHP\Util\Directory;
    
$info = lesscreator_proj::info($this->req->proj);
if (!isset($info['projid'])) {
    die($this->T('Bad Request'));
}
$projPath = lesscreator_proj::path($this->req->proj);

$version = "Unknown";
if (defined("YAF_VERSION")) {
    $version = YAF_VERSION;
}
?>

<div style="background:#f6f7f8;padding: 10px 5px;border-bottom:1px solid #ccc;">
    <img src="/lesscreator/static/img/plugins/php-yaf/yaf-ico-l-360.png" class="h5c_ico" width="60" height="30" />
    <span class="inline"><strong>PHP Yaf Framework</strong> ( <em>Version: <?php echo $version?></em> )</span>
</div>


<?php
if (!defined("YAF_VERSION")) {
    die("<div class='alert alert-error' style='margin:5px;'>
        <h4>".$this->T('Your current system is not installed the PHP-Yaf extension')."</h4><br/>
        ".$this->T('Please login system and execute the following command to install')."
        <pre style='margin:5px 0;'>
yum install php54-devel
pecl install yaf
echo \"extension=yaf.so\">/etc/php.d/yaf.ini
service php-fpm restart</pre>
        TODO: ".$this->T('Integrated into the LessFly Engine')."
        </div>");
}
?>


<div style="padding:5px;">

<div id="jxaebr" class="alert alert-success hide">

<h3>PHP Yaf Framework MVC</h3>

<?php echo $this->T('plugins-php-yaf-mvc-desc')?><br/><br/>
<p><?php echo $this->T('Project site')?>: <a href="http://pecl.php.net/package/yaf" target="_blank">http://pecl.php.net/package/yaf</a></p>
<p><?php echo $this->T('Project source')?>: <a href="https://github.com/laruence/php-yaf" target="_blank">https://github.com/laruence/php-yaf</a></p>

<p style="color:#dc4437;margin-top:15px;font-size:16px;">
<?php echo $this->T('rt-quickstart-fsinit-desc')?>!</p><br/>

<ul>
<?php
$fsini = lesscreator_fs::FsFileGet("{$projPath}/conf/application.ini");

if ($fsini->status != 200) {

    $fs = Directory::listFiles(LESSCREATOR_DIR ."/pagelet/plugins/php-yaf/fs-init-tpl");
    foreach ($fs as $v) {
        echo "<li>$v</li>";
    }
}
?>
</ul><br/>

<a class="btn btn-success" href="#php-yaf/start" onclick="_plugin_yaf_mvc_start()"><?php echo $this->T('Confirm and Start PHP Yaf Framework MVC layer')?></a>

</div>

<div id="qwq3rw"></div>

<script type="text/javascript">

function _plugin_yaf_mvc_start()
{
	var req = {
        "access_token" : lessCookie.Get("access_token"),
        "data" : {
            "projdir": lessSession.Get("ProjPath")
        }
    }

    var uri = '/lesscreator/plugins/php-yaf/fs-init?_='+ Math.random();

	$.ajax({
        type    : "POST",
        url     : uri,
        data    : JSON.stringify(req),
        success : function(rsp) {
            _fs_file_new_callback("/");
            $("#jxaebr").hide();
            _plugin_yaf_cvlist();
        },
        error   : function(xhr, textStatus, error) {
            //
        }
    });
}

function _plugin_yaf_cvlist()
{
    var uri = '/lesscreator/plugins/php-yaf/fs-ov-list?_='+ Math.random();
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
    echo "_plugin_yaf_cvlist();";
}
?>

</script>
