<style>
.ciyhh9 {
    border-bottom: 1px solid #ccc;
}
.ciyhh9 img.licon {
    width: 30px;
    height: 30px;
}
.ciyhh9 .title {
    margin: 5px 0 0 0;
    padding: 0;
    font-weight: bold;
    font-size: 16px;
    line-height: 16px;
}
.ciyhh9 .spec {
    margin: 10px 0 0 0;
    padding: 0;
    font-size: 10px;
    line-height: 10px;
    color: #999;
}
.ciyhh9 :hover {
    background-color: #d9edf7;
}
.ciyhh9 > table {
    width: 100%;
    border-bottom: 1px solid #ccc;
}
.ciyhh9 > table td {
    padding: 5px;
}

</style>

<div id="ztk4yq56" class="alert alert-info">
	Connecting lessOS Cloud Engine to get your boxes ...
</div>

<div id="i7egk4aw"></div>

<div id="i7egk4aw-tpl" class="hide">
{[~it.items :v]}
<a class="ciyhh9" href="#box/{[=v.id]}" onclick="_box_open('{[=v.id]}')">
<table>
  <tr>
    <td width="40px"><img class="licon" src="/lesscreator/~/lesscreator/img/gen/box01.png" /></td>
    <td>
        <div class="title">{[=v.name]}</div>
        <div class="spec">CPU Share: {[=v.spec.cpu_num]}x, Memory: {[=v.spec.mem_size]} MB, Storage: {[=v.spec.stor_size]} MB</div>
    </td>
    <td width="30px" align="right">
      <i class="icon-chevron-right"></i>
    </td>
  </tr>
</table>
</a>
{[~]}
</div>

<script type="text/javascript">

// lessModalButtonAdd("doo8l6", "{{T . "Close"}}", "lessModalClose()", "");

function _load_boxlist()
{
    var url = lessfly_api + "/box/list?";
    url += "access_token="+ lessCookie.Get("access_token");
    url += "&project=lesscreator";
    // console.log(url);
    $.ajax({
        url     : url,
        type    : "GET",
        timeout : 30000,
        success : function(rsp) {

            var rsj = JSON.parse(rsp);

            // console.log(rsp);

            if (rsj.status == 200) {
                
                //$(".load-progress-msg").append("OK");

                if (rsj.data.totalItems == 0) {
                    // TODO
                } else if (rsj.data.totalItems == 1) {
                    // Launch Immediately
                } else if (rsj.data.totalItems > 1) {
                    // Select one to Launch ...
                    lessTemplate.RenderFromId("i7egk4aw", "i7egk4aw-tpl", rsj.data);
                }



            } else {
                // $(".load-progress").removeClass("progress-success").addClass("progress-danger");
                // lessAlert("#ztk4yq56", "alert-error", rsj.message);
            }
        },
        error   : function(xhr, textStatus, error) {
            // $(".load-progress").removeClass("progress-success").addClass("progress-danger");
            // lessAlert("#ztk4yq56", "alert-error", "Failed on Initializing System Environment");
        }
    });
}

_load_boxlist();

function _box_open(boxid)
{
    lessModalClose();
    lessSession.Set("boxid", boxid);
    lcBodyLoader("index/desk");
    // lessModalNext("/lesscreator/index/box-open?boxid="+ boxid, "Log In My Box", null);
}

</script>
