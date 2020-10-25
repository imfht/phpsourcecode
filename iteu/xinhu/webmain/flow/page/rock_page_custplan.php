<?php
/**
*	模块：custplan.跟进计划
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.跟进计划]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'custplan',modename='跟进计划',isflow=0,modeid='98',atype = params.atype,pnum=params.pnum,modenames='',listname='Y3VzdHBsYW4:';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"custname","name":"\u8ddf\u8fdb\u5ba2\u6237","fieldstype":"selectdatafalse","ispx":"0","isalign":"0","islb":"1"},{"fields":"custid","name":"\u5ba2\u6237Id","fieldstype":"hidden","ispx":"0","isalign":"0","islb":"0"},{"fields":"gentype","name":"\u8ddf\u8fdb\u65b9\u5f0f","fieldstype":"rockcombo","ispx":"0","isalign":"0","islb":"1"},{"fields":"status","name":"\u72b6\u6001","fieldstype":"select","ispx":"0","isalign":"0","islb":"1"},{"fields":"plandt","name":"\u8ba1\u5212\u65f6\u95f4","fieldstype":"datetime","ispx":"0","isalign":"0","islb":"1"},{"fields":"findt","name":"\u5b8c\u6210\u65f6\u95f4","fieldstype":"datetime","ispx":"0","isalign":"0","islb":"1"},{"fields":"explain","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"1"},{"fields":"optname","name":"\u8ddf\u8fdb\u4eba","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= [];
	
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
		<td style="padding-left:10px"><select class="form-control" style="width:120px" id="selstatus_{rand}"><option value="">-全部状态-</option><option style="color:blue" value="0">计划</option><option style="color:green" value="1">已完成</option><option style="color:#888888" value="5">已作废</option></select></td>
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
<div id="viewcustplan_{rand}"></div>
<!--HTMLend-->