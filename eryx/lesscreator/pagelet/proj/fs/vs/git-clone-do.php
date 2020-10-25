<?php

use LessPHP\Net\Http;

$req = array(
    'access_token' => $this->req->access_token,
    'data' => array(
        'git_url' => $this->req->git_url,
        'git_base' => $this->req->git_base,
        'git_target' => $this->req->git_target,
    ),
);
//print_r($req);

try {
    /* $cli = new Http("/lesscreator/api?func=vs-git-clone-ws");
    $ret = $cli->Post(json_encode($req));
    
    if ($ret != 200) {
        throw new \Exception("Service Unavailable");
    }

    $ret = json_decode($cli->getBody(), false);
    if (!isset($ret->status)) {
        throw new \Exception("Service Unavailable: ". $ret->message);
    } */

} catch (\Exception $e) {
    echo "<script>lessAlert('#iei0ne', 'alert-error', '".$e->getMessage()."');</script>";
}

?>
<style>
#y22xg9 {
    background-color: #333;
    color: #fff;
    min-height: 100px;
}
</style>
<div id="iei0ne" class="alert alert-info hide"><?php echo $this->T('Clone Git Repository')?></div>

<pre id="y22xg9"></pre>


<script type="text/javascript">

//lessModalButtonAdd("h1rwtp", "<?php echo $this->T('Back')?>", "lessModalPrev()", "pull-left h5c-marginl0");

lessModalButtonAdd("r3aenq", "<?php echo $this->T('Close')?>", "_proj_vs_console_close()", "");

var _proj_vs_sock = null;
var _proj_vs_wsuri = "ws://"+window.location.hostname+":9531/lesscreator/api/vs-git-clone-ws";
//console.log(_proj_vs_wsuri);

var req = {
    'access_token' : '<?php echo $this->req->access_token?>',
    'data' : {
        'git_url' : '<?php echo $this->req->git_url?>',
        'git_base' : '<?php echo $this->req->git_base?>',
        'git_target' : '<?php echo $this->req->git_target?>',
    }
}

var _proj_vs_buf_enter = false;
function _proj_vs_console_open()
{
    if (!("WebSocket" in window)) {
        return
    }
    if (_proj_vs_sock != null) {
        return
    }

    try {
        _proj_vs_sock = new WebSocket(_proj_vs_wsuri);

        _proj_vs_sock.onopen = function() {
            //console.log("connected to " + _proj_vs_wsuri);
            _proj_vs_console_send();
        }

        _proj_vs_sock.onclose = function(e) {
            //console.log("connection closed (" + e.code + ")");
            _proj_vs_sock = null;
        }

        _proj_vs_sock.onmessage = function(e) {
            
            var obj = JSON.parse(e.data);

            var _buf_line = "";
            var tmp = obj.data.output.split("");
            
            for (var i in tmp) {

                _buf_line += tmp[i].replace("\n", "");
                _buf_line = _buf_line.replace("\r", "");
                _buf_line = _buf_line.replace('[K', "");
                _buf_line = _buf_line.replace(']0;', "");

                if (tmp[i] == "\r") {

                    if (_proj_vs_buf_enter) {
                        if (_buf_line.length > 0) {
                            $('#y22xg9 div').last().empty().text(_buf_line);
                        }
                    } else {
                        if (_buf_line.length > 0) {
                            $('#y22xg9').append("<div>"+ _buf_line +"</div>");
                        }
                        _proj_vs_buf_enter = true;
                    }

                    _buf_line = "";
                }

                if (tmp[i] == "\n") {
                    if (_buf_line.length > 0) {
                        $('#y22xg9').append("<div>"+ _buf_line +"</div>");
                    }
                    _buf_line = "";
                    _proj_vs_buf_enter = false;
                }
            }
        }

    } catch(e) {
        //console.log("message open failed: "+ e);
    }
}

function _proj_vs_console_send()
{
    _proj_vs_sock.send(JSON.stringify(req));
}

function _proj_vs_console_close()
{
    if (_proj_vs_sock != null) {
        _proj_vs_sock.close();
    }
    
    lessModalClose();
}

_proj_vs_console_open();
</script>
