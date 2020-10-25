<?php
/**
*	模块：company.公司单位
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.公司单位]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'company',modename='公司单位',isflow=0,modeid='63',atype = params.atype,pnum=params.pnum,modenames='',listname='Y29tcGFueQ::';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"name","name":"\u540d\u79f0","fieldstype":"text","ispx":"0","isalign":"1","islb":"1"},{"fields":"logo","name":"\u5bf9\u5e94logo","fieldstype":"uploadimg","ispx":"0","isalign":"0","islb":"1"},{"fields":"oaname","name":"\u663e\u793aOA\u540d\u79f0","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"oanemes","name":"\u79fb\u52a8\u7aef\u663e\u793a","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"num","name":"\u5355\u4f4d\u7f16\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"nameen","name":"\u5bf9\u5e94\u82f1\u6587\u540d","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"pid","name":"\u6240\u5c5e\u5355\u4f4d","fieldstype":"select","ispx":"0","isalign":"0","islb":"0"},{"fields":"fuzename","name":"\u5bf9\u5e94\u8d1f\u8d23\u4eba","fieldstype":"changeusercheck","ispx":"0","isalign":"0","islb":"1"},{"fields":"city","name":"\u6240\u5728\u57ce\u5e02","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"address","name":"\u5730\u5740","fieldstype":"text","ispx":"0","isalign":"1","islb":"0"},{"fields":"tel","name":"\u7535\u8bdd","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"fax","name":"\u4f20\u771f","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"sort","name":"\u6392\u5e8f\u53f7","fieldstype":"number","ispx":"0","isalign":"0","islb":"1"},{"fields":"id","name":"ID","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

bootparams.fanye=false;
bootparams.celleditor=true;
bootparams.tree=true;
c.setcolumns('sort',{
	'editor':true
});
c.setcolumns('city',{
	'editor':true
});
c.setcolumns('oaname',{
	'editor':true
});
c.setcolumns('oanemes',{
	'editor':true
});
c.setcolumns('num',{
	'editor':true
});
c.setcolumns('logo',{
	'renderer':function(v){
		if(!isempt(v)){
			v='<img src="'+v+'" width="30" height="30">';
		}else{
			v='&nbsp;';
		}
		return v;
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
<div id="viewcompany_{rand}"></div>
<!--HTMLend-->