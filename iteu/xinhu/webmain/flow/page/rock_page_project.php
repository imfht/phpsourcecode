<?php
/**
*	模块：project.项目
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.项目]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'project',modename='项目',isflow=0,modeid='22',atype = params.atype,pnum=params.pnum,modenames='',listname='cHJvamVjdA::';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"title","name":"\u540d\u79f0","fieldstype":"text","ispx":"0","isalign":"1","islb":"1"},{"fields":"num","name":"\u7f16\u53f7","fieldstype":"text","ispx":"1","isalign":"0","islb":"1"},{"fields":"type","name":"\u9879\u76ee\u7c7b\u578b","fieldstype":"rockcombo","ispx":"1","isalign":"0","islb":"1"},{"fields":"startdt","name":"\u5f00\u59cb\u65f6\u95f4","fieldstype":"datetime","ispx":"1","isalign":"0","islb":"1"},{"fields":"enddt","name":"\u9884\u8ba1\u7ed3\u675f\u65f6\u95f4","fieldstype":"datetime","ispx":"0","isalign":"0","islb":"1"},{"fields":"fuze","name":"\u8d1f\u8d23\u4eba","fieldstype":"changeuser","ispx":"1","isalign":"0","islb":"1"},{"fields":"runuser","name":"\u6267\u884c\u4eba","fieldstype":"changedeptusercheck","ispx":"0","isalign":"0","islb":"1"},{"fields":"progress","name":"\u8fdb\u5ea6(%)","fieldstype":"select","ispx":"0","isalign":"0","islb":"1"},{"fields":"content","name":"\u5185\u5bb9","fieldstype":"htmlediter","ispx":"0","isalign":"0","islb":"0"},{"fields":"workshu","name":"\u4efb\u52a1\u6570","fieldstype":"number","ispx":"0","isalign":"0","islb":"1"},{"fields":"status","name":"\u72b6\u6001","fieldstype":"select","ispx":"1","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

c.setcolumns('progress',{
	renderer:function(v){
		return '<div class="progress" style="margin:0;width:120px;"><div class="progress-bar progress-bar-success" style="width:'+v+'%;color:#000000;">'+v+'%</div></div>';
	},
	text:'进度'
});
c.setcolumns('workshu',{
	renderer:function(v,d,i){
		return ''+v+'&nbsp;<a href="javascript:;" onclick="viespere{rand}('+i+')">查看</a>';
	}
});
c.setcolumns('status',{
	renderer:function(v,d,i){
		return d.statusstr;
	}
});
viespere{rand}=function(id){
	var d 	= a.getData(id);
	var bo 	= addtabs({name:'项目['+d.title+']的任务',url:'flow,page,work,pnum=allall,atype=all,projcetid='+d.id+'',num:'projcetidwork'+d.id+''});
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
		<td style="padding-left:10px"><select class="form-control" style="width:120px" id="selstatus_{rand}"><option value="">-全部状态-</option><option style="color:blue" value="0">待执行</option><option style="color:green" value="1">已完成</option><option style="color:#888888" value="2">结束</option><option style="color:#ff6600" value="3">执行中</option><option style="color:#888888" value="5">已作废</option></select></td>
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
<div id="viewproject_{rand}"></div>
<!--HTMLend-->