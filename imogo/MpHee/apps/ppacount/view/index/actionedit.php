<h2>添加管理行为：</h2>
<hr class="mb10"></hr>

<div id="con_one_1" class="form_box">
		<table>
			<tr>
				<th>功能管理：</th>
				<td>
					<label><input name="ppid" type="radio" value="{$ppinfo['id']}" checked="checked"/><span>{$ppinfo['name']}</span></label>
				</td>
			</tr>
			<tr>
				<th>管理功能：</th>
				<td>
					{loop $apps $app $config}
						<label><input name="actions" type="checkbox" value="{$app}" /><span>{$config['APP_NAME']}</span></label>
					{/loop}
				</td>
			</tr>
		</table>
</div>
<div class="btn">
	<div class="button" id="okbtn">确定</div>
    <div class="button" id="closebtn">取消</div>
</div>
<script type="text/javascript">
selectCheckbox('actions','{$actions}');

function selectCheckbox(name,value) {     
    var checkObject = document.getElementsByName(name);     
    var values = value.split("_");     
for(var j = 0; j < values.length; j++)     
    {     
for (var i = 0; i < checkObject.length; i++)      
        {     
if(checkObject[i].value == values[j])     
            {     
                checkObject[i].checked = true;  
				break;     
            }     
        }     
    }     
}

Do.ready('base','layer', function(){

var index = parent.layer.getFrameIndex(window.name); //获取当前窗体索引

$('#closebtn').on('click', function(){
    parent.layer.close(index); //执行关闭
});

$('#okbtn').on('click', function(){
    var ppid = $(':radio[name="ppid"]:checked').val();
	var ppname = $(':radio[name="ppid"]:checked + span').text();
	
	var text='<td data="'+ppid+'">'+ppname+'</td><td>';  
    $("[name='actions']:checked").each(function() {  
        text += '<span class="mr10 mb5" data="'+$(this).val()+'">'+$(this).siblings('span').text()+'<i onclick="$(this).parent().remove();"> X</i></span>'; 
    });
	text += '</td><td><div onclick="editaction(this);" class="button"><i class="fa fa-edit fa-lg"></i> 修改</div><div onclick="$(this).parent().parent().remove();" class="button"><i class="fa fa-trash-o fa-lg"></i> 删除</div></td>';
	
	parent.$('#tr'+ppid).html(text);
    parent.layer.close(index);
});

});
</script>