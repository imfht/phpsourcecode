<?php

use LessPHP\Encoding\Json;
use LessPHP\LessKeeper\Keeper;
use LessPHP\LessFly\WebServer;
use LessPHP\User\Session;

$projPath = lesscreator_proj::path($this->req->proj);
$projInfo = lesscreator_proj::info($this->req->proj);

$kpr = new Keeper();
$msg = null;

$openurl = "http://". $_SERVER['HTTP_HOST'];

try {

    if (!isset($projInfo['projid'])) {
        throw new \Exception($this->T('Bad Request'), 400);
    }

    $lc_plugins = array();
    if (isset($projInfo['lc_plugins'])) {
        $lc_plugins = explode(",", $projInfo['lc_plugins']);
    }

    if (!in_array("go.beego", $lc_plugins)) {
        throw new \Exception($this->T('Bad Request').": No Beego EnvSettings", 400);
    }


    $beegoConf = LESSFLY_USERDIR ."/app/{$projInfo['projid']}/conf/app.conf";
    $rs = lesscreator_fs::FsFileGet($beegoConf);

    $ini = parse_ini_string($rs->data->body);

    $openurl .= ":". $ini['httpport'];

} catch (\Exception $e) {
 
    echo $msg = $e->getMessage();     
}
?>

<div id="mc0zzp" class="alert alert-success">
<?php echo $this->T('Web Server Configuration successful')?><br /><br />

<a href="<?php echo $openurl?>" target='_blank' class='btn'> <i class='icon-share-alt'></i> <strong><?php echo $this->T('Open')?></strong> <?php echo $openurl?></a>
</div>