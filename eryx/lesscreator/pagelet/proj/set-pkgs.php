<?php

use LessPHP\Encoding\Json;
use LessPHP\LessKeeper\Keeper;
use LessPHP\Net\Http;

$info = lesscreator_proj::info($this->req->proj);
if (!isset($info['projid'])) {
    die($this->T('Bad Request'));
}


if (in_array($_SERVER['REQUEST_METHOD'], array('POST', 'PUT'))) {

    $ret = array("status" => 200, "message" => null);

    $projPath = lesscreator_proj::path($this->req->proj);
    $lcpj = "{$projPath}/lcproject.json";

    try {

        $dps = explode(",", $info['depends']);

        foreach ($dps as $k => $pkg) {

            if ($pkg == $this->req->projid) {
                
                unset($dps[$k]);
                $info['depends'] = implode(",", $dps);

                $str = Json::prettyPrint($info);
                $rs = lesscreator_fs::FsFilePut($lcpj, $str);
                if ($rs->status != 200) {
                    throw new \Exception($rs->message, 400);
                }

                $ret['data']['status'] = 0;
                throw new \Exception($this->T('Successfully Done'), 200);
            }
        }


        $c = new Http("http://127.0.0.1:9531/lesscreator/api/env-pkgsetup");
        $req = array(
            "access_token" => $this->req->access_token,
            "data" => array("projid" => $this->req->projid),
        );
        if ($c->Post(json_encode($req)) != 200) {
            throw new \Exception($this->T('Processing Failed'), 400);
        }
        $rs = json_decode($c->getBody(), false);
        if ($rs->status != 200) {
            throw new \Exception($this->T('Processing Failed'), 400);
        }


        $dps[] = $this->req->projid;
        $info['depends'] = implode(",", $dps);


        $str = Json::prettyPrint($info);
        $rs = lesscreator_fs::FsFilePut($lcpj, $str);
        if ($rs->status != 200) {
            throw new \Exception($rs->message, 400);
        }

        $ret['data']['status'] = 1;

    } catch (\Exception $e) {
        $ret['status']  = $e->getCode();
        $ret['message'] = $e->getMessage();
    }

    die(json_encode($ret));
}

$kpr = new Keeper();

$rs = $kpr->LocalNodeListAndGet("/lf/pkg/");

$dps = explode(",", $info['depends']);

?>

<style type="text/css">
.m3w0wf {
    width: 70px; text-align: center;
}
</style>
<div id="ekjujo" class="hide"></div>

<form id="d3tmtf" action="/lesscreator/proj/set-pkgs/" method="post">


<table class="table table-condensed">

<thead>
<tr>
<th width="160"><?php echo $this->T('Name')?></th>
<th width="100"><?php echo $this->T('Version')?></th>
<th><?php echo $this->T('Description')?></th>
<th width="100"></th>
</tr>
</thead>
<?php

    foreach ($rs->elems as $v) {

        $v = json_decode($v->body);
        if (!isset($v->projid)) {
            continue;
        }

        if (in_array($v->projid, array('lesscreator', 'user'))) {
            continue;
        }

        $cksta = '';
        $optit = '<i class="icon-plus-sign"></i> '. $this->T('Append');
        $optcla = '';
        if (in_array($v->projid, $dps)) {
            $cksta = '<i class="icon-ok-circle"></i>';
            $optit = '<i class="icon-remove-sign icon-white"></i> '. $this->T('Remove');
            $optcla = 'btn-success';
        }

        if (mb_strlen($v->summary, "utf-8") > 100) {
            $v->summary = mb_substr($v->summary, 0, 100, "utf-8") . "...";
        }

        echo "<tr>
            <td>{$v->name}</td>
            <td>{$v->version}</td>
            <td>{$v->summary}</td>
            <td id='proj-pkg-{$v->projid}'><a class='m3w0wf btn btn-small {$optcla} pull-right' href='#{$v->projid}'>{$optit}</a></td>
        </tr>";
    }
?>
</table>
</form>

<script>

lessModalButtonAdd("z7tgxo", "<?php echo $this->T('Confirm and Save')?>", "_proj_pkg_save()", "btn-inverse");
lessModalButtonAdd("vkbmpc", "<?php echo $this->T('Close')?>", "lessModalClose()", "");

$(".m3w0wf").click(function() {

    var projid = $(this).attr('href').substr(1);

    $(this).text("Pending");
    //$("#proj-pkg-"+ projid).find("a").text("Pending");

    //return;

    var data = "access_token="+ lessCookie.Get("access_token");
    data += "&proj="+ lessSession.Get("ProjPath");
    data += "&projid="+ projid;

    $.ajax({
        type    : "POST",
        url     : "/lesscreator/proj/set-pkgs/",
        data    : data,
        success : function(rsp) {
        
            console.log(rsp);

            try {
                var rsj = JSON.parse(rsp);
            } catch (e) {
                lessAlert("#ekjujo", "alert-error", "<?php echo $this->T('Service Unavailable')?>");
                return;
            }

            if (rsj.status == 200) {

                lessAlert("#ekjujo", "alert-success", "<?php echo $this->T('Successfully Done')?>");


                if (rsj.data.status == 0) {
                    $("#proj-pkg-"+ projid).find("a")
                        .removeClass("btn-success")
                        .html('<i class="icon-plus-sign"></i> <?php echo $this->T('Append')?>');
                } else {
                    $("#proj-pkg-"+ projid).find("a")
                        .removeClass("btn-success").addClass("btn-success")
                        .html('<i class="icon-remove-sign icon-white"></i> <?php echo $this->T('Remove')?>');
                }

                //$(this).removeClass("Pending");
                _proj_pkgs_refresh();
                //lessModalClose();

            } else {
                lessAlert("#ekjujo", "alert-error", rsj.message);
            }
        },
        error: function(xhr, textStatus, error) {
            lessAlert("#ekjujo", "alert-error", xhr.responseText);
        }
    });
});

function _proj_pkg_save()
{
    $.ajax({
        type    : "POST",
        url     : $("#d3tmtf").attr('action'),
        data    : $("#d3tmtf").serialize() +"&proj="+ lessSession.Get("ProjPath"),
        success : function(rsp) {
            //console.log(rsp);
            try {
                var rsj = JSON.parse(rsp);
            } catch (e) {
                lessAlert("#ekjujo", "alert-error", "<?php echo $this->T('Service Unavailable')?>");
                return;
            }

            if (rsj.status == 200) {

                lessAlert("#ekjujo", "alert-success", "<?php echo $this->T('Successfully Done')?>");
                _proj_pkgs_refresh();
                lessModalClose();

            } else {
                lessAlert("#ekjujo", "alert-error", rsj.message);
            }
        },
        error: function(xhr, textStatus, error) {
            lessAlert("#ekjujo", "alert-error", xhr.responseText);
        }
    });

    return;
}

</script>
