<?php

use LessPHP\Encoding\Json;


$projPath = lesscreator_proj::path($this->req->proj);


$obj = $projPath ."/dataflow";

list($grpid, $actorid) = explode("/", $this->req->uri);

$fsg = $obj."/{$grpid}.grp.json";

$grp = lesscreator_fs::FsFileGet($fsg);
if ($grp->status != 200) {
    die($this->T('Bad Request'));
}
$grp = json_decode($grp->data->body, true);
if (!isset($grp['id'])) {
    die("Internal Server Error");
}

$fsj = $obj."/{$grpid}/{$actorid}.actor.json";
$rs = lesscreator_fs::FsFileGet($fsj);
if ($rs->status != 200) {
    die($this->T('Bad Request'));
}

$actorDefault = array(
    'name'          => $actorid,

    'exec_mode'     => '0',
    'exec_timeout'  => 3600,
    'exec_interval' => 86400,
    'exec_cron'     => "*,*,*,*,*",

    'para_mode'     => '0',
    'para_data'     => '',
);

$actor = json_decode($rs->data->body, true);
if (!isset($actor['id'])) {
    die("Internal Server Error");
}
$actor      = array_merge($actorDefault, $actor);
//lesscreator_service::debugPrint($actor);
$exec_cron  = explode(",", $actor['exec_cron']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim($this->req->name);
    if (!strlen($name)) {
        die('`name` can not be null');
    }
    $actor['name']          = $name;
    $actor['updated']       = time();
    
    $actor['exec_mode']     = intval($this->req->exec_mode);
    $actor['exec_timeout']  = intval($this->req->exec_timeout);
    $actor['exec_interval'] = intval($this->req->exec_interval);
    $actor['exec_cron']     = implode(",", $this->req->exec_cron);
    
    $actor['para_mode']     = intval($this->req->para_mode);
    $actor['para_data']     = $this->req->para_data;
    
    lesscreator_fs::FsFilePut($fsj, Json::prettyPrint($actor));

    die("OK");
}

