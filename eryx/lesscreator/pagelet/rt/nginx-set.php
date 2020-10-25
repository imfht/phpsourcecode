<?php

use LessPHP\Encoding\Json;

if (!isset($this->req->proj) || strlen($this->req->proj) < 1) {
    die($this->T('Page Not Found'));
}

$projPath = lesscreator_proj::path($this->req->proj);

$status = 200;
$msg    = '';

$info = lesscreator_proj::info($this->req->proj);
if (!isset($info['projid'])) {
    die($this->T('Page Not Found'));
}

$enabled_check = "";
$ngx_conf_mode = "std";
foreach ($info['runtimes'] as $name => $rt) {

    if ($name == "nginx" && $rt['status'] == 1) {
        $enabled_check = "checked";

        if (isset($rt['ngx_conf_mode'])) {
            $ngx_conf_mode = $rt['ngx_conf_mode'];
        }
        break;
    }
}

$modes = lesscreator_env::NginxConfTypes();

if ($this->req->apimethod == "ngx_conf.get") {

    $ret = array(
        'status' => 200,
        'message' => "",
    );    

    if (!isset($modes[$this->req->ngx_conf_mode])) {
        $this->req->ngx_conf_mode = "std";
    }

    if ($this->req->ngx_conf_mode == "custom") {
        
        $lcpj = "{$projPath}/misc/nginx/virtual.custom.conf";

        $ctn = lesscreator_fs::FsFileGet($lcpj);

        //$ret['data'] = $ctn;
        //die(json_encode($ret));
        if ($ctn->status == 200) {
            $ret['message'] = null;
            $ret['data'] = $ctn->data->body;
        } else if ($ctn->status == 404) {
            
            $tmp = LESSCREATOR_DIR."/misc/nginx/virtual.phpmix.conf";
        
            $ctn = file_get_contents($tmp);
            if ($ctn !== false) {
                $ret['data'] = $ctn;
            }
        } else {
            $ret['status'] = $ctn->status;
            $ret['message'] = $ctn->message;
        }
        //print_r($cfg);
    } else {

        $lcpj = LESSCREATOR_DIR."/misc/nginx/virtual.{$this->req->ngx_conf_mode}.conf";
        
        $ctn = file_get_contents($lcpj);
        if ($ctn === false) {
            $ret['status'] = 400;
            die(json_encode($ret));
        }

        $ret['data'] = $ctn;
    }

    die(json_encode($ret));
}

?>
<style type="text/css">
#ngx_conf {
    border: 1px solid #ccc;
    overflow: auto;
}
.m484ny {
    margin-bottom: 3px;
}
#ngx_conf_mode {
    margin: 0 0 0 40px; padding: 2px; 
    height: 24px; font-size: 12px;
    width: 400px;
}
#ngx_conf {
    width: 600px; height: 300px;
}
#ngx_conf .CodeMirror {
    font-size: 11px;
}
</style>
<table>
<tr>
<td width="160px" valign="top">
    <img src="/lesscreator/static/img/rt/nginx_200.png" width="120" height="60" />
    <ul>
        <li>version >= 1.4.x</li>
    </ul>
</td>
<td>

    <div id="gix0qn" class="alert alert-info">
        <?php echo $this->T('rt-nginx-desc')?>
    </div>
    
    <div class="m484ny">

    <label class="checkbox inline">
      <input id="k4grco" type="checkbox" value="1" onchange="_proj_rt_nginx_onoff_chg()" <?php echo $enabled_check?> /> <?php echo $this->T('Enable')?> Nginx
    </label>

    <select id="ngx_conf_mode" class="inline hide" onchange="_proj_rt_nginx_mode_chg(this)">
    <?php
    foreach ($modes as $mk => $mv) {
        if ($mk === $ngx_conf_mode) {
            echo "<option value='{$mk}' selected>{$mv}</option>";
        } else {
            echo "<option value='{$mk}'>{$mv}</option>";
        }
    }
    ?>
    </select>

    </div>

    <div id="ngx_conf" name="ngx_conf" class="less_scroll"></div>
