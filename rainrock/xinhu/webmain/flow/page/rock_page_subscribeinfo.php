<?php
/**
*	模块：subscribeinfo.订阅报表
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.订阅报表]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'subscribeinfo',modename='订阅报表',isflow=0,modeid='68',atype = params.atype,pnum=params.pnum,modenames='',listname='c3Vic2NyaWJlaW5mbw::';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"title","name":"\u8ba2\u9605\u6807\u9898","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"cont","name":"\u8ba2\u9605\u5185\u5bb9","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"optdt","name":"\u6dfb\u52a0\u65f6\u5019","fieldstype":"datetime","ispx":"1","isalign":"0","islb":"1"},{"fields":"recename","name":"\u8ba2\u9605\u63d0\u9192\u5bf9\u8c61","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"filepath","name":"\u6587\u4ef6","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

$('#addbtn_{rand}').html('<i class="icon-cog"></i> 订阅管理');
	c.clickwin=function(){
		addtabs({url:'flow,page,subscribe,atype=my',name:'我订阅管理',num:'rssglmy','icons':'cog'});
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
<div id="viewsubscribeinfo_{rand}"></div>
<!--HTMLend-->