?>
<style>
.exec_regular td {
    width: 80px;
}
.exec_regular input {
    width: 30px;
}
</style>
<form id="sy9p3x" action="/lesscreator/proj/dataflow/actor-edit" style="padding:10px;">
  <input type="hidden" name="proj" value="<?php echo $this->req->proj?>" />
  <input type="hidden" name="uri" value="<?php echo $this->req->uri?>" />
  <table width="100%" cellpadding="20px" style="border-spacing:20px;">
    
    <tr>
      <td width="160px">Group</td>
      <td>
        <?php echo $grp['name']?>
      </td>
    </tr>
    
    <tr>
      <td>Name your Actor</td>
      <td><input type="text" name="name" value="<?php echo $actor['name']?>" /></td>
    </tr>
    
    <tr>
      <td>Execution Mode</td>
      <td>
        <select name="exec_mode" onchange="_exec_mode(this.value)" style="width:500px">
        <?php
        $vs = lesscreator_service::listExecMode();
        foreach ($vs as $k => $v) {
          $select = '';
          if ($k == $actor['exec_mode']) {
            $select = 'selected';
          }
          echo "<option value='{$k}' {$select}>{$v}</option>";
        }
        ?>
        </select>
        <div id="nyj2w2">
        </div>
      </td>
    </tr>
    <tr>
      <td></td>
      <td>
        <div class="exec_timeout qvzhnl hide">
          <label>Timeout <font color="red">*</font></label>
          <div class="input-append">
            <input class="input-small" type="text" name="exec_timeout" value="<?php echo $actor['exec_timeout']?>" />
            <span class="add-on">Seconds</span>
          </div>
        </div>
        
        <div class="exec_interval qvzhnl hide">
          <label>Time interval <font color="red">*</font></label>
          <div class="input-append">
            <input class="input-small" type="text" name="exec_interval" value="<?php echo $actor['exec_interval']?>" />
            <span class="add-on">Seconds</span>
          </div>
        </div>
        
        <div class="exec_regular qvzhnl hide" >
        <label>Regular time <font color="red">*</font> (same to unix crontab setting)</label>
        <pre> Example of time definition:
 .---------------- minute (0 - 59)
 |  .------------- hour (0 - 23)
 |  |  .---------- day of month (1 - 31)
 |  |  |  .------- month (1 - 12)
 |  |  |  |  .---- day of week (0 - 6) (Sunday=0)
 |  |  |  |  |
 *  *  *  *  *</pre>
        <table>
          <tr>
            <td>minute</td>
            <td>hour</td>
            <td>day</td>
            <td>month</td>
            <td>week</td>
          </tr>
          <tr>
          <?php
          for ($i = 0; $i < 5; $i++) {
            echo "<td><input type='text' name='exec_cron[{$i}]'' value='{$exec_cron[$i]}'/></td>";
          }
          ?>
          </tr>
        </table>

      </td>
    </tr>
    
    <tr>
      <td>Parallel Mode</td>
      <td>
        <select name="para_mode" onchange="_para_mode(this.value)" style="width:500px;">
        <?php
        $vs = lesscreator_service::listParaMode();
        foreach ($vs as $k => $v) {
          $select = '';
          if ($k == $actor['para_mode']) {
            $select = 'selected';
          }
          echo "<option value='{$k}' {$select}>{$v}</option>";
        }
        ?>
        </select>
      </td>
    </tr>

    <tr>
      <td></td>
      <td>
        <?php
        $para_data_display = '';
        if (isset($actor['para_data']) && strlen($actor['para_data']) > 0) {

            $para_datas = explode("_", $actor['para_data']);

            $fsd  = $projPath."/data/{$para_datas[0]}.ds.json";
            $json = lesscreator_fs::FsFileGet($fsd);
            $json = json_decode($json->data->body, true);
            if (isset($json['name'])) {
                $para_data_display = $json['name'];
            }
            
            $fst  = $projPath."/data/{$para_datas[0]}.{$para_datas[1]}.tbl.json";
            $json = lesscreator_fs::FsFileGet($fst);
            $json = json_decode($json->data->body, true);
            if (isset($json['tablename'])) {
                $para_data_display .= " - ". $json['tablename'];
            }
        }
        ?>
        <div class="smxw88 para_data input-append hide" style="width:500px;">
          <input name="para_data_display" type="text" value="<?php echo $para_data_display?>" class="span4"/>
          <input name="para_data" type="hidden" value="<?php echo $actor['para_data']?>" class="para_data"/>
          <button type="button" class="btn" onclick="_dataflow_edit_paraselect()">
            <i class="icon-share-alt"></i>
            <span class="_proj_new_basedir_dp">Select a Database</span>
          </button>
        </div>
      </td>
    </tr>

    <tr>
      <td></td>
      <td><input type="submit" class="btn btn-primary" value="<?php echo $this->T('Save')?>" style="margin: 10px 0;" /></td>
    </tr>
  </table>
  
</form>


<script type="text/javascript">

function _exec_mode(val)
{
    $(".qvzhnl").hide();

    if (val == 1 || val == 2 || val == 3) {
        $(".exec_timeout").show();
    }
    
    if (val == 2) {
        $(".exec_regular").show();
    }

    if (val == 3) {
        $(".exec_interval").show();
    }
}
_exec_mode(<?php echo $actor['exec_mode']?>);


function _dataflow_edit_paraselect()
{
    var url = "/lesscreator/proj/dataflow/actor-edit-para-data?proj="+projCurrent+"&func=select";
    lessModalOpen(url, 0, 400, 300, 'Select a Database', null);
}

function _para_mode(val)
{
    $(".smxw88").hide();

    if (val == 3 || val == 4 || val == 5) {
        $(".para_data").show();
    }
}
_para_mode(<?php echo $actor['para_mode']?>);

$("#sy9p3x").submit(function(event) {

    event.preventDefault();
    
    $.ajax({
        type    : "POST",
        url     : $(this).attr('action'),
        data    : $(this).serialize(),
        timeout : 3000,
        success : function(rsp) {            
            if (rsp == "OK") {
                hdev_header_alert('success', rsp);
                _proj_dataflow_tabopen(projCurrent, '', 1);
            } else {
                hdev_header_alert("error", rsp);
            }
        },
        error: function(xhr, textStatus, error) {
            hdev_header_alert('error:', textStatus +' '+ xhr.responseText);
        }
    });
});
</script>
