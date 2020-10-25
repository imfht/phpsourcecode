<?php
/**
*	模块：news.新闻资讯
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.新闻资讯]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'news',modename='新闻资讯',isflow=0,modeid='88',atype = params.atype,pnum=params.pnum,modenames='',listname='bmV3cw::';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"title","name":"\u6807\u9898","fieldstype":"text","ispx":"0","isalign":"1","islb":"1"},{"fields":"fengmian","name":"\u5c01\u9762\u56fe\u7247","fieldstype":"uploadimg","ispx":"0","isalign":"1","islb":"1"},{"fields":"typename","name":"\u7c7b\u578b\u540d\u79f0","fieldstype":"rockcombo","ispx":"1","isalign":"0","islb":"1"},{"fields":"content","name":"\u5185\u5bb9","fieldstype":"htmlediter","ispx":"0","isalign":"0","islb":"0"},{"fields":"recename","name":"\u53d1\u9001\u7ed9","fieldstype":"changedeptusercheck","ispx":"0","isalign":"0","islb":"1"},{"fields":"url","name":"\u76f8\u5e94\u5730\u5740","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"zuozhe","name":"\u6765\u6e90","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"indate","name":"\u65e5\u671f","fieldstype":"date","ispx":"1","isalign":"0","islb":"1"},{"fields":"optname","name":"\u53d1\u5e03\u4eba","fieldstype":"text","ispx":"1","isalign":"0","islb":"1"},{"fields":"startdt","name":"\u5c55\u793a\u65e5\u671f","fieldstype":"date","ispx":"0","isalign":"0","islb":"0"},{"fields":"enddt","name":"\u5c55\u793a\u622a\u6b62","fieldstype":"date","ispx":"0","isalign":"0","islb":"0"},{"fields":"issms","name":"\u53d1\u624b\u673a\u77ed\u4fe1","fieldstype":"checkbox","ispx":"0","isalign":"0","islb":"0"},{"fields":"istop","name":"\u6392\u5e8f\u53f7","fieldstype":"checkbox","ispx":"1","isalign":"0","islb":"1"},{"fields":"status","name":"\u72b6\u6001","fieldstype":"checkbox","ispx":"1","isalign":"0","islb":"1"},{"fields":"appxs","name":"APP\u9996\u9875\u663e\u793a","fieldstype":"checkbox","ispx":"1","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

bootparams.celleditor = (admintype=='1' || pnum=='all');
bootparams.statuschange = true;
c.setcolumns('recename',{
	renderer:function(v){
		return '<div style="max-width:250px;text-overflow:ellipsis;overflow:hidden;white-space:nowrap" class="wrap">'+v+'</div>';
	}
});
c.setcolumns('fengmian',{
	renderer:function(v){
		if(!v)return '&nbsp;';
		return '<img src="'+v+'" height="60">';
	}
});
c.setcolumns('title',{
	renderer:function(v,d){
		var s=v;
		if(d.istop>0)s=''+s+' <a class="btn btn-success btn-xs">顶</a>';
		return s;
	}
});

c.setcolumns('istop',{
	type:'number',
	'editor':true
});
c.setcolumns('status',{
	type:'checkbox',
	'editor':true
});
c.setcolumns('appxs',{
	type:'checkbox',
	'editor':true
});

//[自定义区域end]

	js.initbtn(c);
	var a = $('#view'+modenum+'_{rand}').bootstable(bootparams);
	c.init();
	
});
</script>
<!--SCRIPTend-->
<!--HTMLstart-->
<div>
	<table width="100%">
	<tr>
		<td style="padding-right:10px;" id="tdleft_{rand}" nowrap><button id="addbtn_{rand}" class="btn btn-primary" click="clickwin,0" disabled type="button"><i class="icon-plus"></i> 新增</button></td>
		<td>
			<input class="form-control" style="width:160px" id="key_{rand}" placeholder="关键字">
		</td>
		<td style="padding-left:10px"><select class="form-control" style="width:120px" id="selstatus_{rand}"><option value="">-全部状态-</option><option style="color:blue" value="0">停用</option><option style="color:green" value="1">启用</option><option style="color:#888888" value="5">已作废</option></select></td>
		<td style="padding-left:10px">
			<div style="white-space:nowrap">
			<button style="border-right:0;border-top-right-radius:0;border-bottom-right-radius:0" class="btn btn-default" click="searchbtn" type="button">搜索</button><button class="btn btn-default" id="downbtn_{rand}" type="button" style="padding-left:8px;padding-right:8px;border-top-left-radius:0;border-bottom-left-radius:0"><i class="icon-angle-down"></i></button> 
			</div>
		</td>
		<td  width="90%" style="padding-left:10px"><div id="changatype{rand}" class="btn-group"></div></td>
	
		<td align="right" id="tdright_{rand}" nowrap>
			<button class="btn btn-default" style="display:none" id="daobtn_{rand}" disabled click="daochu" type="button">导出 <i class="icon-angle-down"></i></button> 
		</td>
	</tr>
	</table>
</div>
<div class="blank10"></div>
<div id="viewnews_{rand}"></div>
<!--HTMLend-->