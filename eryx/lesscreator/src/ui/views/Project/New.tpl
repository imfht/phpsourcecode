
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

<form id="sdtqvj" action="/lesscreator/project/new/" method="post">
   
  <table width="100%" class="table table-condensed">
    
    <tr class="bordernil">
      <td width="180px"><strong>{{T . "Project ID"}}</strong> </td>
      <td>
        <input id="_proj_new_projid" name="projid" type="text" class="span2" value="{{.projid}}" />
        <label class="label label-important">{{T . "Required"}}</label>
        <label class="help-inline">{{T . "Unique identifier, similar to the package name"}}</label>
      </td>
    </tr>

    <tr>
      <td><strong>{{T . "Display Name"}}</strong> </td>
      <td >
        <input id="_proj_new_name" name="name" type="text" class="span2" value="" />
        <label class="label label-important">{{T . "Required"}}</label>
        <label class="help-inline">{{T . "Example"}}: Hello World</label>
      </td>
    </tr>

    <tr>
      <td><strong>{{T . "Group by Application"}}</strong></td>
      <td class="r0330s">
        {{range $k, $v := .grpapp}}
        <label class="item checkbox">
            <input class="_proj_new_grpapp" type="checkbox" name="grp_app" value="{{$k}}" /> {{$v}}
        </label>
        {{end}}
      </td>
    </tr>

    <tr>
      <td><strong>{{T . "Group by Develop"}}</strong></td>
      <td class="r0330s">
        {{range $k, $v := .grpdev}}
        <label class="item checkbox">
            <input class="_proj_new_grpdev" type="checkbox" name="grp_dev" value="{{$k}}" /> {{$v}}
        </label>
        {{end}}
      </td>
    </tr>
    
    <tr>
      <td valign="top"><strong>{{T . "Description"}}</strong></td>
      <td ><textarea id="_proj_new_desc" name="desc" rows="2" style="width:400px;"></textarea></td>
    </tr>

  </table>
</form>


<script>
if (lessModalPrevId() != null) {
    lessModalButtonAdd("jwyztd", "{{T . "Back"}}", "lessModalPrev()", "pull-left h5c-marginl0");
}

lessModalButtonAdd("d4ngex", "{{T . "Confirm and Create"}}", "_project_new_commit()", "btn-inverse");
lessModalButtonAdd("p5ke7m", "{{T . "Close"}}", "lessModalClose()", "");


var _project_new = "";

function _project_new_commit()
{
    var req = {
        projid : $("#_proj_new_projid").val(),
        name   : $("#_proj_new_name").val(),
        desc   : $("#_proj_new_desc").val(),
    }

    req.projid = "demo"; // TODO

    var grpapp = [];
    $("._proj_new_grpapp:checked").each(function(){
        grpapp.push($(this).val());
    });
    if (grpapp.length > 0) {
        req.grp_app = grpapp.join();
    }

    var grpdev = [];
    $("._proj_new_grpdev:checked").each(function(){
        grpdev.push($(this).val());
    });
    if (grpdev.length > 0) {
        req.grp_dev = grpdev.join();
    }

    req.success = function(rsp) {

        _project_new = rsp.path;

        $("#sdtqvj").hide(100);

        lessAlert("#m4ph6m", "alert-success", "<p><strong>{{T . "Successfully Done"}}</strong> \
            <button class=\"btn btn-success\" onclick=\"_project_new_goto()\">{{T . "Open this Project"}}</button>");
    }

    req.error = function(status, message) {
        lessAlert("#m4ph6m", "alert-error", "Error: "+ message);
    }

    lcProject.New(req);
}

function _project_new_goto()
{
    lcProject.Open(_project_new);
    lessModalClose();
}

</script>
