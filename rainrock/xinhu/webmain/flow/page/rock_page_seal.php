<?php
/**
*	模块：seal.印章证照
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.印章证照]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'seal',modename='印章证照',isflow=0,modeid='48',atype = params.atype,pnum=params.pnum,modenames='',listname='c2VhbA::';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"name","name":"\u540d\u79f0","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"type","name":"\u7c7b\u578b","fieldstype":"rockcombo","ispx":"1","isalign":"0","islb":"1"},{"fields":"bgname","name":"\u4fdd\u7ba1\u4eba","fieldstype":"changeusercheck","ispx":"0","isalign":"0","islb":"1"},{"fields":"sealimg","name":"\u76f8\u5173\u56fe\u7247","fieldstype":"uploadimg","ispx":"0","isalign":"0","islb":"1"},{"fields":"explain","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"1"},{"fields":"startdt","name":"\u7b7e\u53d1\u65e5\u671f","fieldstype":"date","ispx":"1","isalign":"0","islb":"1"},{"fields":"enddt","name":"\u622a\u6b62\u65e5\u671f","fieldstype":"date","ispx":"0","isalign":"0","islb":"1"},{"fields":"sort","name":"\u6392\u5e8f\u53f7","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

bootparams.celleditor=true;
bootparams.sort='sort';
bootparams.dir='asc';
c.setcolumns('sealimg', {
	renderer:function(v){
		var s='&nbsp;';
		if(!isempt(v))s='<img src="'+v+'" onclick="$.imgview({url:this.src})" width="80">';
		return s;
	}
});

c.setcolumns('sort',{
	editor:true
});

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
<div id="viewseal_{rand}"></div>
<!--HTMLend-->