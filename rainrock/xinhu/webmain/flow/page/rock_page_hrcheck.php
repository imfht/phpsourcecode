<?php
/**
*	模块：hrcheck.考核评分
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.考核评分]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'hrcheck',modename='考核评分',isflow=0,modeid='71',atype = params.atype,pnum=params.pnum,modenames='考核内容,评分记录',listname='aHJjaGVjaw::';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"deptname","name":"\u90e8\u95e8","fieldstype":"text","ispx":"1","isalign":"0","islb":"1"},{"fields":"applyname","name":"\u59d3\u540d","fieldstype":"changeuser","ispx":"1","isalign":"0","islb":"1"},{"fields":"title","name":"\u8003\u6838\u9879\u76ee","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"startdt","name":"\u65e5\u671f","fieldstype":"date","ispx":"1","isalign":"0","islb":"1"},{"fields":"enddt","name":"\u8bc4\u5206\u622a\u6b62\u65e5\u671f","fieldstype":"date","ispx":"1","isalign":"0","islb":"1"},{"fields":"pfrenids","name":"\u72b6\u6001","fieldstype":"text","ispx":"1","isalign":"0","islb":"1"},{"fields":"optname","name":"\u64cd\u4f5c\u4eba","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"fen","name":"\u6700\u540e\u5f97\u5206","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"},{"fields":"createdt","name":"\u521b\u5efa\u65f6\u95f4","fieldstype":"datetime","ispx":"1","isalign":"0","islb":"1"}],fieldsselarr= {"columns_hrcheck_tjall":"deptname,applyname,title,startdt,pfrenids,fen"},chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

$('#tdleft_{rand}').hide();
if(pnum=='tjall'){
	bootparams.loadbefore=function(da){
		var das = [];
		for(var i in bootparams.columns)das.push(bootparams.columns[i]);
		das.push({
			'text' :'',
			'dataIndex' :'itemname',
		});
		das.push({
			'text' :'',
			'dataIndex' :'fenshu',
		});
		for(var i=0;i<da.mlen;i++){
			das.push({
				'text' :'',
				'dataIndex' :'pfval'+i+'',
			});
		}
		a.setColumns(das);
	}
	bootparams.itemdblclick=function(){};
}

c.initpage=function(){
	$('#key_{rand}').parent().before('<td style="padding-right:10px;"><input onclick="js.datechange(this,\'month\')" style="width:110px" placeholder="月份" readonly class="form-control datesss" id="dt_{rand}" ></td>');
}
c.searchbtn=function(){
	var dt = get('dt_{rand}').value;
	this.search({month:dt});
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
<div id="viewhrcheck_{rand}"></div>
<!--HTMLend-->