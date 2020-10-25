<?php
/**
*	模块：kqjsn.考勤机设备
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.考勤机设备]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'kqjsn',modename='考勤机设备',isflow=0,modeid='70',atype = params.atype,pnum=params.pnum,modenames='',listname='a3Fqc24:';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"num","name":"\u8bbe\u5907\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"name","name":"\u8bbe\u5907\u540d\u79f0","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"company","name":"\u516c\u53f8\u540d","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"status","name":"\u72b6\u6001","fieldstype":"text","ispx":"1","isalign":"0","islb":"1"},{"fields":"pinpai","name":"\u54c1\u724c","fieldstype":"select","ispx":"1","isalign":"0","islb":"1"},{"fields":"sort","name":"\u6392\u5e8f\u53f7","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"},{"fields":"lastdt","name":"\u6700\u540e\u8bf7\u6c42\u65f6\u95f4","fieldstype":"datetime","ispx":"1","isalign":"0","islb":"1"},{"fields":"id","name":"ID","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"},{"fields":"usershu","name":"\u4eba\u5458\u6570","fieldstype":"number","ispx":"0","isalign":"0","islb":"1"},{"fields":"fingerprintshu","name":"\u6307\u7eb9\u6570","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"},{"fields":"clockinshu","name":"\u6253\u5361\u6570","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"},{"fields":"headpicshu","name":"\u5934\u50cf\u6570\u91cf","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"},{"fields":"picshu","name":"\u73b0\u573a\u7167\u7247\u6570","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"},{"fields":"romver","name":"\u7cfb\u7edf\u7248\u672c","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"appver","name":"\u5e94\u7528\u7248\u672c","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"model","name":"\u8bbe\u5907\u578b\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"snip","name":"\u5206\u914d\u7684ip","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"snport","name":"\u5206\u914d\u7aef\u53e3\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"space","name":"sd\u5361\u5269\u4f59\u7a7a\u95f4","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"},{"fields":"memory","name":"\u5269\u4f59\u5185\u5b58","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

bootparams.celleditor = true;
	bootparams.checked = true;
	$('#tdright_{rand}').prepend(c.getbtnstr('选中设备操作 <i class="icon-angle-down"></i>','optbtn')+'&nbsp;');
	
	$('#btnoptbtn_{rand}').rockmenu({
		width:170,top:35,donghua:false,
		data:[{
			name:'设置配置',lx:'config'
		},{
			name:'重启',lx:'reboot'
		},{
			name:'获取所有人员',lx:'getuser'
		},{
			name:'获取设备信息',lx:'getinfo'
		},{
			name:'设置广告图1',lx:'advert1'
		},{
			name:'设置广告图2',lx:'advert2'
		},{
			name:'设置广告图3',lx:'advert3'
		}],
		itemsclick:function(d, i){
			c.sendcmd(0, d.lx);
		}
	});
	c.optbtn=function(){
	}
	c.sendcmd=function(id, type){
		var ids = a.getchecked();
		if(ids==''){js.msg('msg','没用复选框选中记录');return;}
		js.ajax(js.getajaxurl('sendcmd','kaoqinj','main'),{ids:ids,'type':type},function(ret){
			if(!ret.success){
				js.msg('msg', ret.msg);
			}else{
				js.msg('success', ret.data);
			}
		},'get,json',false,'发送中...,已发送');
	}
	
	c.setcolumns('num',{
		'renderer':function(v,d,i){
			return ''+v+' <a onclick="show_{rand}('+i+')" href="javascript:;">管理</a>';
		}
	});
	
	show_{rand}=function(i){
		var d = a.getData(i);
		addtabs({num:'sngl'+d.id+'',name:'考勤机设备['+d.name+']管理',url:'main,kaoqinj,dept,snid='+d.id+''});
	}
	
	c.setcolumns('sort',{
		'editor':true
	});
	
	c.setcolumns('name',{
		'editor':true
	});
	
	c.setcolumns('company',{
		'editor':true
	});
	
	c.setcolumns('status',{
		'editor':true,
		'type':'checkbox'
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
<div id="viewkqjsn_{rand}"></div>
<!--HTMLend-->