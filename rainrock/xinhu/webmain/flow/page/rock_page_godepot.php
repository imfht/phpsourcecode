<?php
/**
*	模块：godepot.仓库管理
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.仓库管理]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'godepot',modename='仓库管理',isflow=0,modeid='73',atype = params.atype,pnum=params.pnum,modenames='',listname='Z29kZXBvdA::';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"depotname","name":"\u4ed3\u5e93\u540d\u79f0","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"cgname","name":"\u4ed3\u5e93\u7ba1\u7406\u5458","fieldstype":"changeusercheck","ispx":"0","isalign":"0","islb":"1"},{"fields":"depotnum","name":"\u4ed3\u5e93\u7f16\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"depotaddress","name":"\u4ed3\u5e93\u5730\u5740","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"sort","name":"\u6392\u5e8f\u53f7","fieldstype":"number","ispx":"0","isalign":"0","islb":"1"},{"fields":"depotexplain","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"1"},{"fields":"wpshu","name":"\u7269\u54c1\u6570","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

$('#tdright_{rand}').prepend(c.getbtnstr('重新统计物品数','retotal')+'&nbsp;');
c.retotal=function(){
	js.ajax(this.getacturl('retotal'),{},function(s){
		a.reload();
	},'get',false,'统计中...,统计完成')
}
c.setcolumns('wpshu', {
	renderer:function(v,d){
		return ''+v+'<a href="javascript:;" onclick="chakanpand{rand}(\''+d.depotname+'\','+d.id+')">查看</a>';
	}
});
chakanpand{rand}=function(na,id){
	addtabs({name:'仓库['+na+']下物品',url:'main,goods,pdck,depotid='+id+'','num':'pdck'+id+''});
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
<div id="viewgodepot_{rand}"></div>
<!--HTMLend-->