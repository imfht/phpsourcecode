<?php
/**
*	模块：finpiao.发票管理
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.发票管理]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'finpiao',modename='发票管理',isflow=0,modeid='85',atype = params.atype,pnum=params.pnum,modenames='',listname='ZmlucGlhbw::';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"type","name":"\u7c7b\u578b","fieldstype":"select","ispx":"0","isalign":"0","islb":"0"},{"fields":"ptype","name":"\u53d1\u7968\u7c7b\u578b","fieldstype":"rockcombo","ispx":"1","isalign":"0","islb":"1"},{"fields":"opendt","name":"\u5f00\u7968\u65e5\u671f","fieldstype":"date","ispx":"1","isalign":"0","islb":"1"},{"fields":"custname","name":"\u9500\u552e\u65b9\u540d\u79f0","fieldstype":"selectdatafalse","ispx":"0","isalign":"0","islb":"1"},{"fields":"custid","name":"\u9500\u552e\u65b9\u540d\u79f0ID","fieldstype":"hidden","ispx":"0","isalign":"0","islb":"0"},{"fields":"kainame","name":"\u5f00\u7968\u4eba","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"maicustname","name":"\u8d2d\u4e70\u65b9\u540d\u79f0","fieldstype":"selectdatafalse","ispx":"0","isalign":"0","islb":"1"},{"fields":"money","name":"\u91d1\u989d","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"},{"fields":"maicustid","name":"\u8d2d\u4e70\u65b9Id","fieldstype":"hidden","ispx":"0","isalign":"0","islb":"0"},{"fields":"daima","name":"\u53d1\u7968\u4ee3\u7801","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"haoma","name":"\u53d1\u7968\u53f7\u7801","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"explain","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

c.initpage=function(){
	$('#key_{rand}').parent().before('<td style="padding-right:10px;"><input onclick="js.datechange(this,\'month\')" style="width:110px" placeholder="月份" readonly class="form-control datesss" id="dt_{rand}" ></td>');
}
c.searchbtn=function(){
	var dt = get('dt_{rand}').value;
	this.search({month:dt});
}

bootparams.celleditor = true;//开启可编辑
c.setcolumns('daima',{editor:true});//对应字段设置可编辑
c.setcolumns('haoma',{editor:true});

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
<div id="viewfinpiao_{rand}"></div>
<!--HTMLend-->