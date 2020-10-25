<?php
/**
*	模块：hrshebao.社保公积金
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.社保公积金]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'hrshebao',modename='社保公积金',isflow=0,modeid='84',atype = params.atype,pnum=params.pnum,modenames='',listname='aHJzaGViYW8:';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"title","name":"\u540d\u79f0","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"recename","name":"\u9002\u7528\u5bf9\u8c61","fieldstype":"changedeptusercheck","ispx":"0","isalign":"0","islb":"1"},{"fields":"status","name":"\u72b6\u6001","fieldstype":"checkbox","ispx":"0","isalign":"0","islb":"1"},{"fields":"yljishu","name":"\u517b\u8001\u4fdd\u9669\u57fa\u6570","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"ylgeren","name":"\u517b\u8001\u4e2a\u4eba\u6bd4\u4f8b(%)","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"ylunit","name":"\u517b\u8001\u5355\u4f4d\u6bd4\u4f8b(%)","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"syjishu","name":"\u5931\u4e1a\u4fdd\u9669\u57fa\u6570","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"sygeren","name":"\u5931\u4e1a\u4e2a\u4eba\u6bd4\u4f8b(%)","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"syunit","name":"\u5931\u4e1a\u5355\u4f4d\u6bd4\u4f8b(%)","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"gsjishu","name":"\u5de5\u4f24\u4fdd\u9669\u57fa\u6570","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"gsgeren","name":"\u5de5\u4f24\u4e2a\u4eba\u6bd4\u4f8b(%)","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"gsunit","name":"\u5de5\u4f24\u5355\u4f4d\u6bd4\u4f8b(%)","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"syujishu","name":"\u751f\u80b2\u4fdd\u9669\u57fa\u6570","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"syugeren","name":"\u751f\u80b2\u4e2a\u4eba\u6bd4\u4f8b(%)","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"syuunit","name":"\u751f\u80b2\u5355\u4f4d\u6bd4\u4f8b(%)","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"yijishu","name":"\u533b\u7597\u4fdd\u9669\u57fa\u6570","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"yigeren","name":"\u533b\u7597\u4e2a\u4eba\u6bd4\u4f8b(%)","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"yiunit","name":"\u533b\u7597\u5355\u4f4d\u6bd4\u4f8b(%)","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"dbgeren","name":"\u5927\u75c5\u4e2a\u4eba(\u5143)","fieldstype":"number","ispx":"0","isalign":"0","islb":"1"},{"fields":"shebaogeren","name":"\u4e2a\u4eba\u793e\u4fdd\u7f34\u8d39(\u5143)","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"shebaounit","name":"\u5355\u4f4d\u793e\u4fdd\u7f34\u8d39(\u5143)","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"gongjishu","name":"\u516c\u79ef\u91d1\u57fa\u6570","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"gjjgeren","name":"\u516c\u79ef\u91d1\u4e2a\u4eba\u6bd4\u4f8b(%)","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"gjjunit","name":"\u516c\u79ef\u91d1\u5355\u4f4d\u6bd4\u4f8b(%)","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"gonggeren","name":"\u516c\u79ef\u91d1\u4e2a\u4eba(\u5143)","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"gongunit","name":"\u516c\u79ef\u91d1\u5355\u4f4d(\u5143)","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"startdt","name":"\u5f00\u59cb\u6708\u4efd","fieldstype":"month","ispx":"0","isalign":"0","islb":"1"},{"fields":"enddt","name":"\u622a\u6b62\u6708\u4efd","fieldstype":"month","ispx":"0","isalign":"0","islb":"1"},{"fields":"sctime","name":"\u6bcf\u6708\u751f\u6210\u65f6\u95f4","fieldstype":"date","ispx":"0","isalign":"0","islb":"0"},{"fields":"explian","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"0"}],fieldsselarr= [],chufarr= [];
	
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
c.setcolumns('title',{
	editor:true
});
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
<div id="viewhrshebao_{rand}"></div>
<!--HTMLend-->