</td>
</tr>
</table>
<script>
if (lessModalPrevId() != null) {
    lessModalButtonAdd("zz9pdf", "<?php echo $this->T('Back')?>", "lessModalPrev()", "pull-left h5c-marginl0");
}

lessModalButtonAdd("dsr1a0", "<?php echo $this->T('Close')?>", "lessModalClose()", "");

lessModalButtonAdd("irpqn6", "<?php echo $this->T('Save')?>", "_proj_rt_nginx_save()", "btn-inverse");

function _proj_rt_nginx_onoff_chg()
{
    if ($("#k4grco").is (':checked')) {
        
        $('#ngx_conf_mode').show();

        var mode = $('#ngx_conf_mode option:selected').val();
        _proj_rt_nginx_conf_load(mode);

    } else {
        $('#ngx_conf_mode').hide();
        $('#ngx_conf').empty();
    }
}

function _proj_rt_nginx_save()
{
    var req = {
        proj: lessSession.Get("ProjPath"),
        status: 1,
        ngx_conf: null,
        ngx_conf_mode: null,
    }

    if ($("#k4grco").is (':checked')) {

        req.status = 1;

        var mode = $('#ngx_conf_mode option:selected').val(); 
        if (mode == "custom") {
            req.ngx_conf = ngxEditor.getValue();
        }

        req.ngx_conf_mode = mode;

    } else {
        req.status = 0
    }



    $.ajax({ 
        type    : "POST",
        url     : "/lesscreator/rt/nginx-set-do",
        data    : JSON.stringify(req),
        success : function(rsp) {
            //console.log(rsp);
            try {
                var rsj = JSON.parse(rsp);
            } catch (e) {
                lessAlert("#gix0qn", "alert-error", "<?php echo $this->T('Service Unavailable')?>");
                return;
            }

            if (rsj.status != 200) {
                lessAlert("#gix0qn", "alert-error", rsj.message);
                return;
            }

            lessAlert("#gix0qn", "alert-success", rsj.message);
            _proj_rt_refresh();
            //window.scrollTo(0,0);
        },
        error: function(xhr, textStatus, error) {
            lessAlert("#gix0qn", "alert-error", textStatus+' '+xhr.responseText);
        }
    });
}

var ngxEditor = null;
function _proj_rt_nginx_conf_load(mode)
{
    var url = "/lesscreator/rt/nginx-set?apimethod=ngx_conf.get";
    url += "&proj=" + lessSession.Get("ProjPath");
    url += "&ngx_conf_mode="+ mode;

    $.ajax({ 
        type    : "GET",
        url     : url,
        success : function(rsp) {

            //console.log(rsp);

            try {
                var rsj = JSON.parse(rsp);
            } catch (e) {
                lessAlert("#gix0qn", "alert-error", "<?php echo $this->T('Service Unavailable')?>");
                return;
            }

            if (rsj.status != 200) {
                lessAlert("#gix0qn", "alert-error", rsj.message);
                return;
            }

            $("#ngx_conf").empty();

            var readOnly = true;
            if (mode == "custom") {
                readOnly = false;
            }

            ngxEditor = CodeMirror(document.getElementById("ngx_conf"), {
                value         : rsj.data,
                lineNumbers   : true,
                matchBrackets : true,
                mode          : "nginx",
                indentUnit    : 4,
                tabSize       : 4,
                theme         : "default",
                smartIndent   : true,
                lineWrapping  : true,
                readOnly      : readOnly,
            });

            CodeMirror.modeURL = "/codemirror3/3.21.0/mode/%N/%N.min.js";
            CodeMirror.autoLoadMode(ngxEditor, "nginx");
        },
        error: function(xhr, textStatus, error) {
            lessAlert("#gix0qn", "alert-error", textStatus+' '+xhr.responseText);
        }
    });
}

function _proj_rt_nginx_mode_chg(node)
{
    var mode = node.options[node.selectedIndex].value;
    _proj_rt_nginx_conf_load(mode);
    //console.log(mode);
}

<?php
if ($enabled_check == "checked") {
    echo "_proj_rt_nginx_conf_load('{$ngx_conf_mode}');\n
        $('#ngx_conf_mode').show();\n";
}
?>
</script>
