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

        if (!preg_match('/^([A-Z]{1})([0-9a-zA-Z]{1,50})$/', $this->req->ctl)) {
            throw new \Exception(sprintf($this->T('`%s` is not valid'), $this->T('Controller')), 400);
        }

        if (!in_array($this->req->func_name, 
            array('Get','Post','Put','Delete','Head','Patch','Options','Finish'))) {
            throw new \Exception(sprintf($this->T('`%s` is not valid'), $this->T('Function Name')), 400);
        }

        $lcf = "{$projPath}/controllers/{$this->req->file}";
        $lcf = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $lcf);
        
        $rs = lesscreator_fs::FsFileGet($lcf);

        if ($rs->status != 200) {
            throw new \Exception(sprintf($this->T('`%s` Not Found'), $this->T('File')), 404);
        }

        $pat = array("%(#|;|(//)).*%", "%/\*(?:(?!\*/).)*\*/%s");
        $str = preg_replace($pat, "", $rs->data->body);

        $str = str_replace("\n", "NNN", $str);

        if (preg_match('/\*'.$this->req->ctl.'Controller(.*?)\ ('.$this->req->func_name.')\(/', $str, $mat)) {

            throw new \Exception(sprintf($this->T('The `%s` already exists, please choose another one'), $this->T('Action')), 400);
        }
    
        $str = $rs->data->body .'
func (this *'.$this->req->ctl.'Controller) '.$this->req->func_name.'() {
    //this.TplNames = "'.strtolower($this->req->ctl).'-'.strtolower($this->req->func_name).'.tpl"
}
';
        $rs = lesscreator_fs::FsFilePut($lcf, $str);

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
    
    <div class="">
        <select name="func_name">
            <option value="Get">Get</option>
            <option value="Post">Post</option>
            <option value="Put">Put</option>
            <option value="Delete">Delete</option>
            <option value="Head">Head</option>
            <option value="Patch">Patch</option>
            <option value="Options">Options</option>
            <option value="Finish">Finish</option>
        </select>
    </div>

</form>

<script type="text/javascript">

lessModalButtonAdd("xldqgw", "<?php echo $this->T('Create')?>", "_go_beego_action_new()", "btn-inverse pull-left");
lessModalButtonAdd("g7yhlm", "<?php echo $this->T('Cancel')?>", "lessModalClose()", "pull-left");

$("#td5kfz").submit(function(event) {

    event.preventDefault();

    _go_beego_action_new();
});

var reopenurid = null;
function _go_beego_action_new()
{
    var urid = lessCryptoMd5("controllers/<?php echo $this->req->file?>");
    
    var item = h5cTabletPool[urid];

    if (item && item.url) {
        reopenurid = urid;
        lcEditor.EntrySave(urid, "_go_beego_action_new2");
    } else {
        _go_beego_action_new2(null);
    }
}

function _go_beego_action_new2(rs)
{
    if (reopenurid != null) {
        lcTabClose(reopenurid, 1);
    }

    console.log(rs);
    //return;
    var url = "/lesscreator/plugins/go-beego/fs-ov-action-new?func=action-new";

    var data = "proj="+ lessSession.Get("ProjPath");
    data += "&func_name="+ $("#td5kfz").find("select[name=func_name]").val();
    data += "&file=<?php echo $this->req->file?>";
    data += "&ctl=<?php echo $this->req->ctl?>";

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
                
                if (reopenurid != null) {
                        
                    var opt = {
                        'img': '/lesscreator/static/img/ht-page_white_golang.png',
                        'close': '1',
                    };

                    h5cTabOpen('controllers/<?php echo $this->req->file?>','w0','editor',opt);
                }
                
            } else {
                hdev_header_alert('error', obj.message);
            }

            if (typeof _plugin_go_beego_cvlist == 'function') {
                _plugin_go_beego_cvlist();
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
