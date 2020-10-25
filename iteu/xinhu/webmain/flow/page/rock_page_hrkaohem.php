<?php
/**
*	模块：hrkaohem.考核项目
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.考核项目]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'hrkaohem',modename='考核项目',isflow=0,modeid='81',atype = params.atype,pnum=params.pnum,modenames='考核项目内容,评分人',listname='aHJrYW9oZW0:';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"title","name":"\u8003\u6838\u540d\u79f0","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"startdt","name":"\u5f00\u59cb\u65e5\u671f","fieldstype":"date","ispx":"1","isalign":"0","islb":"1"},{"fields":"enddt","name":"\u622a\u6b62\u65e5\u671f","fieldstype":"date","ispx":"0","isalign":"0","islb":"1"},{"fields":"recename","name":"\u8003\u6838\u5bf9\u8c61","fieldstype":"changedeptusercheck","ispx":"0","isalign":"0","islb":"1"},{"fields":"pinlv","name":"\u8003\u6838\u9891\u7387","fieldstype":"select","ispx":"0","isalign":"0","islb":"1"},{"fields":"sctime","name":"\u751f\u6210\u65f6\u95f4","fieldstype":"date","ispx":"0","isalign":"0","islb":"1"},{"fields":"pfsj","name":"\u8bc4\u5206\u65f6\u95f4(\u5929)","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"maxfen","name":"\u6700\u9ad8\u5206\u6570","fieldstype":"number","ispx":"0","isalign":"0","islb":"1"},{"fields":"hegfen","name":"\u5408\u683c\u5206\u6570","fieldstype":"number","ispx":"0","isalign":"0","islb":"1"},{"fields":"temp_zbcont","name":"\u8003\u6838\u5185\u5bb9","fieldstype":"text","ispx":"0","isalign":"1","islb":"1"},{"fields":"temp_pfren","name":"\u8bc4\u5206\u4eba","fieldstype":"text","ispx":"0","isalign":"1","islb":"1"},{"fields":"status","name":"\u72b6\u6001","fieldstype":"select","ispx":"1","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

bootparams.celleditor=true;
c.setcolumns('status',{
	'type':'checkbox',
	'editor':true
});
$('#tdright_{rand}').prepend(c.getbtnstr('复制','copyfuz')+'&nbsp;&nbsp;');
$('#tdright_{rand}').prepend(c.getbtnstr('生成考核评分','shengchege')+'&nbsp;&nbsp;');
c.copyfuz=function(){
	var sid = a.changeid;
	if(!sid){js.msg('msg','没有选中行');return;}
	
	js.msg('wait','复制中...');
	js.ajax(publicmodeurl(modenum,'copyfuz'),{sid:sid}, function(d){
		js.msg('success', '复制成功');
		a.reload();
	},'get');
}
c.shengchege=function(){
	js.msg('wait','生成中...');
	js.ajax(publicmodeurl(modenum,'shengchege'),{}, function(str){
		js.msg('success', str);
	},'get');
}

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
		
		<td style="padding-left:10px">
			<div style="white-space:nowrap">
			<button style="border-right:0;border-top-right-radius:0;border-bottom-right-radius:0" class="btn btn-default" click="searchbtn" type="button">搜索</button><button class="btn btn-default" id="downbtn_{rand}" type="button" style="padding-left:8px;padding-right:8px;border-top-left-radius:0;border-bottom-left-radius:0"><i class="icon-angle-down"></i></button> 
			</div>
		</td>
		<td  width="90%" style="padding-left:10px"><div id="changatype{rand}" class="btn-group"></div></td>
	
		<td align="right" id="tdright_{rand}" nowrap>
			<span style="display:none" id="daoruspan_{rand}"><button class="btn btn-default" click="daoru,1" type="button">导入</button>&nbsp;&nbsp;&nbsp;</span><button class="btn btn-default" style="display:none" id="daobtn_{rand}" disabled click="daochu" type="button">导出 <i class="icon-angle-down"></i></button> 
		</td>
	</tr>
	</table>
</div>
<div class="blank10"></div>
<div id="viewhrkaohem_{rand}"></div>
<!--HTMLend-->