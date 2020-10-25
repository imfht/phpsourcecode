<?php

use LessPHP\LessKeeper\Keeper;
use LessPHP\Net\Http;
use LessPHP\User\Session;

$kpr = new Keeper();

global $msg, $pkgals, $pkgdps, $pkgins;

$msg    = "";
$pkgdps = array();
$pkgals = array();
$pkgins = array();

$uname = Session::Instance()->uname;

try {
    
    // User
    if (!$uname) {
        throw new \Exception(sprintf($this->T('`%s` can not be null'), $this->T('Username')));
    }
    $user = strtolower($uname);
    if (!preg_match('/^([0-9a-z]{2,20})$/', $user)) {
        throw new \Exception(sprintf($this->T('`%s` is not valid'), $this->T('Username')));
    }

    $projPath = lesscreator_proj::path($this->req->proj);
    $projInfo = lesscreator_proj::info($this->req->proj);
    if (!isset($projInfo['projid'])) {
        throw new \Exception($this->T('Bad Request'));
    }

    $lc_plugins = array();
    if (isset($projInfo['lc_plugins'])) {
        $lc_plugins = explode(",", $projInfo['lc_plugins']);
    }
    if (in_array("go.beego", $lc_plugins)) {
        $next_uri = 'go_beego';
    } else {
        $next_uri = "webserver";
    }

    //
    $rs = $kpr->Info();
    $sysInfo = json_decode($rs->body, false);
    if (!isset($sysInfo->local->id)) {
        throw new \Exception($this->T('Service Unavailable'));
    }
    $hostid = $sysInfo->local->id;

    //
    $projInst = array();
    $cfgInstPath = "/app/local/{$hostid}/u/{$user}/inst/{$projInfo['projid']}";
    $rs = $kpr->NodeGet($cfgInstPath);
    if (isset($rs->body->projid)) {
        $projInst = json_decode($rs->body, true);
    }
    if (!isset($projInst['instid'])) {
    
        $projInst['instid'] = LessPHP_Util_String::rand(12, 2);
        $projInst['user'] = $uname;
        $projInst['projid'] = $projInfo['projid'];
        $projInst['projpath'] = $projPath;
        $projInst['status'] = 9;

        $rs = $kpr->NodeSet($cfgInstPath, json_encode($projInst));
    }
    $projInstId = $projInst['instid'];


    // Get the package's information of all
    $rs = $kpr->LocalNodeListAndGet("/lf/pkg");
    if ($rs->type != Keeper::ReplyMulti) {
        throw new \Exception($this->T("Internal Server Error"));
    }
    foreach ($rs->elems as $v) {

        $pkg = json_decode($v->body, true);
        
        if (!isset($pkg['projid'])) {
            continue;
        }
        
        $pkgals[$pkg['projid']] = $pkg;
        
        // TODO, Migration of instance between multiple domains
    }

    // Get the data instance settings of all
    $dbiNow = array();
    $rs = $kpr->NodeListAndGet("/db/ui/{$user}");
    if ($rs->type == Keeper::ReplyMulti) {
    
        foreach ($rs->elems as $v) {
        
            $inst = json_decode($v->body, true);
        
            if (!isset($inst['projid']) || !isset($inst['datainstid'])) {
                continue;
            }

            $dbiNow[$inst['id']] = $inst;
        }
    }

    //print_r($dbiNow);

    // Get current dataset struct
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

        $json['access_host'] = '127.0.0.1';
        $json['access_port'] = '3306';
        $json['access_user'] = '';
        $json['access_pass'] = '';
        $json['access_dbname'] = '';

        $json['tables'] = array();
        
        $datasets[$json['id']]['tables'] = array();
        if (isset($dbiNow[$json['id']])) {

            if (isset($dbiNow[$json['id']]['access_host'])) {
                $json['access_host'] = $dbiNow[$json['id']]['access_host'];
            }
            if (isset($dbiNow[$json['id']]['access_port'])) {
                $json['access_port'] = $dbiNow[$json['id']]['access_port'];
            }
            if (isset($dbiNow[$json['id']]['access_user'])) {
                $json['access_user'] = $dbiNow[$json['id']]['access_user'];
            }
            if (isset($dbiNow[$json['id']]['access_pass'])) {
                $json['access_pass'] = $dbiNow[$json['id']]['access_pass'];
            }
            if (isset($dbiNow[$json['id']]['access_dbname'])) {
                $json['access_dbname'] = $dbiNow[$json['id']]['access_dbname'];
            }
        }

        $datasets[$json['id']] = $json;

        $globsub = $projPath."/lcproj/lessdata/{$json['id']}.*.tbl.json";
        $rs2 = lesscreator_fs::FsList($globsub);
    
        foreach ($rs2->data as $v2) {

            $v2 = $v2->path;
        
            $json2 = lesscreator_fs::FsFileGet($v2);
            $json2 = json_decode($json2->data->body, true);
    
            if (!isset($json2['tableid'])) {
                continue;
            }

            $datasets[$json['id']]['tables'][] = $json2;
        }
    }

    //echo "<pre>";
    //print_r($datasets);
    //echo "</pre>";

} catch (\Exception $e) {
    $msg = $e->getMessage();
}

