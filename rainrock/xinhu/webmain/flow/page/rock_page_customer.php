<?php
/**
*	模块：customer.客户管理
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.客户管理]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'customer',modename='客户管理',isflow=0,modeid='7',atype = params.atype,pnum=params.pnum,modenames='',listname='Y3VzdG9tZXI:';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"name","name":"\u5ba2\u6237\u540d\u79f0","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"suoname","name":"\u6240\u5c5e\u4eba","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"type","name":"\u5ba2\u6237\u7c7b\u578b","fieldstype":"rockcombo","ispx":"1","isalign":"0","islb":"1"},{"fields":"laiyuan","name":"\u6765\u6e90","fieldstype":"rockcombo","ispx":"0","isalign":"0","islb":"0"},{"fields":"unitname","name":"\u5ba2\u6237\u5355\u4f4d","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"tel","name":"\u8054\u7cfb\u7535\u8bdd","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"mobile","name":"\u8054\u7cfb\u624b\u673a","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"email","name":"\u90ae\u7bb1","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"sheng","name":"\u6240\u5728\u7701","fieldstype":"selectdatafalse","ispx":"0","isalign":"0","islb":"0"},{"fields":"shi","name":"\u6240\u5728\u5e02","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"address","name":"\u5730\u5740","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"routeline","name":"\u4ea4\u901a\u8def\u7ebf","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"0"},{"fields":"shibieid","name":"\u7eb3\u7a0e\u8bc6\u522b\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"openbank","name":"\u5f00\u6237\u884c","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"cardid","name":"\u5f00\u6237\u5e10\u53f7","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"status","name":"\u72b6\u6001","fieldstype":"select","ispx":"0","isalign":"0","islb":"0"},{"fields":"isstat","name":"\u6807\u2605","fieldstype":"select","ispx":"1","isalign":"0","islb":"0"},{"fields":"isgys","name":"\u4f9b\u5e94\u5546","fieldstype":"checkbox","ispx":"0","isalign":"0","islb":"0"},{"fields":"linkname","name":"\u8054\u7cfb\u4eba","fieldstype":"text","ispx":"0","isalign":"0","islb":"0"},{"fields":"explain","name":"\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"0"},{"fields":"htshu","name":"\u5408\u540c\u6570","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"},{"fields":"moneyz","name":"\u9500\u552e\u603b\u989d","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"},{"fields":"moneyd","name":"\u5f85\u6536\u91d1\u989d","fieldstype":"number","ispx":"1","isalign":"0","islb":"1"},{"fields":"isgh","name":"\u653e\u5165\u516c\u6d77","fieldstype":"select","ispx":"0","isalign":"0","islb":"0"},{"fields":"lastdt","name":"\u6700\u540e\u8ddf\u8fdb","fieldstype":"datetime","ispx":"1","isalign":"0","islb":"1"}],fieldsselarr= {"columns_customer_":"name,suoname,type,unitname,mobile,htshu,moneyz,moneyd,lastdt,caozuo","columns_customer_all":"name,suoname,type,unitname,isstat,isgys,htshu,moneyz,moneyd,lastdt,caozuo","columns_customer_dist":"name,suoname,type,laiyuan,unitname,isgys,linkname","columns_customer_ghai":"name,suoname,type,unitname,sheng,shi,status,caozuo","columns_customer_gys":"name,suoname,type,unitname,tel,address,status,linkname,explain,caozuo","columns_customer_shate":"name,suoname,type,laiyuan,unitname,isstat,caozuo"},chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

var chengsuid = '';
if(atype!='my')$('#daoruspan_{rand}').remove();
if(pnum=='' || pnum=='all'){
	bootparams.checked = true;

	c.move=function(){
		var s= a.getchecked();
		if(s==''){js.msg('msg','没有选择记录');return;}
		chengsuid=s;
		js.confirm('是否客户转移给其他人，并客户下的合同和待收付款单和销售机会和销售单同时转移？', function(jg){
			if(jg=='yes')c.moveto();
		});
	}
	c.movetoss=function(sna,toid){
		js.ajax(js.getajaxurl('movecust',modenum,'main'),{'toid':toid,'sid':chengsuid},function(s){
			a.reload();
		},'post',false,'转移给:'+sna+'...,转移成功');
	}
	c.moveto=function(sid){
		var cans = {
			type:'user',
			title:'转移给...',
			callback:function(sna,sid){
				if(sid)c.movetoss(sna,sid);
			}
		}
		setTimeout(function(){js.getuser(cans);},10);
	}
	$('#tdright_{rand}').append('&nbsp; '+c.getbtnstr('客户转移','move'));
}

if(pnum!='gys' && pnum!='')$('#tdleft_{rand}').hide();
if(pnum=='dist'){
	bootparams.checked = true;
	c.distss=function(o1,lx){
		var s = a.getchecked();
		if(s==''){js.msg('msg','没有选中行');return;}
		if(lx==0){
			js.confirm('确定要将选中标为未分配吗？',function(jg){
				if(jg=='yes')c.distssok(s, '','', 0);
			});
			return;
		}
		var cans = {
			type:'user',
			title:'选中分配给...',
			callback:function(sna,sid){
				if(sna=='')return;
				setTimeout(function(){
					js.confirm('确定要将选中记录分配给：['+sna+']吗？',function(jg){
						if(jg=='yes')c.distssok(s, sna,sid,1);
					});
				},10);
			}
		};
		js.getuser(cans);
	}
	c.distssok=function(s, sna,sid, lx){
		js.ajax(js.getajaxurl('distcust',modenum,'main'),{sid:s,sname:sna,snid:sid,lx:lx},function(s){
			a.reload();
		},'post','','处理中...,处理成功');
	}
	$('#tdright_{rand}').prepend(c.getbtnstr('标为未分配','distss,0')+'&nbsp;');
	$('#tdright_{rand}').prepend(c.getbtnstr('选中分配给','distss,1')+'&nbsp;&nbsp;');
}

if(pnum!='gys' && pnum!='ghai'){
	$('#tdright_{rand}').prepend(c.getbtnstr('重新统计金额','retotal')+'&nbsp;');

	c.retotal=function(){
		js.ajax(js.getajaxurl('retotal',modenum,'main'),{},function(s){
			a.reload();
		},'get',false,'统计中...,统计完成')
	}
}
if(pnum=='gys'){	
	modename = '供应商管理';
	c.clickwin=function(o1,lx){
		openinput(modename,modenum,'0&def_isgys=1','opegs{rand}');
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
<div id="viewcustomer_{rand}"></div>
<!--HTMLend-->