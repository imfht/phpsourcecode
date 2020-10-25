
<div id="htowou" class="alert alert-info">
    <?php echo $this->T('Clone Git Repository')?>
</div>


<table width="100%">
<tr>
  <td valign="top" width="120px">
  	<img src="/lesscreator/static/img/vs/git-100.png" />
  </td>

  <td>
    <div class="label label-important"><?php echo $this->T('todo-unstable-desc')?></div>

	<form id="bvszs0" action="/lesscreator/proj/fs/vs/git-clone-do">
    	<label><?php echo $this->T('Repository URL')?></label>
    	<input type="text" name="git_url" class="swqv89 input input-xxlarge" value="https://github.com/eryx/tips.go.git"/>
    	
    	<label><?php echo $this->T('Project directory name')?></label>
    	<input type="text" name="git_target" class="input input-xxlarge" value="tips.go"/>

    	<label><?php echo $this->T('Create project as subdirectory of')?></label>
    	<input type="text" name="git_base" class="input input-xxlarge" readonly="readonly" value=""/>
	</form>
  </td>
</tr>
</table>

<script type="text/javascript">


lessModalButtonAdd("c8chlb", "<?php echo $this->T('Back')?>", "lessModalPrev()", "pull-left h5c-marginl0");

lessModalButtonAdd("fvxmpf", "<?php echo $this->T('Create Project')?>", "_proj_fs_vs_create_do()", "btn-inverse");

lessModalButtonAdd("r3aenq", "<?php echo $this->T('Close')?>", "lessModalClose()", "");


$("input[name=git_base]").val(lessSession.Get("basedir") +"/app/");

$("input[name=git_url]").bind("change", function(e) {
	//e.preventDefault();
    var url = $(this).val();

    var urls = url.split("\/").pop().split(".");
    urls.pop();

    $("input[name=git_target]").val(urls[0]);
    //console.log("AA"+ urls[0]);
});


$("#bvszs0").submit(function(event) {
    event.preventDefault(); 
    _proj_fs_vs_create_do();
});

// git@github.com:eryx/tips.go.git
// https://github.com/eryx/tips.go.git
function _proj_fs_vs_create_do()
{
    console.log($("#bvszs0").serialize());

    var url = '/lesscreator/proj/fs/vs/git-clone-do';
    url += '?access_token='+ lessCookie.Get("access_token");
    url += '&'+ $("#bvszs0").serialize();
    lessModalNext(url, "<?php echo $this->T('Create Project')?>", null);

    return;
    var req = {
        "access_token": lessCookie.Get("access_token"),
        "data": {
            "git_url": $("#bvszs0 input[name=git_url]").val(),
            "git_target": $("#bvszs0 input[name=git_target]").val(),
            "git_base": $("#bvszs0 input[name=git_base]").val(),
        }
    }
    
    $.ajax({
        type    : "POST",
        url     : $("#bvszs0").attr('action'),
        data    : $("#bvszs0").serialize(),
        success : function(rsp) {

            try {
                var rsj = JSON.parse(rsp);
            } catch (e) {
                lessAlert("#htowou", "alert-error", "<?php echo $this->T('Service Unavailable')?>");
                return;
            }

            if (rsj.status == 200) {

                //_basedir = $("#basedir").val();
                //_projid  = $("#projid").val();

                lessAlert("#htowou", "alert-success", "<p><strong><?php echo $this->T('Successfully Done')?></strong> \
                    <button class=\"btn btn-success\" onclick=\"_proj_new_goto()\"><?php echo $this->T('Open this Project')?></button>");

            } else {
                lessAlert("#htowou", "alert-error", rsj.message);
            }
        },
        error: function(xhr, textStatus, error) {
            lessAlert("#htowou", "alert-error", xhr.responseText);
        }
    });

    return;
}


</script>
