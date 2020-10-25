<?php

use LessPHP\Encoding\Json;
use LessPHP\LessKeeper\Keeper;


if (!isset($this->req->proj)
    || strlen($this->req->proj) < 1) {
    header("HTTP/1.1 404 Not Found");
    die($this->T('Page Not Found'));
}

$projPath = lesscreator_proj::path($this->req->proj);

$title  = 'Edit Project';

$info = lesscreator_env::ProjInfoDef("");
$t = lesscreator_proj::info($this->req->proj);
if (is_array($t)) {
    $info = array_merge($info, $t);
}

$lcpj = "{$projPath}/lcproject.json";
$lcpj = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $lcpj);

if ($this->req->apimethod == "self.rt.list") {

    $rts = lesscreator_env::RuntimesList();

    foreach ($info['runtimes'] as $name => $rt) {

        if ($rt['status'] != 1) {
            continue;
        }

        echo "
        <a class=\"item border_radius_5\" href=\"#rt/{$name}-set\" onclick=\"_proj_rt_set(this)\" title=\"". $this->T('Click to configuration') ."\">
            <img class=\"rt-ico\" src=\"/lesscreator/static/img/rt/{$name}_200.png\" />
            <label class=\"rt-tit\">{$rts[$name]['title']}</label>
        </a>";

        unset($rts[$name]);
    }

    if (count($rts) > 0) {

        echo '
        <a class="item border_radius_5 gray" href="#rt/select" onclick="_proj_rt_set(this)">
            <img class="newrt-ico" src="/lesscreator/static/img/for-test/setting2-128.png" />
            <span class="newrt-tit">'. $this->T('Add Runtime Environment') .'</span>
            <span class="newrt-desc">PHP, Python, Java, Go ...</span>
        </a>';
    }
    
    die();
}

if ($this->req->apimethod == "self.pkg.list") {

    $kpr = new Keeper();

    $rs = $kpr->LocalNodeListAndGet("/lf/pkg/");
    
    $dps = explode(",", $info['depends']);

    foreach ($rs->elems as $v) {

        $v = json_decode($v->body);
        if (!isset($v->projid)) {
            continue;
        }

        if (!in_array($v->projid, $dps)) {
            continue;
        }

        echo "
        <span class=\"item border_radius_5\">
            {$v->name}
        </span>";
    }

    echo '
        <a class="item-set border_radius_5" href="#" onclick="_proj_pkgs_select(this)">
            <img class="newrt-ico" src="/lesscreator/static/img/app-t3-16.png" />
            <span class="newrt-tit">'. $this->T('Add or Remove Projects') .'</span>
        </a>';
    
    die();
}


if (in_array($_SERVER['REQUEST_METHOD'], array('POST', 'PUT'))) {

    $ret = array("status" => 200, "message" => null);

    try {

        foreach ($info as $k => $v) {
            if (isset($_POST[$k]) && $k != "runtimes") {
                $info[$k] = trim($_POST[$k]);
            }
        }

        if (!isset($info['name']) || strlen($info['name']) < 1) {
            throw new \Exception(sprintf($this->T('`%s` can not be null'), $this->T('Name')), 400);
        }

        if (!isset($info['version']) || strlen($info['version']) < 1) {
            throw new \Exception(sprintf($this->T('`%s` can not be null'), $this->T('Version')), 400);
        }

        if (isset($info['props']) && is_array($info['props'])) {
            $info['props'] = implode(",", $info['props']);
        }
        if (isset($info['props_app']) && is_array($info['props_app'])) {
            $info['props_app'] = implode(",", $info['props_app']);
        }
        if (isset($info['props_dev']) && is_array($info['props_dev'])) {
            $info['props_dev'] = implode(",", $info['props_dev']);
        }
    
        $str = Json::prettyPrint($info);
        $rs = lesscreator_fs::FsFilePut($lcpj, $str);
        if ($rs->status != 200) {
            throw new \Exception($rs->message, 400);
        }

    } catch (\Exception $e) {
        $ret['status']  = $e->getCode();
        $ret['message'] = $e->getMessage();
    }

    die(json_encode($ret));
}

?>

