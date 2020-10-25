<?php
/**
*	模块：carmby.车辆保养
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.车辆保养]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'carmby',modename='车辆保养',isflow=1,modeid='60',atype = params.atype,pnum=params.pnum,modenames='',listname='Y2FybWFuZw::';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"carid","name":"\u4fdd\u517b\u8f66\u8f86","fieldstype":"select","ispx":"1","isalign":"0","islb":"1"},{"fields":"type","name":"\u5355\u636e\u7c7b\u578b","fieldstype":"fixed","ispx":"0","isalign":"0","islb":"0"},{"fields":"jianame","name":"\u9a7e\u9a76\u5458","fieldstype":"changeusercheck","ispx":"0","isalign":"0","islb":"1"},{"fields":"reason","name":"\u4fdd\u517b\u539f\u56e0","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"1"},{"fields":"address","name":"\u4fdd\u517b\u5730\u70b9","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"bujian","name":"\u4fdd\u517b\u5185\u5bb9","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"startdt","name":"\u4fdd\u517b\u65f6\u95f4","fieldstype":"datetime","ispx":"0","isalign":"0","islb":"1"},{"fields":"enddt","name":"\u53d6\u8f66\u65f6\u95f4","fieldstype":"datetime","ispx":"1","isalign":"0","islb":"1"},{"fields":"money","name":"\u4fdd\u517b\u91d1\u989d","fieldstype":"number","ispx":"0","isalign":"0","islb":"1"},{"fields":"kmshu","name":"\u5f53\u524d\u4fdd\u517b\u516c\u91cc\u6570","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"nextdt","name":"\u4e0b\u6b21\u4fdd\u517b\u65e5\u671f","fieldstype":"date","ispx":"0","isalign":"0","islb":"1"},{"fields":"kmnshu","name":"\u4e0b\u6b21\u4fdd\u517b\u516c\u91cc\u6570","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"explain","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"0"}],fieldsselarr= [],chufarr= [];
	
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
<div id="viewcarmby_{rand}"></div>
<!--HTMLend-->