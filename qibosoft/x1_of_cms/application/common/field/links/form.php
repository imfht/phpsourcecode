<?php
function_exists('urls') || die('ERR');


$jscode = '';
$jscode .= fun('field@load_js','jquiS')?'<SCRIPT LANGUAGE="JavaScript" src="'.STATIC_URL.'libs/jquery-ui/jquery-ui.min.js'.'"></SCRIPT>':'';
if(fun('field@load_js',$field['type'])){
	
	if(empty(input('hy_id'))){
		$jscode .= '<style type="text/css">.get-link{display:none;}</style>';
	}else{
		$linkurl = iurl("qun/get_link/index",['hy_id'=>input('hy_id')]);
	}
	$width = IN_WAP===true?'["95%","80%"]':'["800px","600px"]';
	$jscode .= <<<EOT
<script type="text/javascript">
jQuery(document).ready(function() {
	$('.list_links').each(function () {
		var base = $(this);

		$(document).on("keypress", "input", function(event) { 
			return event.keyCode != 13;	//回车不能提交表单,请点击提交按钮!
		});
		
		//移除按钮事件
		function init_delete(o){
			o.find('.del').click(function(){
				if(base.find('.del').length<2){
					layer.alert("必须要保留一个");
					return ;
				}
				$(this).parent().remove();
				count_value();
			});
		}
		init_delete(base);
		
		//加多一项
		base.find('.addmore').click(function(){
			copy_item().find("input").val('');
		});
		function copy_item(default_color){
			base.append( base.find('.input-group').first().clone() );
			var that = base.find('.input-group').last();
			set_init(that,default_color);
			return that;
		}
		
		//添加事件
		function set_init(that,default_color){
			init_icon(that);
			init_color(that,default_color);
			init_link(that);
			init_count(that);
			init_delete(that);
			init_down(that);
			init_up(that);
			init_copy(that);
		}
		

		//统计数据
		function count_value(){
			var vals = [];
			base.find('.input-group').each(function(){
				vals.push({
					url:$(this).find(".url input").val(),
					icon:$(this).find(".icon input").val(),
					title:$(this).find(".title input").val(),						
					font_color:$(this).find(".font_color input").val(),
					bgcolor:$(this).find(".bgcolor input").val(),
					about:$(this).find(".about input").val(),
					tagid:$(this).find(".tagid input").val(),
				});
			});
			//vals.join(',')
			base.find('textarea').val( JSON.stringify(vals)  );
		}

		
		//创建统计事件
		function init_count(o){
			o.find('input').on('blur',function(){
				count_value();
			});
		}
		init_count(base);

		//选择图标
		function init_icon(o){
			o.find(".get-icon").click(function(){
				var that = $(this);
				layer.open({
					title:'点击图标即可选中',
					type:2,
					area:{$width},
					content:'/public/static/icon/index.html',
					end:function(){
						that.prev().val(icon_name);
						that.attr("class","get-icon "+icon_name);
                        that.prev().focus();
					}
				});
			});
		}
		init_icon(base);
		
		//选择颜色
		function init_color(o,default_color){
			o.find(".get-color").each(function(){
				var that = $(this);
				var _color = '#ccc';				
				if(typeof(default_color)=='object'){
					var _c = default_color[that.parent().parent().attr("class")];
					if(_c!=undefined && _c!=''){
						_color = _c;
					}					
				}
				layui.use('colorpicker', function(){
						var colorpicker = layui.colorpicker;
						colorpicker.render({
							elem: that,
							color: _color,//default_color!=undefined&&default_color!=''?default_color:'#ccc'
							done: function(color){
								that.parent().parent().find("input").val(color);
								count_value();
							}
						});
				});			
			});
		}
		init_color(base);
		
		//选择链接
		function init_link(o){
			o.find(".get-link").click(function(){
				var that = $(this).parent().parent();
				select_menu();
				link_obj = {
					url:that.find(".url input"),
					icon:that.find(".icon input"),
					title:that.find(".title input"),
				}
			});
		}
		init_link(base);

		//复制一个
		function init_copy(o){
			o.find(".copy").click(function(){
				var that = $(this).parent();
				that.after(that.clone());
				set_init(that.next(),{font_color:that.find(".font_color input").val(),bgcolor:that.find(".bgcolor input").val()});	that.next().find(".url input").val('');
				that.next().find(".title input").val('').focus();
				count_value();
			});
		}
		init_copy(base);
		
		//下移
		function init_down(o){
			o.find(".down").click(function(){
				var that = $(this).parent();
				if(that.next().hasClass('input-group')){
					that.next().after(that.clone());
					set_init(that.next().next(),{font_color:that.find(".font_color input").val(),bgcolor:that.find(".bgcolor input").val()});
					that.remove();
					count_value();
				}else{
					layer.alert('到尽头了');
				}								
			});
		}
		init_down(base);

		//上移
		function init_up(o){
			o.find(".up").click(function(){
				var that = $(this).parent();
				if(that.prev().hasClass('input-group')){
					that.prev().before(that.clone());
					set_init(that.prev().prev(),{font_color:that.find(".font_color input").val(),bgcolor:that.find(".bgcolor input").val()});
					that.remove();
					count_value();
				}else{
					layer.alert('到尽头了');
				}								
			});
		}
		init_up(base);
		
		//初始化数据
		var str = base.find("textarea").val();
		if(str!=''){
			var ar = JSON.parse(str);
			ar.forEach((rs)=>{
					var that = copy_item({font_color:rs.font_color,bgcolor:rs.bgcolor});
					that.find(".url input").val(rs.url);
					that.find(".icon input").val(rs.icon);
					that.find(".title input").val(rs.title);
					that.find(".font_color input").val(rs.font_color);
					that.find(".bgcolor input").val(rs.bgcolor);
					that.find(".about input").val(rs.about);
					that.find(".tagid input").val(rs.tagid);

					that.find(".get-icon").attr('class',"get-icon "+rs.icon);
			});
			if(ar.length>0){
				base.find('.input-group').first().remove();	//因为第一项内容为空,所以要删除
			}
		}

		base.sortable({
			handle:".move",
			revert: true,
			axis: 'y', 
			items: '.input-group', 
			stop:function(){
				count_value();
			}
		});

	});

	
});

if("{$linkurl}"!=""){
	$.get("{$linkurl}",function(res){
		$("body").append(res);
	});
}

 

var icon_name;
function geticon(s){	//获取图标,固定函数
	icon_name = s;
}

var link_obj;
function editurl(url,icon,title){	//获取链接,固定函数
    link_obj.url.val(url);
	link_obj.icon.val(icon);
	link_obj.title.val(title);
    HiddenMoreAciton();
	link_obj.url.focus();
}

</script>

EOT;

}

 
//$array = json_decode($info[$name],true);
 


