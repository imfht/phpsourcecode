<?php
/**
*	模块：meet.会议
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.会议]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'meet',modename='会议',isflow=0,modeid='2',atype = params.atype,pnum=params.pnum,modenames='',listname='bWVldA::';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"hyname","name":"\u4f1a\u8bae\u5ba4","fieldstype":"rockcombo","ispx":"1","isalign":"0","islb":"1"},{"fields":"title","name":"\u4e3b\u9898","fieldstype":"text","ispx":"0","isalign":"1","islb":"1"},{"fields":"zcren","name":"\u4e3b\u6301\u4eba","fieldstype":"changeusercheck","ispx":"0","isalign":"0","islb":"1"},{"fields":"startdt","name":"\u5f00\u59cb\u65f6\u95f4","fieldstype":"datetime","ispx":"0","isalign":"0","islb":"1"},{"fields":"enddt","name":"\u7ed3\u675f\u65f6\u95f4","fieldstype":"datetime","ispx":"0","isalign":"0","islb":"1"},{"fields":"joinname","name":"\u53c2\u4f1a\u4eba","fieldstype":"changedeptusercheck","ispx":"0","isalign":"0","islb":"1"},{"fields":"type","name":"\u7c7b\u578b","fieldstype":"select","ispx":"0","isalign":"0","islb":"0"},{"fields":"rate","name":"\u4f1a\u8bae\u9891\u7387","fieldstype":"select","ispx":"0","isalign":"0","islb":"0"},{"fields":"explain","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"1"},{"fields":"optname","name":"\u53d1\u8d77\u4eba","fieldstype":"text","ispx":"1","isalign":"0","islb":"1"},{"fields":"state","name":"\u72b6\u6001","fieldstype":"text","ispx":"1","isalign":"0","islb":"1"},{"fields":"jyname","name":"\u4f1a\u8bae\u7eaa\u8981\u4eba","fieldstype":"changeusercheck","ispx":"0","isalign":"0","islb":"1"},{"fields":"content","name":"\u4f1a\u8bae\u7eaa\u8981\u5185\u5bb9","fieldstype":"htmlediter","ispx":"0","isalign":"0","islb":"0"},{"fields":"issms","name":"\u77ed\u4fe1\u63d0\u9192","fieldstype":"checkbox","ispx":"0","isalign":"0","islb":"0"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

c.initpage=function(){
	$('#key_{rand}').parent().before('<td style="padding-right:10px;"><input onclick="js.datechange(this,\'date\')" style="width:110px" placeholder="日期" readonly class="form-control datesss" id="dt_{rand}" ></td>');
}
c.searchbtn=function(){
	var dt = get('dt_{rand}').value;
	this.search({dt:dt});
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
			<button class="btn btn-default" style="display:none" id="daobtn_{rand}" disabled click="daochu" type="button">导出 <i class="icon-angle-down"></i></button> 
		</td>
	</tr>
	</table>
</div>
<div class="blank10"></div>
<div id="viewmeet_{rand}"></div>
<!--HTMLend-->