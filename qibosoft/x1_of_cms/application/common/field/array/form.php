<?php
function_exists('urls') || die('ERR');


$jscode = '';
if(fun('field@load_js',$field['type'])){
	$jscode = <<<EOT
<script type="text/javascript">
jQuery(document).ready(function() {
	$('.list_array').each(function () {
		var base = $(this);
		var basehtml = base.find('div.input-group:first').prop("outerHTML");
		//base.append(basehtml);
		
		$(document).on("keypress", "input", function(event) { 
			return event.keyCode != 13;	//回车不能提交表单,请点击提交按钮!
		});

		//统计数据
		var count_value = function(){
			var vals = [];
			base.find('input.wri').each(function(){
				if($(this).val()!='')vals.push($(this).val());
			});
			//vals.join(',')
			base.find('textarea').val( JSON.stringify(vals)  );
		}
		
		//输入框鼠标离开事件
		var blur_act = function(){
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
		}

		//下移
		function down_act(){
			base.find("span.down").click(function(){
				var that = $(this).parent();
				if(that.next().hasClass('input-group')){
					that.next().after(that.clone());
					that.remove();
					init_act();
				}else{
					layer.alert('到尽头了');
				}								
			});
		}		

		//上移
		function up_act(){
			base.find("span.up").click(function(){
				var that = $(this).parent();
				if(that.prev().hasClass('input-group')){
					that.prev().before(that.clone());
					that.remove();
					init_act();
				}else{
					layer.alert('到尽头了');
				}								
			});
		}
		
		//添加按钮事件
		var add_act = function(){
			base.find('span.add').on('click',
				function(){
					$(this).parent().after(basehtml);
					$(this).parent().next().find("input").val('');
					init_act();
				}
			);
		}

		//移除按钮事件
		var del_act = function(){
			base.find('span.del').on('click',
				function(){
					$(this).parent().remove();
					count_value();
				}
			);
		}

		var init_act = function(){
			base.find('span').off('click');
			base.find('input').off('blur');
			add_act();
			del_act();
			blur_act();
			down_act();
			up_act();
			count_value();
		}
		init_act();
	});
});
</script>

EOT;

}

$groups = '';
$array = json_decode($info[$name],true);
if($array){
	foreach($array AS $key=>$vo){
		$groups .= "<div class='input-group'>
			<span class='input-group-addon add'><i class='fa fa-plus-square'></i></span>
			<input class='wri' type='text' value='{$vo}' placeholder='请输入' >			
			<span class='input-group-addon del'><i class='fa fa-fw fa-close'></i></span>
			<span class='input-group-addon down'><i class='fa fa-arrow-down'></i></span>
			<span class='input-group-addon up'><i class='fa fa-arrow-up'></i></span>
        </div>";
	}
}else{
	$groups="<div class='input-group'>
			<span class='input-group-addon add'><i class='fa fa-plus-square'></i></span>
			<input class='wri' type='text' value='' placeholder='请输入' >			
			<span class='input-group-addon del'><i class='fa fa-fw fa-close'></i></span>
			<span class='input-group-addon down'><i class='fa fa-arrow-down'></i></span>
			<span class='input-group-addon up'><i class='fa fa-arrow-up'></i></span>
        </div>";
}


return <<<EOT


<div class="list_array">
$groups
<textarea style="display:none;" id="{$name}" name="{$name}" >{$info[$name]}</textarea>
</div>
$jscode

EOT;
;