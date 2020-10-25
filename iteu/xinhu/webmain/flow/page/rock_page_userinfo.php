<?php
/**
*	模块：userinfo.人员档案
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.人员档案]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'userinfo',modename='人员档案',isflow=0,modeid='29',atype = params.atype,pnum=params.pnum,modenames='工作经历,教育经历',listname='dXNlcmluZm8:';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"name","name":"\u59d3\u540d","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"deptname","name":"\u90e8\u95e8","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"ranking","name":"\u804c\u4f4d","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"state","name":"\u4eba\u5458\u72b6\u6001","fieldstype":"rockcombo","ispx":"1","isalign":"0","islb":"1"},{"fields":"zhaopian","name":"\u7167\u7247","fieldstype":"uploadimg","ispx":"0","isalign":"0","islb":"0"},{"fields":"idnum","name":"\u8eab\u4efd\u8bc1\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"tel","name":"\u7535\u8bdd","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"housedizhi","name":"\u5bb6\u5ead\u4f4f\u5740","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"nowdizhi","name":"\u73b0\u4f4f\u5740","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"hunyin","name":"\u5a5a\u59fb","fieldstype":"select","ispx":"0","isalign":"0","islb":"0"},{"fields":"mobile","name":"\u624b\u673a\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"email","name":"\u90ae\u7bb1","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"birtype","name":"\u751f\u65e5\u7c7b\u578b","fieldstype":"select","ispx":"0","isalign":"0","islb":"0"},{"fields":"birthday","name":"\u751f\u65e5","fieldstype":"date","ispx":"0","isalign":"0","islb":"1"},{"fields":"xueli","name":"\u5b66\u5386","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"minzu","name":"\u6c11\u65cf","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"jiguan","name":"\u7c4d\u8d2f","fieldstype":"selectdatafalse","ispx":"0","isalign":"0","islb":"0"},{"fields":"banknum","name":"\u5de5\u8d44\u5361\u5e10\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"bankname","name":"\u5f00\u6237\u884c","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"spareman","name":"\u5907\u7528\u8054\u7cfb\u4eba","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"sparetel","name":"\u5907\u7528\u8054\u7cfb\u4eba\u7535\u8bdd","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"workdate","name":"\u5165\u804c\u65e5\u671f","fieldstype":"date","ispx":"1","isalign":"0","islb":"1"},{"fields":"syenddt","name":"\u8bd5\u7528\u671f\u5230","fieldstype":"date","ispx":"0","isalign":"0","islb":"0"},{"fields":"positivedt","name":"\u8f6c\u6b63\u65e5\u671f","fieldstype":"date","ispx":"1","isalign":"0","islb":"1"},{"fields":"quitdt","name":"\u79bb\u804c\u65e5\u671f","fieldstype":"date","ispx":"0","isalign":"0","islb":"0"},{"fields":"companyid","name":"\u6240\u5c5e\u5355\u4f4d","fieldstype":"select","ispx":"0","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

if(atype=='all'){
	
	$('#tdright_{rand}').prepend(c.getbtnstr('人员状态编辑','bianjila','','disabled')+'&nbsp;&nbsp;');
	$('#tdright_{rand}').prepend(c.getbtnstr('更新数据','gengxin','success')+'&nbsp;&nbsp;');
	c.gengxin=function(){
		js.msg('wait', '更新中...');
		$.get(js.getajaxurl('updatedata','admin','system'), function(da){
			js.msg('success', da);
			a.reload();
		});
	}
	c.bianjila=function(){
		var h = $.bootsform({
			title:'人员状态编辑',height:400,width:400,
			tablename:bootparams.tablename,isedit:1,
			url:this.getacturl('publicsave'),aftersaveaction:'userstateafter',
			submitfields:'workdate,state,quitdt,syenddt,positivedt',
			items:[{
				labelText:'名称',name:'name'
			},{
				labelText:'人员状态',name:'state',type:'select',valuefields:'id',displayfields:'name',store:a.getData('statearr'),required:true
			},{
				labelText:'入职日期',name:'workdate',type:'date',required:true
			},{
				labelText:'离职日期',name:'quitdt',type:'date'
			},{
				labelText:'试用期到',name:'syenddt',type:'date'
			},{
				labelText:'转正日期',name:'positivedt',type:'date'
			}],
			success:function(){
				a.reload();
			}
		});
		h.setValues(a.changedata);
		h.setValue('name',a.changedata.name);
		h.setValue('state',a.changedata.stateval);
		h.isValid();
		return h;
	}
	bootparams.itemclick=function(){
		get('btnbianjila_{rand}').disabled=false;
	}
	bootparams.beforeload=function(){
		get('btnbianjila_{rand}').disabled=true;
	}
	
	$('#viewuserinfo_{rand}').after('<div class="tishi">添加人员档案请到[用户管理]那添加，删除档案，需要先删除用户在删除档案。</div>');
}
$('#tdleft_{rand}').hide();

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
		<td style="padding-left:10px"><select class="form-control" style="width:120px" id="selstatus_{rand}"><option value="">-全部状态-</option><option style="color:" value="0">试用期</option><option style="color:" value="1">正式</option><option style="color:" value="2">实习生</option><option style="color:" value="3">兼职</option><option style="color:" value="4">临时工</option><option style="color:" value="5">离职</option></select></td>
		<td style="padding-left:10px">
			<div style="white-space:nowrap">
			<button style="border-right:0;border-top-right-radius:0;border-bottom-right-radius:0" class="btn btn-default" click="searchbtn" type="button">搜索</button><button class="btn btn-default" id="downbtn_{rand}" type="button" style="padding-left:8px;padding-right:8px;border-top-left-radius:0;border-bottom-left-radius:0"><i class="icon-angle-down"></i></button> 
			</div>
		</td>
		<td  width="90%" style="padding-left:10px"><div id="changatype{rand}" class="btn-group"></div></td>
	
		<td align="right" id="tdright_{rand}" nowrap>
			<span style="display:none" id="daoruspan_{rand}"><button class="btn btn-default" click="daoru,1" type="button">导入</button>&nbsp;&nbsp;&nbsp;</span><button class="btn btn-default" style="display:none" id="daobtn_{rand}" disabled click="daochu" type="button">导出 <i class="icon-angle-down"></i></button> 
		</td>
	</tr>
	</table>
</div>
<div class="blank10"></div>
<div id="viewuserinfo_{rand}"></div>
<!--HTMLend-->