return <<<EOT
<style type="text/css">
.list_links .input-group-addon{
	font-size:20px;
	margin-right:10px;
}
</style>
<div class="list_links">
	<span class='addmore' style="cursor:pointer;">
		<i style="font-size:18px;" class=' fa fa-plus-square'></i> 增加一项
	</span>
	<textarea style="display:none;" id="{$name}" name="{$name}">{$info[$name]}</textarea>
	<div class='input-group' style='border:1px dotted #ccc;padding:8px;margin:8px;'>
			<span class='input-group-addon del' title="删除"><i class='fa fa-fw fa-close'></i></span> 
			<span class='input-group-addon move' style="cursor:move;" title="移动"><i class='fa fa-arrows'></i></span>
			<span class='input-group-addon down' title="下移"><i class='fa fa-arrow-down'></i></span>
			<span class='input-group-addon up' title="上移"><i class='fa fa-arrow-up'></i></span>
			<span class='input-group-addon copy' title="复制一个"><i class='fa fa-copy'></i></span>
			<span class="mc">
				<div class='url'>链接:<input type='text' placeholder='必填'> <span class="get-link fa fa-chain">选择</span></div>
				<div class='title'>标题:<input type='text' placeholder='必填'></div>				
				<div class='icon'>图标:<input type='text' placeholder='必选'> <span class="get-icon fa fa-smile-o" style="width:50px"> 选择</span></div>				
				<div class='font_color'>
					<ul class='layui-input-inline'>颜色:<input type='text' placeholder='字体颜色,非必选' /></ul>
					<ul class='layui-inline' style='left: -44px;'><li class='get-color'></li></ul>
				</div>
				<div class='bgcolor'>
					<ul class='layui-input-inline'>背景:<input type='text' placeholder='背景颜色,非必选' /></ul>
					<ul class='layui-inline' style='left: -44px;'><li class='get-color'></li></ul>
				</div>
				<div class='about'>描述:<input type='text' placeholder='描述介绍,一般留空'></div>
				<div class='tagid'>分组:<input type='text' placeholder='一般留空,可扩展为二级菜单'> <span class="glyphicon glyphicon-question-sign" onclick="layer.alert('分组标志，一般为空。<br>设置的话(可用任何字符)，模板可以自由发挥排版。<br>比如可以做高亮显示标志。<br>或者扩展为二级菜单，即同一个分组的第一个就是一级菜单，其它就是二组菜单。')"></span></div>
			</span>
   </div>
</div>
$jscode
 
EOT;
;