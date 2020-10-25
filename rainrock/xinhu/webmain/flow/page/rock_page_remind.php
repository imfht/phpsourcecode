<?php
/**
*	模块：remind.单据提醒设置
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.单据提醒设置]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'remind',modename='单据提醒设置',isflow=0,modeid='61',atype = params.atype,pnum=params.pnum,modenames='',listname='Zmxvd19yZW1pbmQ:';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"modenum","name":"\u6a21\u5757\u7f16\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"mid","name":"\u4e3bId","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"explain","name":"\u63d0\u9192\u5185\u5bb9","fieldstype":"textarea","ispx":"0","isalign":"1","islb":"1"},{"fields":"startdt","name":"\u5f00\u59cb\u65f6\u95f4","fieldstype":"datetime","ispx":"1","isalign":"0","islb":"1"},{"fields":"enddt","name":"\u622a\u6b62\u65f6\u95f4","fieldstype":"datetime","ispx":"0","isalign":"0","islb":"1"},{"fields":"rate","name":"\u91cd\u590d","fieldstype":"hidden","ispx":"0","isalign":"0","islb":"0"},{"fields":"recename","name":"\u63d0\u9192\u7ed9","fieldstype":"changedeptusercheck","ispx":"0","isalign":"0","islb":"1"},{"fields":"rateval","name":"\u91cd\u590d\u503c","fieldstype":"hidden","ispx":"0","isalign":"0","islb":"0"},{"fields":"ratecont","name":"\u63d0\u9192\u9891\u7387","fieldstype":"text","ispx":"0","isalign":"1","islb":"1"},{"fields":"temp_rateval","name":"\u9891\u7387\u8bbe\u7f6e","fieldstype":"auto","ispx":"0","isalign":"0","islb":"0"},{"fields":"status","name":"\u72b6\u6001","fieldstype":"select","ispx":"1","isalign":"0","islb":"1"},{"fields":"optdt","name":"\u64cd\u4f5c\u65f6\u95f4","fieldstype":"datetime","ispx":"1","isalign":"0","islb":"1"},{"fields":"optname","name":"\u64cd\u4f5c\u4eba","fieldstype":"text","ispx":"1","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

//让状态列可以编辑
bootparams.celleditor = true;
bootparams.statuschange = true;
c.setcolumns('status',{
	editor:true,
	type:'checkbox',
	editorafter:function(){
		c.reload();
	}
});
c.setcolumns('modenum',{
	text:'模块名称',
	dataIndex:'modename'
});
c.clickwin=function(){
	js.msg('msg','不能从这里新增提醒');
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
		<td style="padding-left:10px"><select class="form-control" style="width:120px" id="selstatus_{rand}"><option value="">-全部状态-</option><option style="color:green" value="1">启用</option><option style="color:#888888" value="0">停用</option></select></td>
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
<div id="viewremind_{rand}"></div>
<!--HTMLend-->