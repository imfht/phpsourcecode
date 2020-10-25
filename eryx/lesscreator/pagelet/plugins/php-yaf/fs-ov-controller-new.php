<?php

use LessPHP\Util\Directory;
    
$info = lesscreator_proj::info($this->req->proj);
if (!isset($info['projid'])) {
    die($this->T('Bad Request'));
}
$projPath = lesscreator_proj::path($this->req->proj);

if ($this->req->func == "controller-new") {

    $ret = array(
        'status'  => 200,
        'message' => null,
    );

    try {

        if (!preg_match('/^([A-Z]{1})([0-9a-zA-Z]{1,50})$/', $this->req->ctrl_name)) {
            throw new \Exception(sprintf($this->T('`%s` is not valid'), $this->T('Name')), 400);
        }

        $lcf = "{$projPath}/application/controllers/{$this->req->ctrl_name}.php";
        $lcf = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $lcf);
        
        $rs = lesscreator_fs::FsFileGet($lcf);

        if ($rs->status == 200) {
            throw new \Exception(sprintf($this->T('The `%s` already exists, please choose another one'), $this->T('Controller')), 500);
        }

        $str = '<?php

class '.$this->req->ctrl_name.'Controller extends Yaf_Controller_Abstract
{

}
';
        $rs = lesscreator_fs::FsFilePut($lcf, $str);

        if ($rs->status != 200) {
            throw new \Exception(sprintf($this->T('Cannot write to file `%s`'), $lcf), 500);
        }

        throw new \Exception($this->T('Successfully Done'), 200);
        
    } catch (\Exception $e) {

        $ret['status'] = intval($e->getCode());
        $ret['message'] = $e->getMessage();
    }
    
    die(json_encode($ret));
}
?>


<form id="illb99" action="#" method="post">
    <input type="text" name="ctrl_name" value="" class="span3" />
    <span class="help-inline"><?php echo $this->T('The first character must be an uppercase letter') .', '. $this->T('Example')?>: <strong>Hello</strong></span>
</form>

<script type="text/javascript">

lessModalButtonAdd("xldqgw", "<?php echo $this->T('Create')?>", "_php_yaf_controller_new()", "btn-inverse pull-left");
lessModalButtonAdd("g7yhlm", "<?php echo $this->T('Cancel')?>", "lessModalClose()", "pull-left");

$("#illb99").submit(function(event) {

    event.preventDefault();

    _php_yaf_controller_new();
});

function _php_yaf_controller_new()
{
    var url = "/lesscreator/plugins/php-yaf/fs-ov-controller-new?func=controller-new";

    var data = "proj="+ lessSession.Get("ProjPath");
    data += "&ctrl_name="+ $("#illb99").find("input[name=ctrl_name]").val();

    $.ajax({
        type    : "POST",
        url     : url,
        data    : data,
        timeout : 3000,
        success : function(rsp) {

            //console.log(rsp);

            var obj = JSON.parse(rsp);
            if (obj.status == 200) {
                hdev_header_alert('success', "<?php echo $this->T('Successfully Done')?>");
            } else {
                hdev_header_alert('error', obj.message);
            }

            if (typeof _plugin_yaf_cvlist == 'function') {
                _plugin_yaf_cvlist();
            }
            
            // TODO tree refresh
            //_fs_file_new_callback($("#illb99").find("input[name=path]").val());
            lessModalClose();
        },
        error   : function(xhr, textStatus, error) {
            hdev_header_alert('error', textStatus+' '+xhr.responseText);
        }
    });
}

</script>
