<?php
/**
*	模块：wordxie.文档协作
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.文档协作]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'wordxie',modename='文档协作',isflow=0,modeid='86',atype = params.atype,pnum=params.pnum,modenames='',listname='d29yZHhpZQ::';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"wtype","name":"\u7c7b\u578b","fieldstype":"select","ispx":"1","isalign":"0","islb":"1"},{"fields":"name","name":"\u6587\u6863\u540d\u79f0","fieldstype":"text","ispx":"0","isalign":"1","islb":"1"},{"fields":"fenlei","name":"\u5206\u7c7b","fieldstype":"text","ispx":"1","isalign":"0","islb":"1"},{"fields":"xiename","name":"\u534f\u4f5c\u4eba","fieldstype":"changedeptusercheck","ispx":"0","isalign":"0","islb":"1"},{"fields":"recename","name":"\u53ef\u67e5\u770b\u4eba","fieldstype":"changedeptusercheck","ispx":"0","isalign":"0","islb":"1"},{"fields":"optname","name":"\u521b\u5efa\u4eba","fieldstype":"text","ispx":"1","isalign":"0","islb":"1"},{"fields":"optdt","name":"\u64cd\u4f5c\u65f6\u95f4","fieldstype":"datetime","ispx":"1","isalign":"0","islb":"1"},{"fields":"explian","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"1"},{"fields":"temp_opt","name":"\u64cd\u4f5c","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

c.setcolumns('wtype', {
	renderer:function(v,d){
		return '<img src="web/images/fileicons/'+v+'.gif" height="20">';
	}
});
c.setcolumns('name', {
	renderer:function(v,d){
		var ss=' <span class="label label-success label-sm">协作</span>';
		if(!d.xiebool)ss=' <span class="label label-default">只读</span>';
		return ''+v+'.'+d.wtype+''+ss+'';
	}
});
c.setcolumns('temp_opt', {
	renderer:function(v,d,oi){
		var lxs = ',doc,docx,xls,xlsx,ppt,pptx,';
	
		var str = (lxs.indexOf(','+d.wtype+',')>-1 && d.xiebool) ? '&nbsp;<a href="javascript:;" onclick="showvies{rand}('+oi+',3)">编辑</a>&nbsp;<a href="javascript:;" onclick="showvies{rand}('+oi+',1)"><i class="icon-arrow-down"></i></a>' : '';
		return '<a href="javascript:;" onclick="showvies{rand}('+oi+',0)">预览</a>'+str+'';
	},
	text:'&nbsp;'
});
showvies{rand}=function(oi,lx){
	var d=a.getData(oi);
	if(lx==3){
		//js.sendeditoffice(d.fileid);
		js.fileopt(d.fileid,2);
		return;
	}
	if(lx==1){
		js.downshow(d.fileid)
	}else{
		js.yulanfile(d.fileid,d.wtype,d.filepath,d.name);
	}
}
$('#viewwordxie_{rand}').after('<div class="tishi">如没有在线编辑插件，可用下载下来编辑写好了在上传，上传的文档名称需一致。</div>');
$('#tdright_{rand}').prepend(c.getbtnstr('上传写好文件','upxieok')+'&nbsp;');
var btnupobj = get('btnupxieok_{rand}');
btnupobj.disabled=true;
bootparams.itemclick=function(d){
	btnupobj.disabled = (!d.xiebool);	
}
bootparams.load=function(d){
	c.loaddata(d);
	btnupobj.disabled=true;
}
c.upxieok=function(){
	var d = a.changedata;
	js.upload('upfilexiezuo{rand}|upchagneback{rand}',{maxup:'1','title':d.name+'.'+d.wtype,uptype:d.wtype});
}
upchagneback{rand}=function(f){
	var d = a.changedata;
	if(f.name.indexOf(d.name)!=0)return '选择的文件名不一致，必须选['+d.name+'.'+d.wtype+']';
}
upfilexiezuo{rand}=function(d){
	js.ajax(c.getacturl('savefile'),{'id':a.changedata.id,'upfileid':d[0].id},function(s){
		a.reload();
	},'get',false, '保存中...,保存成功');
}
openxieeditfile=function(d){
	js.fileopt(d.fileid,2);
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
<div id="viewwordxie_{rand}"></div>
<!--HTMLend-->