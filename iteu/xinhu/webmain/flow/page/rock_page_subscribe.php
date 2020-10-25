<?php
/**
*	模块：subscribe.订阅管理
*	说明：自定义区域内可写你想要的代码
*	来源：流程模块→表单元素管理→[模块.订阅管理]→生成列表页
*/
defined('HOST') or die ('not access');
?>
<script>
$(document).ready(function(){
	{params}
	var modenum = 'subscribe',modename='订阅管理',isflow=0,modeid='67',atype = params.atype,pnum=params.pnum,modenames='',listname='c3Vic2NyaWJl';
	if(!atype)atype='';if(!pnum)pnum='';
	var fieldsarr = [{"name":"\u7533\u8bf7\u4eba","fields":"base_name"},{"name":"\u7533\u8bf7\u4eba\u90e8\u95e8","fields":"base_deptname"},{"name":"\u5355\u53f7","fields":"sericnum"},{"fields":"optname","name":"\u8ba2\u9605\u4eba","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"title","name":"\u8ba2\u9605\u6807\u9898","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"cont","name":"\u8ba2\u9605\u63d0\u9192\u5185\u5bb9","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"1"},{"fields":"explain","name":"\u8ba2\u9605\u8bf4\u660e","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"1"},{"fields":"optdt","name":"\u8ba2\u9605\u65f6\u95f4","fieldstype":"datetime","ispx":"1","isalign":"0","islb":"1"},{"fields":"status","name":"\u72b6\u6001","fieldstype":"checkbox","ispx":"0","isalign":"0","islb":"1"},{"fields":"recename","name":"\u540c\u65f6\u8ba2\u9605\u7ed9","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"ratecont","name":"\u8ba2\u9605\u63d0\u9192\u65f6\u95f4","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"},{"fields":"suburl","name":"\u8ba2\u9605\u5730\u5740","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"0"},{"fields":"suburlpost","name":"\u8ba2\u9605\u5730\u5740post\u53c2\u6570","fieldstype":"textarea","ispx":"0","isalign":"0","islb":"0"},{"fields":"lastdt","name":"\u6700\u540e\u8fd0\u884c\u65f6\u95f4","fieldstype":"datetime","ispx":"1","isalign":"0","islb":"1"},{"fields":"shatename","name":"\u5171\u4eab\u7ed9","fieldstype":"changedeptusercheck","ispx":"0","isalign":"0","islb":"1"},{"fields":"id","name":"ID","fieldstype":"text","ispx":"0","isalign":"0","islb":"1"}],fieldsselarr= [],chufarr= [];
	
	<?php
	include_once('webmain/flow/page/rock_page.php');
	?>
	
//[自定义区域start]

//设置单元格可编辑
	bootparams.celleditor = true;
	c.setcolumns('title',{
		editor:true
	});
	c.setcolumns('explain',{
		editor:true,
		type:'textarea'
	});
	c.setcolumns('cont',{
		editor:true,
		type:'textarea'
	});
	
	c.setcolumns('status',{
		editor:true,
		type:'checkbox',
		editorafter:function(){
			c.reload();
		}
	});
	
	$('#tdright_{rand}').prepend(c.getbtnstr('运行订阅','dinghue','','disabled')+'&nbsp;&nbsp;');
	c.dinghue=function(){
		js.msg('wait','运行中...');
		js.ajax(this.getacturl('yunsubscribe'),{id:a.changeid},function(ret){
			if(ret.success){
				js.msg('success', '运行成功');
				a.reload();
			}else{
				js.msg('msg', ret.msg);
			}
		},'get,json');
	}
	bootparams.itemclick=function(){
		get('btndinghue_{rand}').disabled=false;
	}
	bootparams.beforeload=function(){
		get('btndinghue_{rand}').disabled=true;
	}

	$('#viewsubscribe_{rand}').after('<div class="tishi">未设置订阅提醒时间是不会生效哦，可在[操作]菜单上添加提醒设置，查看<a href="<?=URLY?>view_subscribe.html" target="_blank">[帮助]</a>。</div>');
	
	c.clickwin=function(){
		js.confirm('订阅管理新增不是在这里的，请到各个列表页面下的[导出]按钮下点订阅，是否打开相关帮助说明？',function(jg){
			if(jg=='yes')window.open('<?=URLY?>view_subscribe.html');
		});
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
		<td style="padding-left:10px"><select class="form-control" style="width:120px" id="selstatus_{rand}"><option value="">-全部状态-</option><option style="color:#888888" value="0">停用</option><option style="color:green" value="1">启用</option></select></td>
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
<div id="viewsubscribe_{rand}"></div>
<!--HTMLend-->