<style>
#k2948f {
    padding: 5px;
}
#k2948f input,textarea {
    margin-bottom: 0px;
}
#k2948f .bordernil td {
    border-top:0px;
}
.rky7cv a {
    text-decoration: none;
}
.rky7cv .item {
    position: relative;
    background-color: #dff0d8;
    border: 2px solid #dff0d8;
    height: 40px; width: 220px;
    float: left; margin: 2px 5px 2px 0;
    line-height: 100%;
}
.rky7cv .item .newrt-ico {
    width: 30px; height: 30px;
    position: absolute; top: 5px; left: 5px;
}
.rky7cv .item .newrt-tit {
    position: absolute; font-size: 12px; font-weight: bold;
    color: #333; top: 5px; left: 40px;
}
.rky7cv .item .newrt-desc {
    position: absolute; font-size: 11px;
    color: #777; left: 40px;
    bottom: 3px;
}
.rky7cv .item .rt-ico {
    position: absolute;
    width: 60px; height: 30px;
    top: 50%; left: 5px; margin-top: -15px;
}
.rky7cv .item.gray {
    background-color: #fff;
}
.rky7cv .item:hover {
    border: 2px solid #7acfa8;
    background-color: #dff0d8;
}
.rky7cv .item .rt-tit {
    position: absolute; color: #333;
    margin-left: 80px; margin-top: -6px; top: 50%;
    font-weight: bold; font-size: 12px; line-height: 100%; 
}
.r0330s .item {
    position: relative;
    width: 220px;
    font-size: 12px;
    float: left; margin: 3px 10px 3px 0;
}
.r0330s .item input {
    margin-bottom: 0;
}

.lgjn8r a {
    text-decoration: none;
}
.lgjn8r .item {
    background-color: #dff0d8;
    border: 2px solid #7acfa8;
    padding: 5px; 
    float: left; margin: 2px 5px 2px 0;
    color: #000; font-weight: bold; font-size: 13px; line-height: 100%;
}
.lgjn8r .item-set {
    background-color: #fff;
    border: 2px solid #dff0d8;
    padding: 5px;
    float: left; margin: 2px 5px 2px 0;
    color: #000; font-weight: bold; font-size: 13px; line-height: 100%;
}
.lgjn8r .item-set img {
    width: 13px; height: 13px;
}
.lgjn8r .item-set:hover {
    border: 2px solid #7acfa8;
    background-color: #dff0d8;
}
</style>
<form id="k2948f" action="/lesscreator/proj/set/" method="post">
  <input name="proj" type="hidden" value="<?=$projPath?>" />
  <table class="table table-condensed" width="100%">

    <tr class="bordernil">
      <td width="180px"><strong><?php echo $this->T('Project ID')?></strong></td>
      <td><?=$info['projid']?></td>
    </tr>
    <tr>
      <td><strong><?php echo $this->T('Display Name')?></strong></td>
      <td>
        <input name="name" class="input-large" type="text" value="<?=$info['name']?>" />
        <label class="label label-important"><?php echo $this->T('Required')?></label>
        <span class="help-inline"><?php echo $this->T('Example')?>: <strong>Hello World</strong></span>
      </td>
    </tr>
    
    <tr>
      <td><strong><?php echo $this->T('Version')?></strong></td>
      <td>
        <input name="version" class="input-large" type="text" value="<?=$info['version']?>" /> 
        <label class="label label-important"><?php echo $this->T('Required')?></label>
        <span class="help-inline"><?php echo $this->T('Example')?>: <strong>1.0.0</strong></span>
      </td>
    </tr>

    <tr>
      <td valign="top"><strong><?php echo $this->T('Description')?></strong></td>
      <td><textarea name="summary" rows="2" style="width:400px;"><?=$info['summary']?></textarea></td>
    </tr>

    <tr>
      <td><strong><?php echo $this->T('Group by Application')?></strong></td>
      <td class="r0330s">
        <?php
        $preProps = explode(",", $info['props_app']);
        $ls = lesscreator_env::GroupByAppList();
        foreach ($ls as $k => $v) {
            $ck = '';
            if (in_array($k, $preProps)) {
                $ck = "checked";
            }
            echo "<label class=\"item checkbox\">
                <input type=\"checkbox\" name=\"props_app[]\" value=\"{$k}\" {$ck}/> ". $this->T($v) ."
                </label>";
        }
        ?>
      </td>
    </tr>

    <tr>
      <td><strong><?php echo $this->T('Group by Develop')?></strong></td>
      <td class="r0330s">
        <?php
        $preProps = explode(",", $info['props_dev']);
        $ls = lesscreator_env::GroupByDevList();
        foreach ($ls as $k => $v) {
            $ck = '';
            if (in_array($k, $preProps)) {
                $ck = "checked";
            }
            echo "<label class=\"item checkbox\">
                <input type=\"checkbox\" name=\"props_dev[]\" value=\"{$k}\" {$ck}/> ". $this->T($v) ."
                </label>";       
        }
        ?>
      </td>
    </tr>

    <tr>
      <td><strong><?php echo $this->T('Runtime Environment')?></strong></td>
      <td><div class="rky7cv">Loading</div></td>
    </tr>

    <tr>
      <td><strong><?php echo $this->T('Dependent Packages')?></strong></td>
      <td><div class="lgjn8r">Loading</div></td>
    </tr>

    <tr>
      <td></td>
      <td><input type="submit" name="submit" value="<?php echo $this->T('Save')?>" class="btn btn-inverse" /></td>
    </tr>
  </table>
