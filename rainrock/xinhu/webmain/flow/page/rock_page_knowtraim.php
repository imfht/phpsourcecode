<?php
/**
*	模块：knowtraim.考试培训
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.考试培训]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'knowtraim',modename='考试培训',isflow=0,modeid='56',atype = params.atype,pnum=params.pnum,modenames='',listname='a25vd3RyYWlt';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"title","name":"\u6807\u9898","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"tikuid","name":"\u9898\u5e93id","fieldstype":"hidden","ispx":"0","isalign":"0","islb":"0"},{"fields":"kstime","name":"\u8003\u8bd5\u65f6\u95f4(\u5206\u949f)","fieldstype":"select","ispx":"0","isalign":"0","islb":"0"},{"fields":"startdt","name":"\u5f00\u59cb\u65f6\u95f4","fieldstype":"datetime","ispx":"1","isalign":"0","islb":"1"},{"fields":"enddt","name":"\u622a\u6b62\u65f6\u95f4","fieldstype":"datetime","ispx":"0","isalign":"0","islb":"1"},{"fields":"dsshu","name":"\u5355\u9009\u9898\u6570","fieldstype":"number","ispx":"0","isalign":"0","islb":"1"},{"fields":"dxshu","name":"\u591a\u9009\u9898\u6570","fieldstype":"number","ispx":"0","isalign":"0","islb":"1"},{"fields":"pdshu","name":"\u5224\u65ad\u9898\u6570","fieldstype":"number","ispx":"0","isalign":"0","islb":"1"},{"fields":"recename","name":"\u8003\u8bd5\u5bf9\u8c61","fieldstype":"changedeptusercheck","ispx":"0","isalign":"0","islb":"1"},{"fields":"tikuname","name":"\u57f9\u8bad\u9898\u5e93","fieldstype":"selectdatatrue","ispx":"0","isalign":"0","islb":"1"},{"fields":"reshu","name":"\u57f9\u8bad\u4eba\u6570","fieldstype":"number","ispx":"0","isalign":"0","islb":"1"},{"fields":"ydshu","name":"\u5df2\u7b54\u6570","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"},{"fields":"zfenshu","name":"\u603b\u5206","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"},{"fields":"hgfen","name":"\u5408\u683c\u5206\u6570","fieldstype":"number","ispx":"0","isalign":"0","islb":"1"},{"fields":"explain","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"1"},{"fields":"optname","name":"\u64cd\u4f5c\u4eba","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"state","name":"\u72b6\u6001","fieldstype":"select","ispx":"1","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

c.setcolumns('title',{
	renderer:function(v,d,oi){
		return ''+v+' <a onclick="tongji{rand}('+oi+')" href="javascript:;">[统计]</a>';
	}
});

tongji{rand}=function(oi){
	var d = a.getData(oi);
	addtabs({num:'kuntotog'+d.id+'',name:'统计['+d.title+']',url:'flow,page,kuntraimtotal,id='+d.id+''});
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
<div id="viewknowtraim_{rand}"></div>
<!--HTMLend-->