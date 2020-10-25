<?php

use LessPHP\Encoding\Json;

if (!isset($this->req->projid) || strlen($this->req->projid) < 1) {
    $projid = LessPHP_Util_String::rand(8, 2);
} else {
    $projid = $this->req->projid;
}

if (isset($this->req->basedir) && strlen($this->req->basedir)) {
    $basedir = $this->req->basedir;
} else {
    $basedir = $_COOKIE['basedir'];
}

$basedir = rtrim(preg_replace("/\/\/+/", "/", $basedir), '/');

if (in_array($_SERVER['REQUEST_METHOD'], array('POST', 'PUT'))) {

    $ret = array("status" => 200, "message" => null);

    $proj_new = "{$basedir}/app/{$projid}";
    $proj_new = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $proj_new);

    $proj_new_json = $proj_new ."/lcproject.json";

    try {

        $rs = lesscreator_fs::FsFileGet($proj_new_json);
        if ($rs->status == 200) {
            throw new \Exception(sprintf($this->T('The `%s` already exists, please choose another one'), 'Project ID'), 400);
        }

        if (!strlen($projid)) {
            throw new \Exception(sprintf($this->T('`%s` can not be null'), 'Project ID'), 400);
        }

        if (!strlen($this->req->name)) {
            throw new \Exception(sprintf($this->T('`%s` can not be null'), $this->T('Display Name')), 400);
        }

        $set = lesscreator_env::ProjInfoDef($projid);
        $set['name'] = $this->req->name;
        $set['summary'] = $this->req->summary;

        if (isset($this->req->props_app)) {
            $set['props_app'] = implode(",", $this->req->props_app);
        }
        if (isset($this->req->props_dev)) {
            $set['props_dev'] = implode(",", $this->req->props_dev);
        }

        $rs = lesscreator_fs::FsFilePut($proj_new_json, Json::PrettyPrint($set));
        if ($rs->status != 200) {
            throw new \Exception($rs->message, 400);
        }

    } catch (\Exception $e) {
        $ret['status']  = $e->getCode();
        $ret['message'] = $e->getMessage();
    }

    $ret['data']['proj'] = $proj_new;

    die(json_encode($ret));
}
?>
<style>
#sdtqvj {
    padding: 0px;
}
#sdtqvj input,textarea,.input-prepend,button {
    margin-bottom: 0px;
}
#sdtqvj .bordernil td {
    border-top:0px;
}

.r0330s .item {
    position: relative;
    width: 260px;
    float: left; margin: 3px 10px 3px 0;
}
.r0330s .item input {
    margin-bottom: 0;
}

</style>

<div id="m4ph6m" class="hide"></div>

<form id="sdtqvj" action="/lesscreator/proj/new/" method="post">
  
  <input name="basedir" type="hidden" value="<?php echo $basedir?>" />
  
  <table width="100%" class="table table-condensed">
    
    <tr class="bordernil">
      <td width="180px"><strong><?php echo $this->T('Project ID')?></strong> </td>
      <td>
        <input name="projid" type="text" class="span2" value="<?php echo $projid?>" />
        <label class="label label-important"><?php echo $this->T('Required')?></label>
        <label class="help-inline"><?php echo $this->T('Unique identifier, similar to the package name')?></label>
      </td>
    </tr>

    <tr>
      <td><strong><?php echo $this->T('Display Name')?></strong> </td>
      <td >
        <input name="name" type="text" class="span2" value="" />
        <label class="label label-important"><?php echo $this->T('Required')?></label>
        <label class="help-inline"><?php echo $this->T('Example')?>: Hello World</label>
      </td>
    </tr>

    <tr>
      <td><strong><?php echo $this->T('Group by Application')?></strong></td>
      <td class="r0330s">
        <?php
        $ls = lesscreator_env::GroupByAppList();
        foreach ($ls as $k => $v) {
            echo "<label class=\"item checkbox\">
                <input type=\"checkbox\" name=\"props_app[]\" value=\"{$k}\" /> ".$this->T($v)."
                </label>";
        }
        ?>
      </td>
    </tr>

    <tr>
      <td><strong><?php echo $this->T('Group by Develop')?></strong></td>
      <td class="r0330s">
        <?php
        $ls = lesscreator_env::GroupByDevList();
        foreach ($ls as $k => $v) {
            echo "<label class=\"item checkbox\">
                <input type=\"checkbox\" name=\"props_dev[]\" value=\"{$k}\" /> ".$this->T($v)."
                </label>";       
        }
        ?>
      </td>
    </tr>
    
    <tr>
      <td valign="top"><strong><?php echo $this->T('Description')?></strong></td>
      <td ><textarea name="summary" rows="2" style="width:400px;"></textarea></td>
    </tr>

  </table>
</form>


<script>
if (lessModalPrevId() != null) {
    lessModalButtonAdd("jwyztd", "<?php echo $this->T('Back')?>", "lessModalPrev()", "pull-left h5c-marginl0");
}

lessModalButtonAdd("d4ngex", "<?php echo $this->T('Confirm and Create')?>", "_proj_new_commit()", "btn-inverse");
lessModalButtonAdd("p5ke7m", "<?php echo $this->T('Close')?>", "lessModalClose()", "");


var _proj_new = "";

function _proj_new_commit()
{
    $.ajax({
        type    : "POST",
        url     : $("#sdtqvj").attr('action'),
        data    : $("#sdtqvj").serialize(),
        success : function(rsp) {
            //console.log(rsp);
            try {
                var rsj = JSON.parse(rsp);
            } catch (e) {
                lessAlert("#m4ph6m", "alert-error", "<?php echo $this->T('Service Unavailable')?>");
                return;
            }

            if (rsj.status == 200) {

                _proj_new = rsj.data.proj;

                lessAlert("#m4ph6m", "alert-success", "<p><strong><?php echo $this->T('Successfully Done')?></strong> \
                    <button class=\"btn btn-success\" onclick=\"_proj_new_goto()\"><?php echo $this->T('Open this Project')?></button>");

            } else {
                lessAlert("#m4ph6m", "alert-error", "Error: "+ rsj.message);
            }
        },
        error: function(xhr, textStatus, error) {
            lessAlert("#m4ph6m", "alert-error", "Error: "+ xhr.responseText);
        }
    });

    return;
}

function _proj_new_goto()
{
    h5cProjectOpen(_proj_new);
    lessModalClose();
    //window.open("/lesscreator/index?proj="+ _proj_new, "_blank");
}


</script>
