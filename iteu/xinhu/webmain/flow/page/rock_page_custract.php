<?php
/**
*	模块：custract.客户合同
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.客户合同]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'custract',modename='客户合同',isflow=0,modeid='35',atype = params.atype,pnum=params.pnum,modenames='',listname='Y3VzdHJhY3Q:';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"num","name":"\u5408\u540c\u7f16\u53f7","fieldstype":"num","ispx":"0","isalign":"0","islb":"1"},{"fields":"fenlei","name":"\u5408\u540c\u5206\u7c7b","fieldstype":"rockcombo","ispx":"0","isalign":"0","islb":"1"},{"fields":"custid","name":"\u5ba2\u6237\u540d\u79f0","fieldstype":"hidden","ispx":"0","isalign":"0","islb":"0"},{"fields":"custname","name":"\u5ba2\u6237\u540d\u79f0","fieldstype":"selectdatafalse","ispx":"0","isalign":"0","islb":"1"},{"fields":"optname","name":"\u62e5\u6709\u8005","fieldstype":"text","ispx":"1","isalign":"0","islb":"1"},{"fields":"saleid","name":"\u9500\u552e\u673a\u4f1a","fieldstype":"select","ispx":"0","isalign":"0","islb":"0"},{"fields":"signdt","name":"\u7b7e\u7ea6\u65e5\u671f","fieldstype":"date","ispx":"1","isalign":"0","islb":"1"},{"fields":"money","name":"\u5408\u540c\u91d1\u989d","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"},{"fields":"startdt","name":"\u751f\u6548\u65e5\u671f","fieldstype":"date","ispx":"1","isalign":"0","islb":"1"},{"fields":"enddt","name":"\u622a\u6b62\u65e5\u671f","fieldstype":"date","ispx":"1","isalign":"0","islb":"1"},{"fields":"content","name":"\u5408\u540c\u5185\u5bb9","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"0"},{"fields":"type","name":"\u5408\u540c\u7c7b\u578b","fieldstype":"select","ispx":"0","isalign":"0","islb":"1"},{"fields":"moneys","name":"\u5f85\u6536\/\u4ed8\u91d1\u989d","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"explain","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"1"},{"fields":"statetext","name":"\u72b6\u6001","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"createname","name":"\u521b\u5efa\u4eba","fieldstype":"text","ispx":"1","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

c.initpage=function(){
	$('#key_{rand}').parent().before('<td style="padding-right:10px;"><input onclick="js.datechange(this,\'month\')" style="width:110px" placeholder="签约月份" readonly class="form-control datesss" id="dt_{rand}" ></td>');
}
c.searchbtn=function(){
	var dt = get('dt_{rand}').value;
	this.search({dt:dt});
}
$('#tdright_{rand}').prepend(c.getbtnstr('待收/付金额更新','retotal')+'&nbsp;');

c.retotal=function(){
	js.ajax(publicmodeurl(modenum,'remoney'),{},function(s){
		a.reload();
	},'get',false,'更新中...,更新完成')
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
<div id="viewcustract_{rand}"></div>
<!--HTMLend-->