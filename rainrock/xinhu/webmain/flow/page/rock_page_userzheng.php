<?php
/**
*	模块：userzheng.人员证件
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.人员证件]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'userzheng',modename='人员证件',isflow=0,modeid='108',atype = params.atype,pnum=params.pnum,modenames='',listname='dXNlcnpoZW5n';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"uname","name":"\u6240\u5c5e\u4eba","fieldstype":"changeuser","ispx":"0","isalign":"0","islb":"1"},{"fields":"mingc","name":"\u8bc1\u4ef6\u540d\u79f0","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"numc","name":"\u8bc1\u4e66\u7f16\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"fengmian","name":"\u76f8\u5173\u56fe\u7247","fieldstype":"uploadimg","ispx":"0","isalign":"0","islb":"1"},{"fields":"sdt","name":"\u53d6\u5f97\u65f6\u95f4","fieldstype":"date","ispx":"1","isalign":"0","islb":"1"},{"fields":"edt","name":"\u5230\u671f\u65f6\u95f4","fieldstype":"date","ispx":"0","isalign":"0","islb":"1"},{"fields":"explain","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

c.setcolumns('fengmian',{
	renderer:function(v){
		if(!v)return '&nbsp;';
		return '<img src="'+v+'" height="60">';
	}
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
<div id="viewuserzheng_{rand}"></div>
<!--HTMLend-->