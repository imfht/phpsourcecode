<?php
function_exists('urls') || die('ERR');

$showicon = $info[$name]?:'fa fa-fw fa-file-text';

$jscode = '';
if(fun('field@load_js',$field['type'])){
	$width = IN_WAP===true?'["95%","80%"]':'["800px","600px"]';
	$jscode .= <<<EOT

<style type="text/css">
.js-icon-picker .input-group-addon {
    cursor: pointer;
}
.js-icon-list {
    padding-left: 0;
    padding-bottom: 1px;
    margin-bottom: 20px;
    list-style: none;
    overflow: hidden;
}
.js-icon-list li{
    float: left;
    width: 5%;
    padding: 15px;
    margin: 0 -1px -1px 0;
    font-size: 12px;
    line-height: 1.4;
    text-align: center;
    border: 1px solid #ddd;
    cursor: pointer;
}
.js-icon-list li:hover {
    background-color: #F5F5F5;
}
.js-icon-list li code {
    display: none;
}
</style>
<script type="text/javascript">
    // 打开图标选择器
$(function(){
	$(".js-icon-picker").each(function(){
		var base = $(this);
		base.find(".icon").click(function(){
			var that = $(this);
			layer.open({
					title:'点击图标即可选中',
					type:2,
					area:{$width},
					content:'/public/static/icon/index.html',
					end:function(){
						that.next().val(icon_name);
						that.find("i").attr("class",icon_name);
                        that.next().focus();
					}
			});
		});

		base.find('.delete-icon').click(function(event){
			event.stopPropagation();
			if ($(this).prev().is(':disabled')) {
				return;
			}
			$(this).prev().val('');
			$(this).prev().prev().html('<i class="fa fa-fw fa-plus-circle"></i>');
		});

	});
});


var icon_name;
function geticon(s){	//获取图标,固定函数
	icon_name = s;
}
    
</script>

EOT;

}

return <<<EOT
$jscode
	<div class="input-group js-icon-picker">
            <span class="input-group-addon icon"><i class="{$showicon}"></i></span>
            <input class="icon_input" style="width:300px;" type="text" id="atc_{$name}" name="{$name}" value="{$info[$name]}" placeholder="请选择图标" >
            <span class="input-group-addon delete-icon"><i class="fa fa-times"></i></span>
    </div>
EOT;
