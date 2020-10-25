<?php
/**
*	模块：hrsalarm.薪资模版
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.薪资模版]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'hrsalarm',modename='薪资模版',isflow=0,modeid='83',atype = params.atype,pnum=params.pnum,modenames='字段内容项目',listname='aHJzYWxhcm0:';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"title","name":"\u6a21\u7248\u540d\u79f0","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"atype","name":"\u6a21\u7248\u7c7b\u578b","fieldstype":"select","ispx":"0","isalign":"0","islb":"1"},{"fields":"recename","name":"\u9002\u7528\u5bf9\u8c61","fieldstype":"changedeptusercheck","ispx":"1","isalign":"0","islb":"1"},{"fields":"startdt","name":"\u5f00\u59cb\u6708\u4efd","fieldstype":"month","ispx":"0","isalign":"0","islb":"1"},{"fields":"enddt","name":"\u622a\u6b62\u6708\u4efd","fieldstype":"month","ispx":"0","isalign":"0","islb":"1"},{"fields":"explain","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"1"},{"fields":"sort","name":"\u6392\u5e8f\u53f7","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"},{"fields":"status","name":"\u72b6\u6001","fieldstype":"checkbox","ispx":"0","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

bootparams.celleditor = true;
c.setcolumns('status',{
	editor:true,
	type:'checkbox',
	editorafter:function(){
		a.reload();
	}
});
c.setcolumns('sort',{
	editor:true
});
c.setcolumns('title',{
	editor:true
});
c.setcolumns('explain',{
	editor:true
});
$('#tdright_{rand}').prepend(c.getbtnstr('导入模版','daoruxzmb')+'&nbsp;&nbsp;');
$('#tdright_{rand}').prepend(c.getbtnstr('复制','copyfuz')+'&nbsp;&nbsp;');
c.copyfuz=function(){
	var sid = a.changeid;
	if(!sid){js.msg('msg','没有选中行');return;}
	
	js.msg('wait','复制中...');
	js.ajax(publicmodeurl(modenum,'copyfuz'),{sid:sid}, function(d){
		js.msg('success', '复制成功');
		a.reload();
	},'get');
}
c.daoruxzmb=function(){
	js.msg('wait','导入中...');
	js.ajax(publicmodeurl(modenum,'daoruxzmb'),{}, function(ret){
		if(ret=='ok'){
			js.msg('success', '导入成功');
			a.reload();
		}else{
			js.msg('msg', ret);
		}
	},'get');
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
		<td style="padding-left:10px"><select class="form-control" style="width:120px" id="selstatus_{rand}"><option value="">-全部状态-</option><option style="color:blue" value="0">待处理</option><option style="color:green" value="1">已审核</option><option style="color:red" value="2">不同意</option><option style="color:#888888" value="5">已作废</option></select></td>
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
<div id="viewhrsalarm_{rand}"></div>
<!--HTMLend-->