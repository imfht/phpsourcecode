<?php
function_exists('urls') || die('ERR');


$jscode = '';
if(fun('field@load_js',$field['type'])){
	$jscode = <<<EOT
<script type="text/javascript">
$(function(){
	$('.list_usergroup').each(function () {
		var base = $(this);

		//统计数据
		var count_value = function(){
			var vals = {};
			base.find('input.wri').each(function(){
				var gid = parseInt($(this).data("id"));
				vals[gid] = $(this).val();
				//if($(this).val()!='')vals.push({"gid":$(this).val()});
			});
			//vals.join(',')
			base.find('textarea').val( JSON.stringify(vals)  );
		}
		
		//输入框鼠标离开事件
		base.find('input.wri').on('blur',
				function(){
					count_value();
				}
		);

		base.find('input.wri').bind('keyup',function(e){
				if (event.keyCode == "13") {
					layer.alert('请点击底部的提交按钮来提交表单!');
				}
		});

		$(document).on("keypress", "input", function(event) { 
			return event.keyCode != 13;	//回车不能提交表单,请点击提交按钮!
		});
		
		//初始化
		var str = base.find('textarea').val();
		if(str!=''){
			obj = JSON.parse(str);
			for (var item in obj) {
				var jValue = obj[item]; //key所对应的value
				base.find('[data-id="' + item + '"]').val(jValue);
			}
		}
		
	});
});
</script>

EOT;

}

$groups = '';
foreach(getGroupByid(0) AS $key=>$vo){
	$groups .= "<div class='input-group'><span class='gtitle'>{$vo}</span><span style='padding-left:15px;' class='_input'><input type='text'class='wri' data-id='{$key}' value='' placeholder='请输入数值'></span></div>";
}

return <<<EOT


<div class="list_usergroup">
$groups
<textarea style="display:none;" id="atc_{$name}" name="{$name}" >{$info[$name]}</textarea>
</div>
$jscode

EOT;
;