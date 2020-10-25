<?php
function_exists('urls') || die('ERR');


$get_time_url = iurl('wxapp.api/get_time_byday');
$jscode = fun('field@load_js','laydate')?"<script type='text/javascript'>if(typeof(layui)=='undefined'){document.write(\"<script LANGUAGE='JavaScript' src='$static/layui/layui.js'><\\/script>\");}</script><link rel='stylesheet' href='$static/layui/css/layui.css' media='all'>":'';
if(fun('field@load_js',$field['type'])){
	$jscode = <<<EOT
<script type="text/javascript">
jQuery(document).ready(function() {
	var laydate;
	layui.use('laydate', function(){
		laydate = layui.laydate;			
	});
	$('.list_daytime').each(function () {
		var base = $(this);
		var basehtml = base.find('div.input-group:first').prop("outerHTML");
		//base.append(basehtml);
		
		$(document).on("keypress", "input", function(event) { 
			return event.keyCode != 13;	//回车不能提交表单,请点击提交按钮!
		});

		//统计数据
		var count_value = function(){
			var vals = [];
			base.find('input.day_date').each(function(){
				if($(this).val()!='')vals.push($(this).val()+'|'+$(this).next().val());
			});
			//vals.join(',')
			base.find('textarea').val( JSON.stringify(vals)  );
		}
		
		//输入框鼠标离开事件
		var blur_act = function(){
			base.find('input.wri').on('blur',function(){
					count_value();
				});

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
			base.find('span.add').on('click',function(){
					$(this).parent().after(basehtml);
					$(this).parent().next().find("input").val('');
					init_act();
				}
			);
		}

		//移除按钮事件
		var del_act = function(){
			base.find('span.del').on('click',function(){
				$(this).parent().remove();
				count_value();
			});
		}
		
		//添加自动获取时间组件
		var add_getday = function(){
			base.find('input').off('click');
			setTimeout(function(){ //页面初始化时,laydate加载需要时间
				base.find('input.day_date').each(function(){
					var id = ('day_'+Math.random()).replace('.','');
					$(this).attr('id',id); 
					laydate.render({elem: '#'+id,type: 'date',done: function(value, date){
						count_value();
					}});
				});
			},300);

			base.find('input.day_times').click(function(){
				var that = $(this);
				var day = that.prev().val();
				if(day==''){
					layer.alert('请先选择日期,再设置时间段');
					return ;
				}
				var type = $("#form_group_timesort input[name=timesort]:checked").val();
				$.get("{$get_time_url}?day="+day+"&type="+(type?type:0),function(res){
					if(res.code==0){
						var str = "";
						res.data.forEach((rs)=>{
							var is_ck = (','+that.val()+',').indexOf(','+rs.id+',')>-1 ? 'checked' : '';
							str += '<input type="checkbox" name="time[]" '+is_ck+' value="'+rs.id+'">'+rs.name+'<br>';
						});
						var id = ('day_'+Math.random()).replace('.','');
						$(this).attr('id',id); 
						layer.alert("<div class='choose_daytime'>"+str+"</div>",{title:'请选择时间段'},function(i){
							var va = '';
							$(".choose_daytime input:checked").each(function(){
								va += ','+$(this).val();
							});
							that.val( va.substring(1) );
							count_value();
							layer.close(i)
						});
					}else{
						layer.msg('没有时间段可选择!');
					}
				});				
			});
		}

		var init_act = function(){console.log('长',44);
			base.find('span').off('click');
			base.find('input').off('blur');			
			add_act();
			del_act();
			blur_act();
			down_act();
			up_act();
			add_getday();
			count_value();
		}
		init_act();

	});
});
</script>

EOT;

}

$groups = '<style type="text/css">
.input-group .day_date{width:130px;}
.input-group .day_times{width:80px;}
@media (max-width:600px) {
	.input-group .day_date{width:110px;}
	.input-group .day_times{width:50px;}
}
</style>';
$array = json_decode($info[$name],true);
if($array){
	foreach($array AS $key=>$vo){
		list($title,$price,$num) = explode('|',$vo);
		$groups .= "<div class='input-group'>
			<span class='input-group-addon add'><i class='fa fa-plus-square'></i></span>
			<input class='wri day_date' type='text' value='{$title}' placeholder='格式:2020-09-08'>
			<input class='wri day_times' type='text' value='{$price}' placeholder='时间段'>
			<span class='input-group-addon del'><i class='fa fa-fw fa-close'></i></span>
			<span class='input-group-addon down'><i class='fa fa-arrow-down'></i></span>
			<span class='input-group-addon up'><i class='fa fa-arrow-up'></i></span>
        </div>";
	}
}else{
	$groups .= "<div class='input-group'>
			<span class='input-group-addon add'><i class='fa fa-plus-square'></i></span>
			<input class='wri day_date' type='text' value='' placeholder='格式:2020-09-08'>
			<input class='wri day_times' type='text' value='' placeholder='时间段'>
			<span class='input-group-addon del'><i class='fa fa-fw fa-close'></i></span>
			<span class='input-group-addon down'><i class='fa fa-arrow-down'></i></span>
			<span class='input-group-addon up'><i class='fa fa-arrow-up'></i></span>
        </div>";
}


return <<<EOT


<div class="list_daytime">
$groups
<textarea style="display:none;" id="{$name}" name="{$name}" >{$info[$name]}</textarea>
</div>
$jscode

EOT;
;