<?php
/**
*	模块：daily.工作日报
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.工作日报]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'daily',modename='工作日报',isflow=0,modeid='3',atype = params.atype,pnum=params.pnum,modenames='',listname='ZGFpbHk:';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"deptname","name":"\u90e8\u95e8","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"optname","name":"\u4eba\u5458","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"type","name":"\u65e5\u62a5\u7c7b\u578b","fieldstype":"select","ispx":"1","isalign":"0","islb":"1"},{"fields":"dt","name":"\u65e5\u671f","fieldstype":"date","ispx":"1","isalign":"0","islb":"1"},{"fields":"enddt","name":"\u622a\u6b62\u65e5\u671f","fieldstype":"date","ispx":"0","isalign":"0","islb":"0"},{"fields":"content","name":"\u5185\u5bb9","fieldstype":"textarea","ispx":"0","isalign":"1","islb":"1"},{"fields":"plan","name":"\u660e\u65e5\u8ba1\u5212","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"0"},{"fields":"adddt","name":"\u65b0\u589e\u65f6\u95f4","fieldstype":"hidden","ispx":"0","isalign":"0","islb":"1"},{"fields":"mark","name":"\u8bc4\u5206\u5206\u6570","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"optdt","name":"\u64cd\u4f5c\u65f6\u95f4","fieldstype":"datetime","ispx":"1","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= [];
	
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
<div id="viewdaily_{rand}"></div>
<!--HTMLend-->