if (strlen($msg) > 1) {
    echo "<div class=\"alert alert-error\">{$msg}</div>";
    echo '<script>lessModalButtonAdd("yyixb9", "'.$this->T('Close').'", "lessModalClose()", "pull-lefts");</script>';
    return;
}

        

?>


<style>
.mxt4wy th {
    padding: 5px;
}
.mxt4wy td {
    padding: 5px;
}
.db-access-info {
    width: 100px;
}
</style>

<div id="f1lj5d" class="alert alert-info">
  <?php echo $this->T('Setup the database access information')?>
</div>

<div class="x203yg progress progress-info progress-striped active hide">
  <div class="bar" style="width: 1%"></div>
</div>

<form class="rtmr4e" action="#">

<table class="mxt4wy table table-bordered table-condenseds">
<!--<tr><td colspan="3"><strong>DataSets</strong></td></tr>-->
<tr>
    <td ><strong><?php echo $this->T('Name')?></strong></td>
    <td ><strong><?php echo $this->T('Type')?></strong></td>
    <td ><strong><?php echo $this->T('Database Access Information')?></strong></td>
</tr>

<?php
foreach ($datasets as $k => $v) {

    if ($v['type'] == 1) {
        $typedp = "BigTable";
    } else if ($v['type'] == 2) {
        $typedp = $this->T('MySQL Relational Database');
    } else if ($v['type'] == 3) {
        $typedp = $this->T('Aliyun Relational Database Service');
    } else {
        $typedp = $this->T('Unkown');
    }

    echo "<tr>
<td>
  <input type=\"hidden\" name=\"access_dbs[]\" value=\"".$v['id']."\" />
  {$v['name']}
</td>
<td>{$typedp}</td>
<td>

<div class='input-prepend '>
  <span class='add-on' style='width:130px'>".$this->T('Host')." / ".$this->T('Port')."</span>
  <input class='input-medium' type='text' name='access_host[]' value='".$v['access_host']."' />  
  <input class='input-medium' type='text' name='access_port[]' value='".$v['access_port']."' style='margin-left:5px'/>
</div><br/>
<div class='input-prepend'>
  <span class='add-on' style='width:130px'>".$this->T('Username')." / ".$this->T('Password')."</span>
  <input class='input-medium' type='text' name='access_user[]' value='".$v['access_user']."' />
  <input class='input-medium' type='text' name='access_pass[]' value='".$v['access_pass']."' style='margin-left:5px' />
</div><br/>
<div class='input-prepend'>
  <span class='add-on' style='width:130px'>".$this->T('Database Name')."</span>
  <input class='input-medium' type='text' name='access_dbname[]' value='".$v['access_dbname']."' />
</div>
</td>
</tr>";
}
?>
</table>

