<?php

use LessPHP\Encoding\Json;


$status = 200;
$msg    = 'Successfully Saved';//'Internal Server Error';

$projPath = lesscreator_proj::path($this->req->proj);


$grps = array();



if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $this->req->name;

    if (!strlen($name)) {
        header("HTTP/1.1 500"); die('Invalid Params');
    }

    $obj = $projPath ."/dataflow";
    $obj = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $obj);
    
    $id = LessPHP_Util_String::rand(12, 2);

    $obj .= "/{$id}/grp.json";
    $obj = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $obj);

    $set = array(
        'id'    => $id,
        'name'  => $name,
    );
    lesscreator_fs::FsFilePut($obj, Json::prettyPrint($set));

    die("OK");
}

?>

<table class="h5c_dialog_header" width="100%">
    <tr>
        <td width="20px"></td>
        <td style="font-size:14px;font-weight:bold;">New Actor</td>
    </tr>
</table>

<form id="tmq-workflow-edit-form" action="/tmqueue/adm-workflow/edit" class="form-horizontal">

  <table width="100%" cellpadding="3">
    <tr>
      <td width="120"><strong>Group <font color="red">*</font></strong></td>
      <td>
        <?php
        if ($set['grpid'] == '') {
            echo "<select name='grpid'>";
            foreach ($infoproj as $k => $v) {
                if ($k == $set['grpid']) {
                    echo "<option value='{$k}' selected>{$v}</option>";
                } else {
                    echo "<option value='{$k}'>{$v}</option>";
                }
            }
            echo "</select>";
        } else {
            echo "<strong>{$infoproj[$set['grpid']]}</strong>";
            echo "<input name='grpid' type='hidden' value='{$set['grpid']}' />";
        }
        ?>
      </td>
    </tr>
    <tr>
      <td><strong>名称 <font color="red">*</font></strong></td>
      <td><input type="text" name="title" placeholder="" value="<?php echo $set['title']?>" /></td>
    </tr>
    <tr>
      <td><strong>状态 <font color="red">*</font></strong></td>
      <td>
        <select id="status" name="status">
            <option value="1" <?php if ($set['status'] == 1) {echo ' selected';}?>>启动</option>
            <option value="0" <?php if ($set['status'] == 0) {echo ' selected';}?>>停用</option>
        </select>
      </td>
    </tr>
    <tr>
      <td><strong>脚本执行方式</strong></td>
      <td>
        <select id="execmode" name="execmode" onchange="_exectype(this.value)">
            <option value="1" <?php if ($set['execmode'] == 1) {echo ' selected';}?>>永久执行</option>
            <option value="2" <?php if ($set['execmode'] == 2) {echo ' selected';}?>>循环执行</option>
            <option value="3" <?php if ($set['execmode'] == 3) {echo ' selected';}?>>手动执行</option>
        </select>
      </td>
    </tr>
    <tr>
      <td></td>
      <td>
        <div id="execmode1" class="tmq-exectype displaynone">
        <p class="muted">脚本永久执行，永不过期; 如遭异常退出，系统将自动重载继续执行.</p>
        </div>
        
        <div id="execmode2" class="tmq-exectype displaynone">
        <p class="muted">按照约定间隔时间循环执行</p>
        <table>
          <tr>
            <td width="160px">每次执行间隔 <font color="red">*</font></td>
            <td>
              <div class="input-append">
                <input class="input-small" type="text" name="execmode_sleep" value="<?php echo $set['execmode_sleep']?>" />
                <span class="add-on">秒</span>
              </div>
            </td>
          </tr>
          <tr>
            <td>超时时间 <font color="red">*</font></td>
            <td>
              <div class="input-append">
                <input class="input-small" type="text" name="execmode_timeout" value="<?php echo $set['execmode_timeout']?>" />
                <span class="add-on">秒</span>
              </div>
            </td>
          </tr>
        </table>
        </div>
        
        <div id="execmode3" class="tmq-exectype displaynone">
        <p class="muted">系统管理员手动触发，任务执行完成后即过期.</p>
        </div>
        
        <!-- TODO
        <table id="execmode3" class="tmq-exectype displaynone">
          <tr>
            <td>分钟</td>
            <td>小时</td>
            <td>日</td>
            <td>月</td>
            <td>周</td>
          </tr>
          <tr>
            <td><input type="text" class="input-small" name="execmode_cron_m" value="*"/></td>
            <td><input type="text" class="input-small" name="execmode_cron_h" value="*"/></td>
            <td><input type="text" class="input-small" name="execmode_cron_dom" value="*"/></td>
            <td><input type="text" class="input-small" name="execmode_cron_mon" value="*"/></td>
            <td><input type="text" class="input-small" name="execmode_cron_dow" value="*"/></td>
          </tr>
        </table>
        -->
      </td>
    </tr>
    <tr>
      <td><strong>脚本启动策略</strong></td>
      <td>
        <select id="mapmode" name="mapmode" onchange="_mapmode(this.value)" style="width:400px">
            <option value="1" <?php if ($set['mapmode'] == 1) {echo ' selected';}?>>关联数据实例: 每分片启动一个进程</option>
            <option value="2" <?php if ($set['mapmode'] == 2) {echo ' selected';}?>>关联数据实例: 每物理节点启动一个进程</option>
            <option value="3" <?php if ($set['mapmode'] == 3) {echo ' selected';}?>>绑定服务器: 每物理节点启动一个进程</option>
        </select>
      </td>
    </tr>
    <tr>
      <td></td>
      <td>
        <div id="mapmode_data" class="tmq-mapmode displaynone">
        <table>
          <tr>
            <td width="100px">数据实例 ID <font color="red">*</font></td>
            <td>
              <input class="input" type="text" name="mapmode_data" value="<?php echo $set['mapmode_data']?>" /> (TODO 弹框,多选)
            </td>
          </tr>
        </table>
            
        </div>
        
        <div id="mapmode3" class="tmq-mapmode displaynone">
        <table>
          <tr>
            <td width="100px">服务器 <font color="red">*</font></td>
            <td>
              <input class="input-xxlarge" type="text" name="mapmode_node" value="<?php echo $set['mapmode_node']?>" /> (TODO 弹框,多选)
            </td>
          </tr>
        </table>
        </div>

      </td>
    </tr>
    <tr>
      <td></td>
      <td><input type="submit" class="btn btn-primary" value="保存" /></td>
    </tr>
  </table>
  
</form>
