<?php

use LessPHP\Util\Directory;
    
$info = lesscreator_proj::info($this->req->proj);
if (!isset($info['projid'])) {
    die($this->T('Bad Request'));
}
$projPath = lesscreator_proj::path($this->req->proj);

if ($this->req->func == "action-new") {

    $ret = array(
        'status'  => 200,
        'message' => null,
    );

    try {

        if (!preg_match('/^([a-z]{1})([0-9a-zA-Z]{1,50})$/', $this->req->func_name)) {
            throw new \Exception(sprintf($this->T('`%s` is not valid'), $this->T('Function Name')), 400);
        }

        $lcf = "{$projPath}/{$this->req->path}";
        $lcf = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $lcf);
        
        $rs = lesscreator_fs::FsFileGet($lcf);

        if ($rs->status != 200) {
            throw new \Exception(sprintf($this->T('`%s` Not Found'), $this->T('File')), 404);
        }

        $cls = $this->req->class;

        $match = array(
            "%class\s{$cls}Controller(.*?)\{(.*?)%si",
        );
        $replace = array(
            "class {$cls}Controller$1{\n    function {$this->req->func_name}Action()\n    {\n    }\n\n$2",
        );
        $str = preg_replace($match, $replace, $rs->data->body);
        $rs = lesscreator_fs::FsFilePut($projPath ."/". $this->req->path, $str);

        if ($rs->status != 200) {
            throw new \Exception(sprintf($this->T('Cannot write to file `%s`'), $projPath ."/". $this->req->path), 500);
        }

        throw new \Exception($this->T('Successfully Done'), 200);
        
    } catch (\Exception $e) {

        $ret['status'] = intval($e->getCode());
        $ret['message'] = $e->getMessage();
    }
    
    die(json_encode($ret));
}
?>


<form id="td5kfz" action="#" method="post">
    
    <div class="input-append">
        <input type="text" name="func_name" value="" class="span2" />
        <span class="add-on"><strong>Action</strong></span>
    </div>

    <div><?php echo $this->T('First character must be a lowercase letter') .', '. $this->T('Example') ?>: <strong>hello</div>
</form>

<script type="text/javascript">

lessModalButtonAdd("xldqgw", "<?php echo $this->T('Create')?>", "_php_yaf_action_new()", "btn-inverse pull-left");
lessModalButtonAdd("g7yhlm", "<?php echo $this->T('Cancel')?>", "lessModalClose()", "pull-left");

$("#td5kfz").submit(function(event) {

    event.preventDefault();

    _php_yaf_action_new();
});

function _php_yaf_action_new()
{
    var url = "/lesscreator/plugins/php-yaf/fs-ov-action-new?func=action-new";

    var data = "proj="+ lessSession.Get("ProjPath");
    data += "&path=/application/controllers/<?php echo $this->req->ctl?>";
    data += "&func_name="+ $("#td5kfz").find("input[name=func_name]").val();
    data += "&class=<?php echo strstr($this->req->ctl, ".", true)?>";
    //console.log(data);

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
            //_fs_file_new_callback($("#td5kfz").find("input[name=path]").val());
            lessModalClose();
        },
        error   : function(xhr, textStatus, error) {
            hdev_header_alert('error', textStatus+' '+xhr.responseText);
        }
    });
}

</script>
