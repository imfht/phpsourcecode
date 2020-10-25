<?php
/**
*	模块：custxiao.销售
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.销售]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'custxiao',modename='销售',isflow=1,modeid='92',atype = params.atype,pnum=params.pnum,modenames='销售物品',listname='Z29vZG0:';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u9500\u552e\u4eba","fields":"base_name"},{"name":"\u9500\u552e\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u9500\u552e\u5355\u53f7","fields":"sericnum"},{"fields":"num","name":"\u9500\u552e\u5355\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"applydt","name":"\u9500\u552e\u65e5\u671f","fieldstype":"date","ispx":"1","isalign":"0","islb":"1"},{"fields":"custractid","name":"\u5173\u8054\u5408\u540c","fieldstype":"select","ispx":"0","isalign":"0","islb":"1"},{"fields":"custname","name":"\u5ba2\u6237\u540d\u79f0","fieldstype":"selectdatafalse","ispx":"0","isalign":"0","islb":"1"},{"fields":"custid","name":"\u5ba2\u6237id","fieldstype":"hidden","ispx":"0","isalign":"0","islb":"0"},{"fields":"discount","name":"\u4f18\u60e0\u4ef7\u683c","fieldstype":"number","ispx":"0","isalign":"0","islb":"1"},{"fields":"money","name":"\u9500\u552e\u91d1\u989d","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"},{"fields":"explain","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"1"},{"fields":"state","name":"\u51fa\u5e93\u72b6\u6001","fieldstype":"select","ispx":"1","isalign":"0","islb":"1"},{"fields":"shoukuzt","name":"\u6536\u6b3e\u72b6\u6001","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= {"base_name":"\u9500\u552e\u4eba","base_deptname":"\u9500\u552e\u4eba\u90e8\u95e8","base_sericnum":"\u9500\u552e\u5355\u53f7"};
	
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
			<input class="form-control" style="width:160px" id="key_{rand}" placeholder="关键字/申请人/单号">
		</td>
		<td style="padding-left:10px"><select class="form-control" style="width:120px" id="selstatus_{rand}"><option value="">-全部状态-</option><option style="color:blue" value="0">待处理</option><option style="color:green" value="1">已审核</option><option style="color:red" value="2">不同意</option><option style="color:#888888" value="5">已作废</option><option style="color:#17B2B7" value="23">退回</option></select></td>
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
<div id="viewcustxiao_{rand}"></div>
<!--HTMLend-->