</form>

<script>

var user     = '<?php echo $user?>';
var dbjobid  = 'ouod4gr2';//'<?php echo LessPHP_Util_String::rand(10, 2)?>';
var dbjobtry = 0;

if (lessModalPrevId() != null) {
    lessModalButtonAdd("laab5w", "<?php echo $this->T('Back')?>", "lessModalPrev()", "pull-left");
}
lessModalButtonAdd("clwutn", "<?php echo $this->T('Confirm and Submit')?>", "_app_install_data_do()", "btn-inverse");
lessModalButtonAdd("yyixb9", "<?php echo $this->T('Close')?>", "lessModalClose()", "");


function _app_install_data_do()
{
    if (dbjobtry > 0) {
        //console.log("pendding");
        //return;
    }
    
    var uri = "user="+ user;
    uri += "&jobid="+ dbjobid;
    uri += "&proj="+ lessSession.Get("ProjPath");
    //console.log($(".rtmr4e").serialize());

    $.ajax({
        type    : "POST",
        url     : "/lesscreator/launch/dataset-queue?"+ uri,
        data    : $(".rtmr4e").serialize(),
        timeout : 3000,
        success : function(rsp) {

            var rsj = JSON.parse(rsp);
            
            if (rsj.status == 200) {
                
                lessModalScrollTop();
                
                lessAlert("#f1lj5d", " ", "<?php echo $this->T('op-commit-wait-desc')?>");
                window.setTimeout(_app_install_data_status, 500);

            } else {
                alert(rsj.message);
            }
        },
        error: function(xhr, textStatus, error) {
            alert("<?php echo $this->T('Internal Server Error')?>");
        }
    });
}

function _app_install_data_status()
{
    if (dbjobtry > 600) {
        
        lessAlert("#f1lj5d", "alert-error", "<?php echo $this->T('Processing timeout, please try again later')?>");
        $(".x203yg").hide(300);
        
        dbjobtry = 0;
        //return;
    }
    
    var uri = "user="+ user;
    uri += "&jobid="+ dbjobid;
    uri += "&proj="+ lessSession.Get("ProjPath");

    $.ajax({
        type    : "GET",
        url     : "/lesscreator/launch/dataset-queue-status?"+ uri,
        timeout : 3000,
        success : function(rsp) {

            try {
                var rsj = JSON.parse(rsp);
            } catch (e) {
                //lessAlert("#gix0qn", "alert-error", "<?php echo $this->T('Service Unavailable')?>");
                return;
            }
            
            if (rsj.status == 200) {

                $(".x203yg").show();
                $(".x203yg .bar").css({"width": rsj.data.process_percent+"%"});

                if (rsj.data.process_percent >= 100) {
                    
                    lessAlert("#f1lj5d", "alert-success", "<?php echo $this->T('Successfully Processed')?>");
                    $(".x203yg").hide(600);

                    dbjobtry = 0;
                    
                    window.setTimeout(_app_install_done, 500);

                } else {
                    window.setTimeout(_app_install_data_status, 300);
                }

            } else {
                alert("Error: "+ rsj.message);
            }
        },
        error: function(xhr, textStatus, error) {
            alert("<?php echo $this->T('Internal Server Error')?>");
        }
    });    
    
    dbjobtry++;
}

function _app_install_done()
{
    if (dbjobtry > 0) {
        return;
    }
    
    var uri = "/lesscreator/launch/<?php echo $next_uri?>?";
    uri += "proj="+ lessSession.Get("ProjPath");

    lessModalNext(uri, "<?php echo $this->T('Setting WebServer')?>", null);
}

<?php
if (count($datasets) < 1) {
    echo 'window.setTimeout(_app_install_done, 300);';
}
?>
</script>
