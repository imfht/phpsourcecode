<?php

use LessPHP\Encoding\Json;
use LessPHP\LessKeeper\Keeper;
use LessPHP\LessFly\WebServer;
use LessPHP\User\Session;

$projPath = lesscreator_proj::path($this->req->proj);
$projInfo = lesscreator_proj::info($this->req->proj);

$kpr = new Keeper();

if (!isset($projInfo['projid'])) {
    die($this->T('Bad Request'));
}

$lc_plugins = array();
if (isset($projInfo['lc_plugins'])) {
    $lc_plugins = explode(",", $projInfo['lc_plugins']);
}

$msg = $this->T('Processing, please wait');
$script = "";

try {

    // Go/beego
    if (in_array("go.beego", $lc_plugins)) {

        $beePkgPath = LESSFLY_USERDIR ."/runtime/gopath/bin/bee";
        $rs = lesscreator_fs::FsFileExists($beePkgPath);

        $beegoPkgPath = LESSFLY_USERDIR ."/runtime/gopath/src/github.com/astaxie/beego/beego.go";
        $rs2 = lesscreator_fs::FsFileExists($beegoPkgPath);

        if (!$rs || !$rs2) {

            $msg = sprintf($this->T('The `%s` is not yet installed'), 'Beego');
            
            $msg .= "<br/>". $this->T('beego-install-desc');

            $msg .= ' <br/><button class="btn" onclick="_launth_beego()">'.$this->T('Install Now').'</button>';
            throw new \Exception($msg, 9001);
            //echo "NO";
        }

        //echo LESSFLY_USERDIR ."/app/{$projInfo['projid']}/conf/app.conf";
        $beegoConf = LESSFLY_USERDIR ."/app/{$projInfo['projid']}/conf/app.conf";
        $rs = lesscreator_fs::FsFileGet($beegoConf);

        $port = lesscreator_fs::EnvNetPort();
        $ini = parse_ini_string($rs->data->body);
        //$ini['usefcgi'] = true;
        $ini['httpport'] = $port;
        $ini['appname'] = $projInfo['projid'];
        //$ini['httpaddr'] = "/tmp/lf.go.".Session::Instance()->uname.".".$projInfo['projid'].".sock";

        $ini1 = "";
        foreach ($ini as $k => $v) {
            $ini1 .= "{$k} = {$v}\n";
        }
        //echo "<pre>";
        //print_r($ini);
        //echo $ini1;
        //echo "</pre>";
        if ($ini1 != $rs->data->body) {
            lesscreator_fs::FsFilePut($beegoConf, $ini1);
        }
        
        $script = "_launth_beego_run();\n";
        throw new \Exception(sprintf($this->T('`%s` starting up, please wait'), 'Beego'));
    }
    
    $script = "_launch_next_dataset();\n";
    
    throw new \Exception($this->T('Processing, please wait'));

} catch (\Exception $e) {
    
    $msg = $e->getMessage();
}

?>

<div id="kdj3iv" class="alert alert-info">
<?php echo $msg?>
</div>

<script type="text/javascript">

var projid = '<?php echo $projInfo["projid"]?>';

lessModalButtonAdd("fpntcr", "<?php echo $this->T('Close')?>", "lessModalClose()", "");

lcWebTerminal(1);

function _launth_beego()
{
    lcWebTerminal(1);
    
    setTimeout(function() {
        
        if (lc_terminal_conn.IsOk()) {
            var seq = String.fromCharCode(67 - 64); // Ctrl + C
            lc_terminal_conn.SendCmd(seq);
            lc_terminal_conn.SendCmd("icd ~\r");
            lc_terminal_conn.SendCmd("go get github.com/astaxie/bee\r");
            lc_terminal_conn.SendCmd("go get github.com/astaxie/beego\r");
        }
        lessModalClose();

    }, 1000);
}

function _launth_beego_run()
{
    lcWebTerminal(1);
    
    setTimeout(function() {
        
        if (lc_terminal_conn.IsOk()) {
            var seq = String.fromCharCode(67 - 64); // Ctrl + C
            lc_terminal_conn.SendCmd(seq);
            lc_terminal_conn.SendCmd(" cd ~/app/"+projid+"\r");
            lc_terminal_conn.SendCmd("bee run "+projid+"\r");
        }
        
        _launch_next_dataset();

    }, 3000);
}

function _launch_next_dataset()
{
    var uri = "/lesscreator/launch/dataset";
    uri += "?proj="+ lessSession.Get("ProjPath");

    lessModalNext(uri, "<?php echo $this->T('Run and Deply')?>", null);
}

<?php echo $script?>
</script>
