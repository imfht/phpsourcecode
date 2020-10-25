<?php
/**
*	模块：userract.员工合同
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.员工合同]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'userract',modename='员工合同',isflow=0,modeid='31',atype = params.atype,pnum=params.pnum,modenames='',listname='dXNlcnJhY3Q:';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"uname","name":"\u7b7e\u7f72\u4eba","fieldstype":"changeuser","ispx":"0","isalign":"0","islb":"1"},{"fields":"deptname","name":"\u90e8\u95e8","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"name","name":"\u5408\u540c\u540d\u79f0","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"company","name":"\u7b7e\u7f72\u5355\u4f4d","fieldstype":"hidden","ispx":"0","isalign":"0","islb":"1"},{"fields":"companyid","name":"\u7b7e\u7f72\u5355\u4f4d","fieldstype":"select","ispx":"0","isalign":"0","islb":"0"},{"fields":"httype","name":"\u5408\u540c\u7c7b\u578b","fieldstype":"rockcombo","ispx":"0","isalign":"0","islb":"1"},{"fields":"startdt","name":"\u5f00\u59cb\u65e5\u671f","fieldstype":"date","ispx":"0","isalign":"0","islb":"1"},{"fields":"enddt","name":"\u622a\u6b62\u65e5\u671f","fieldstype":"date","ispx":"0","isalign":"0","islb":"1"},{"fields":"state","name":"\u72b6\u6001","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"tqenddt","name":"\u63d0\u524d\u7ec8\u6b62\u65e5\u671f","fieldstype":"date","ispx":"0","isalign":"0","islb":"1"},{"fields":"explain","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]



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
<div id="viewuserract_{rand}"></div>
<!--HTMLend-->