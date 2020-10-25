<?php
/**
*	模块：demo.演示测试
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.演示测试]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'demo',modename='演示测试',isflow=4,modeid='72',atype = params.atype,pnum=params.pnum,modenames='多行子表1,多行子表2',listname='ZGVtbw::';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"applydt","name":"\u7533\u8bf7\u65e5\u671f","fieldstype":"date","ispx":"0","isalign":"0","islb":"1"},{"fields":"num","name":"\u7f16\u53f7","fieldstype":"num","ispx":"0","isalign":"0","islb":"1"},{"fields":"custname","name":"\u5ba2\u6237","fieldstype":"selectdatafalse","ispx":"0","isalign":"0","islb":"1"},{"fields":"custid","name":"\u5ba2\u6237id","fieldstype":"hidden","ispx":"0","isalign":"0","islb":"0"},{"fields":"fengmian","name":"\u5c01\u9762\u56fe\u7247","fieldstype":"uploadimg","ispx":"0","isalign":"0","islb":"0"},{"fields":"sheng","name":"\u7701","fieldstype":"select","ispx":"0","isalign":"0","islb":"1"},{"fields":"shi","name":"\u5e02","fieldstype":"select","ispx":"0","isalign":"0","islb":"1"},{"fields":"xian","name":"\u53bf(\u533a)","fieldstype":"select","ispx":"0","isalign":"0","islb":"1"},{"fields":"explain","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"1"},{"fields":"tanxuan","name":"\u5f39\u51fa\u4e0b \u62c9\u5355\u9009","fieldstype":"selectdatafalse","ispx":"0","isalign":"0","islb":"1"},{"fields":"tanxuanid","name":"\u5355\u9009\u5f39\u51fa\u9009\u62e9id","fieldstype":"hidden","ispx":"0","isalign":"0","islb":"0"},{"fields":"tanxuancheck","name":"\u5f39\u6846\u4e0b \u62c9\u591a\u9009","fieldstype":"selectdatatrue","ispx":"0","isalign":"0","islb":"1"},{"fields":"upfile1","name":"\u6587\u4ef6\u4e0a\u4f201","fieldstype":"uploadfile","ispx":"0","isalign":"0","islb":"0"},{"fields":"upfile2","name":"\u6587\u4ef6\u4e0a\u4f202","fieldstype":"uploadfile","ispx":"0","isalign":"0","islb":"0"},{"fields":"testfirs","name":"\u6d4b\u8bd5\u5b57\u6bb5","fieldstype":"checkboxall","ispx":"0","isalign":"0","islb":"0"},{"fields":"htmlcont","name":"html\u7f16\u8f91\u5668","fieldstype":"htmlediter","ispx":"0","isalign":"0","islb":"0"}],fieldsselarr= [],chufarr= [];
	
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
<div id="viewdemo_{rand}"></div>
<!--HTMLend-->