</form>

<script>

$("#k2948f").submit(function(event) {

    event.preventDefault();

    $.ajax({ 
        type    : "POST",
        url     : $(this).attr('action'),
        data    : $(this).serialize(),
        success : function(rsp) {
            
            try {
                var rsj = JSON.parse(rsp);
            } catch (e) {
                hdev_header_alert('error', "<?php echo $this->T('Service Unavailable')?>");
                return;
            }

            if (rsj.status == 200) {
                hdev_header_alert('success', "<?php echo $this->T('Successfully Updated')?>");
                //window.scrollTo(0,0);
            } else {
                hdev_header_alert('error', rsj.message);
            }            
        },
        error: function(xhr, textStatus, error) {
            hdev_header_alert('error', textStatus+' '+xhr.responseText);
        }
    });
});

function _proj_rt_refresh()
{
    var url = "/lesscreator/proj/set?apimethod=self.rt.list";
    url += "&proj=" + lessSession.Get("ProjPath");

    $.ajax({ 
        type    : "GET",
        url     : url,
        success : function(rsp) {
            $(".rky7cv").empty().html(rsp);
        },
        error: function(xhr, textStatus, error) {
            // 
        }
    });
}

function _proj_rt_set(node)
{
    var uri = $(node).attr("href").substr(1);
    
    var title = "";
    switch (uri) {
    case "rt/select":
        title = "<?php echo $this->T('Add Runtime Environment')?>";
        break;
    case "rt/nginx-set":
        title = "<?php echo sprintf($this->T('%s Settings'), 'Nginx')?>"
        break;
    case "rt/php-set":
        title = "<?php echo sprintf($this->T('%s Settings'), 'PHP')?>";
        break;
    case "rt/go-set":
        title = "<?php echo sprintf($this->T('%s Settings'), 'Go')?>";
        break;
    case "rt/nodejs-set":
        title = "<?php echo sprintf($this->T('%s Settings'), 'NodeJS')?>";
        break;
    default:
        return;
    }
    
    uri += "?proj=" + lessSession.Get("ProjPath");
    lessModalOpen("/lesscreator/"+ uri, 1, 800, 500, title, null);
}

_proj_rt_refresh();


function _proj_pkgs_refresh()
{
    var url = "/lesscreator/proj/set?apimethod=self.pkg.list";
    url += "&proj=" + lessSession.Get("ProjPath");

    $.ajax({ 
        type    : "GET",
        url     : url,
        success : function(rsp) {
            $(".lgjn8r").empty().html(rsp);
        },
        error: function(xhr, textStatus, error) {
            // 
        }
    });
}

function _proj_pkgs_select(node)
{
    var uri = "/lesscreator/proj/set-pkgs?proj="+ lessSession.Get("ProjPath");
    lessModalOpen(uri, 1, 800, 500, "<?php echo $this->T('Select Dependent Packages')?>", null);
}

_proj_pkgs_refresh();


</script>
