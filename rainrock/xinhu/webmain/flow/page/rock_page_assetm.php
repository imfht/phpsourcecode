<?php
/**
*	模块：assetm.固定资产
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.固定资产]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'assetm',modename='固定资产',isflow=0,modeid='41',atype = params.atype,pnum=params.pnum,modenames='',listname='YXNzZXRt';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"typeid","name":"\u8d44\u4ea7\u5206\u7c7b","fieldstype":"select","ispx":"0","isalign":"0","islb":"1"},{"fields":"num","name":"\u7f16\u53f7","fieldstype":"num","ispx":"1","isalign":"0","islb":"1"},{"fields":"title","name":"\u540d\u79f0","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"brand","name":"\u54c1\u724c","fieldstype":"rockcombo","ispx":"1","isalign":"0","islb":"0"},{"fields":"address","name":"\u6240\u5728\u4f4d\u7f6e","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"model","name":"\u89c4\u683c\u578b\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"laiyuan","name":"\u8d44\u4ea7\u6765\u6e90","fieldstype":"rockcombo","ispx":"0","isalign":"0","islb":"0"},{"fields":"state","name":"\u72b6\u6001","fieldstype":"select","ispx":"1","isalign":"0","islb":"1"},{"fields":"buydt","name":"\u8d2d\u8fdb\u65e5\u671f","fieldstype":"date","ispx":"0","isalign":"0","islb":"0"},{"fields":"price","name":"\u4ef7\u683c","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"usename","name":"\u4f7f\u7528\u8005","fieldstype":"changedeptusercheck","ispx":"0","isalign":"0","islb":"1"},{"fields":"fengmian","name":"\u5c01\u9762\u56fe\u7247","fieldstype":"uploadimg","ispx":"0","isalign":"0","islb":"1"},{"fields":"explain","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"0"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

if(pnum=='all'){
	bootparams.checked=true;
	bootparams.autoLoad=false;

	var shtm = '<table width="100%"><tr valign="top"><td><div style="border:1px #cccccc solid;width:220px"><div id="optionview_{rand}" style="height:400px;overflow:auto;"></div></div></td><td width="10" nowrap>&nbsp;</td><td width="95%"><div id="viewassetm_{rand}"></div></td></tr></table>';
	$('#viewassetm_{rand}').after(shtm).remove();
	c.stable = 'assetm';
	c.optionview = 'optionview_{rand}';
	c.optionnum = 'assetstype';
	c.title = '资产分类';
	c.rand = '{rand}';

	var c = new optionclass(c);

	$('#'+c.optionview+'').css('height',''+(viewheight-120)+'px');
	$('#tdright_{rand}').prepend(c.getbtnstr('所有资产','allshow')+'&nbsp;&nbsp;');
	$('#tdright_{rand}').prepend(c.getbtnstr('打印二维码','prinwem')+'&nbsp;&nbsp;');
	$('#tdright_{rand}').prepend('<span id="megss{rand}"></span>&nbsp;&nbsp;');
	setTimeout(function(){c.mobj=a},5);//延迟设置，不然不能双击分类搜索

	c.prinwem=function(){
		var sid = a.getchecked();
		if(sid==''){
			js.msg('msg','没有选中记录');
			return;
		}
		var url = '?a=printewm&m=assetm&d=main&sid='+sid+'';
		window.open(url);
	}
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
			<span style="display:none" id="daoruspan_{rand}"><button class="btn btn-default" click="daoru,1" type="button">导入</button>&nbsp;&nbsp;&nbsp;</span><button class="btn btn-default" style="display:none" id="daobtn_{rand}" disabled click="daochu" type="button">导出 <i class="icon-angle-down"></i></button> 
		</td>
	</tr>
	</table>
</div>
<div class="blank10"></div>
<div id="viewassetm_{rand}"></div>
<!--HTMLend-->