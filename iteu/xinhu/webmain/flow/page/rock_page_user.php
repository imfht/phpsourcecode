<?php
/**
*	模块：user.用户
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.用户]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'user',modename='用户',isflow=0,modeid='10',atype = params.atype,pnum=params.pnum,modenames='',listname='YWRtaW4:';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"face","name":"\u5934\u50cf","fieldstype":"uploadimg","ispx":"0","isalign":"0","islb":"1"},{"fields":"name","name":"\u59d3\u540d","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"user","name":"\u7528\u6237\u540d","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"pass","name":"\u5bc6\u7801","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"num","name":"\u7f16\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"workdate","name":"\u5165\u804c\u65e5\u671f","fieldstype":"date","ispx":"1","isalign":"0","islb":"0"},{"fields":"sex","name":"\u6027\u522b","fieldstype":"select","ispx":"0","isalign":"0","islb":"1"},{"fields":"mobile","name":"\u624b\u673a\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"deptid","name":"\u90e8\u95e8Id","fieldstype":"number","ispx":"0","isalign":"0","islb":"0"},{"fields":"deptname","name":"\u90e8\u95e8","fieldstype":"changedept","ispx":"0","isalign":"0","islb":"0"},{"fields":"deptallname","name":"\u90e8\u95e8\u5168\u79f0","fieldstype":"text","ispx":"0","isalign":"1","islb":"1"},{"fields":"ranking","name":"\u804c\u4f4d","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"deptnames","name":"\u591a\u90e8\u95e8","fieldstype":"changedeptcheck","ispx":"0","isalign":"0","islb":"0"},{"fields":"rankings","name":"\u591a\u804c\u4f4d","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"superman","name":"\u4e0a\u7ea7\u4e3b\u7ba1","fieldstype":"changeusercheck","ispx":"0","isalign":"0","islb":"1"},{"fields":"superid","name":"\u4e0a\u7ea7\u4e3b\u7ba1id","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"pingyin","name":"\u540d\u5b57\u62fc\u97f3","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"tel","name":"\u7535\u8bdd","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"email","name":"\u90ae\u7bb1","fieldstype":"email","ispx":"0","isalign":"0","islb":"0"},{"fields":"weixinid","name":"\u5fae\u4fe1\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"companyid","name":"\u6240\u5c5e\u5355\u4f4d","fieldstype":"select","ispx":"0","isalign":"0","islb":"0"},{"fields":"status","name":"\u542f\u7528","fieldstype":"select","ispx":"1","isalign":"0","islb":"1"},{"fields":"type","name":"\u7ba1\u7406\u5458","fieldstype":"select","ispx":"1","isalign":"0","islb":"1"},{"fields":"id","name":"\u7528\u6237Id","fieldstype":"number","ispx":"0","isalign":"0","islb":"1"},{"fields":"sort","name":"\u6392\u5e8f\u53f7","fieldstype":"number","ispx":"0","isalign":"0","islb":"1"},{"fields":"groupname","name":"\u6240\u5728\u7ec4","fieldstype":"checkboxall","ispx":"0","isalign":"0","islb":"0"},{"fields":"isvcard","name":"\u901a\u8baf\u5f55","fieldstype":"checkbox","ispx":"0","isalign":"0","islb":"1"},{"fields":"dwid","name":"\u66f4\u591a\u5355\u4f4d","fieldstype":"hidden","ispx":"0","isalign":"0","islb":"0"},{"fields":"temp_dwid","name":"\u66f4\u591a\u6240\u5c5e\u5355\u4f4d","fieldstype":"selectdatatrue","ispx":"0","isalign":"0","islb":"0"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

bootparams.statuschange = true;
bootparams.celleditor = (atype=='all');
if(ISDEMO)bootparams.celleditor=false;
c.setcolumns('status',{
	editor:true,
	type:'checkbox',
	editorafter:function(){
		a.reload();
	},
	editorbefore:function(d){
		if(d.id=='1'){
			js.msg('msg','ID=1的用户不能编辑');
			return false;
		}else{
			return true;
		}
	}
});

c.setcolumns('isvcard',{
	editor:true,
	type:'checkbox'
});

c.setcolumns('sex',{
	editor:true,
	editor:true,type:'select',store:[['男','男'],['女','女']]
});

c.setcolumns('sort',{
	editor:true
});
c.setcolumns('tel',{
	editor:true
});
c.setcolumns('face',{
	renderer:function(v,d){
		if(isempt(v))v='images/noface.png';
		return '<img src="'+v+'" id="faceviewabc_'+d.id+'" height="24" width="24">';
	}
});
if(atype=='all'){
	bootparams.checked=true;
	$('#tdright_{rand}').prepend(c.getbtnstr('修改上级','editsuper')+'&nbsp;&nbsp;');
	$('#tdright_{rand}').prepend(c.getbtnstr('修改头像','editface','','disabled')+'&nbsp;&nbsp;');
	$('#tdright_{rand}').prepend(c.getbtnstr('更新数据','gengxin','success')+'&nbsp;&nbsp;');

	c.gengxin=function(){
		js.msg('wait', '更新中...');
		$.get(js.getajaxurl('updatedata','admin','system'), function(da){
			js.msg('success', da);
		});
	}
	c.editface=function(){
		editfacechang(a.changeid, a.changedata.name);
	}
	bootparams.itemclick=function(){
		get('btneditface_{rand}').disabled=false;
	}
	bootparams.beforeload=function(){
		get('btneditface_{rand}').disabled=true;
	}
	c.editsuper=function(){
		var xid = a.getchecked();
		if(xid==''){js.msg('msg','请先用复选框选择行');return;}
		var cans = {
			type:'usercheck',
			title:'选择新的上级主管',
			callback:function(sna,sid){
				if(sna=='')return;
				js.msg('wait','修改中...');
				js.ajax(c.getacturl('editsuper'),{sna:sna,sid:sid,xid:xid}, function(ret){
					js.msg('success', '修改成功');
					a.reload();
				},'post');
				
			}
		};
		js.getuser(cans);
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
<div id="viewuser_{rand}"></div>
<!--HTMLend-->