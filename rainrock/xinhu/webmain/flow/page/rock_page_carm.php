<?php
/**
*	模块：carm.车辆管理
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.车辆管理]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'carm',modename='车辆管理',isflow=0,modeid='42',atype = params.atype,pnum=params.pnum,modenames='',listname='Y2FybQ::';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"carnum","name":"\u8f66\u724c\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"cartype","name":"\u8f66\u8f86\u7c7b\u578b","fieldstype":"rockcombo","ispx":"0","isalign":"0","islb":"1"},{"fields":"carbrand","name":"\u8f66\u8f86\u54c1\u724c","fieldstype":"rockcombo","ispx":"0","isalign":"0","islb":"1"},{"fields":"carmode","name":"\u578b\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"buydt","name":"\u8d2d\u4e70\u65e5\u671f","fieldstype":"date","ispx":"1","isalign":"0","islb":"1"},{"fields":"buyprice","name":"\u8d2d\u4e70\u4ef7\u683c","fieldstype":"number","ispx":"0","isalign":"0","islb":"1"},{"fields":"framenum","name":"\u8f66\u67b6\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"enginenb","name":"\u53d1\u52a8\u673a\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"ispublic","name":"\u516c\u5f00\u4f7f\u7528","fieldstype":"checkbox","ispx":"1","isalign":"0","islb":"1"},{"fields":"state","name":"\u72b6\u6001","fieldstype":"select","ispx":"1","isalign":"0","islb":"1"},{"fields":"explain","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"1"},{"fields":"djshu","name":"\u767b\u8bb0\u6570","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

c.setcolumns('djshu',{
	renderer:function(v,d){
		var s=''+v+'';
		if(v>0)s+=',<a onclick="valdwew{rand}('+d.id+',\''+d.carnum+'\')" href="javascript:;">查看</a>';
		if(v=='0')s='&nbsp;';
		return s;
	}
});
valdwew{rand}=function(cid,nums){
	addtabs({url:'flow,page,carms,atype=all,pnum=all,carid='+cid+'',num:'cacarmss'+cid+'',name:'['+nums+']车辆登记查看'});
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
<div id="viewcarm_{rand}"></div>
<!--HTMLend-->