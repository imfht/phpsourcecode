Box Open ...

<script type="text/javascript">

// if (lessModalPrevId() != null) {
//     lessModalButtonAdd("laab5w", "{{T . "Back"}}", "lessModalPrev()", "pull-left");
// }
lessModalButtonAdd("yyixb9", "{{T . "Close"}}", "lessModalClose()", "");


function _box_start()
{
    var url = lessfly_api + "/box/cmd?";
    url += "access_token="+ lessCookie.Get("access_token");
    url += "&boxid={{.boxid}}";
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
                    // lessTemplate.RenderFromId("i7egk4aw", "i7egk4aw-tpl", rsj.data);
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

</script>