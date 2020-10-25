<?php
/**
*	模块：work.任务
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.任务]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'work',modename='任务',isflow=1,modeid='4',atype = params.atype,pnum=params.pnum,modenames='',listname='d29yaw::';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"title","name":"\u6807\u9898","fieldstype":"text","ispx":"0","isalign":"1","islb":"1"},{"fields":"type","name":"\u7c7b\u578b","fieldstype":"rockcombo","ispx":"0","isalign":"0","islb":"1"},{"fields":"grade","name":"\u7b49\u7ea7","fieldstype":"rockcombo","ispx":"1","isalign":"0","islb":"1"},{"fields":"dist","name":"\u5206\u914d\u7ed9","fieldstype":"changeuser","ispx":"0","isalign":"0","islb":"1"},{"fields":"projectid","name":"\u6240\u5c5e\u9879\u76ee","fieldstype":"select","ispx":"0","isalign":"0","islb":"0"},{"fields":"explain","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"0"},{"fields":"startdt","name":"\u5f00\u59cb\u65f6\u95f4","fieldstype":"datetime","ispx":"0","isalign":"0","islb":"1"},{"fields":"enddt","name":"\u622a\u6b62\u65f6\u95f4","fieldstype":"datetime","ispx":"0","isalign":"0","islb":"1"},{"fields":"ddname","name":"\u7763\u5bfc\u4eba","fieldstype":"changeusercheck","ispx":"0","isalign":"0","islb":"1"},{"fields":"score","name":"\u4efb\u52a1\u5206\u503c","fieldstype":"number","ispx":"0","isalign":"0","islb":"1"},{"fields":"mark","name":"\u5f97\u5206","fieldstype":"text","ispx":"1","isalign":"0","islb":"1"},{"fields":"optname","name":"\u521b\u5efa\u4eba","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"optdt","name":"\u521b\u5efa\u65f6\u95f4","fieldstype":"datetime","ispx":"0","isalign":"0","islb":"1"}],fieldsselarr= {"columns_work_":"title,type,grade,dist,startdt,enddt,ddname,score,optname,optdt,caozuo","columns_work_all":"title,type,grade,dist,startdt,enddt,ddname,score,mark,optname,optdt,caozuo"},chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

var plid = params.projcetid;
if(plid)bootparams.url+='&projcetid='+plid+'';
c.setcolumns('title',{
	renderer:function(v,d){
		var s = v;
		if(d.projectid!='')s+='<br><span style="color:#888888;font-size:12px">'+d.projectid+'</span>';
		return s;
	}
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
			<input class="form-control" style="width:160px" id="key_{rand}" placeholder="关键字/申请人/单号">
		</td>
		<td style="padding-left:10px"><select class="form-control" style="width:120px" id="selstatus_{rand}"><option value="">-全部状态-</option><option style="color:blue" value="0">待分配</option><option style="color:green" value="1">已完成</option><option style="color:red" value="2">无法完成</option><option style="color:#ff6600" value="3">待执行</option><option style="color:#526D08" value="4">执行中</option><option style="color:#888888" value="5">已作废</option><option style="color:" value="6">待验证</option><option style="color:#17B2B7" value="23">退回</option></select></td>
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
<div id="viewwork_{rand}"></div>
<!--HTMLend-->