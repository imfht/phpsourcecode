<?php if(!defined('HOST'))die('not access');?>
<script >
$(document).ready(function(){
	var a = $('#veiw_{rand}').bootstable({
		tablename:'task',celleditor:true,modenum:'task',sort:'sort',dir:'asc',
		columns:[{
			text:'分类',dataIndex:'fenlei',editor:true,sortable:true
		},{
			text:'名称',dataIndex:'name',editor:true,align:'left'
		},{
			text:'地址',dataIndex:'url',editor:true
		},{
			text:'频率',dataIndex:'type',editor:true
		},{
			text:'运行时间',dataIndex:'time',editor:true
		},{
			text:'运行说明',dataIndex:'ratecont',editor:true
		},{
			text:'状态',dataIndex:'status',editor:true,type:'checkbox',editor:true,sortable:true
		},{
			text:'排序号',dataIndex:'sort',editor:true,sortable:true
		},{
			text:'最后运行',dataIndex:'lastdt',sortable:true,renderer:function(v){
				return v.replace(' ','<br>');
			}
		},{
			text:'最后状态',dataIndex:'state',renderer:function(v){
				var s='<font color=#888888>待运行</font>';
				if(v==1)s='<font color=green>成功</font>';
				if(v==2)s='<font color=red>失败</font>';
				return s;
			}
		},{
			text:'最后内容',dataIndex:'lastcont'
		},{
			text:'说明',dataIndex:'explain',type:'textarea',editor:true,align:'left'
		},{
			text:'提醒给',dataIndex:'todoname'	
		},{
			text:'ID',dataIndex:'id'	
		}],
		itemclick:function(){
			btn(false);
		}
	});
	
	var c = {
		del:function(){
			a.del({check:function(lx){if(lx=='yes')btn(true)}});
		},
		clickwin:function(o1,lx){
			var h = $.bootsform({
				title:'计划任务',height:400,width:500,
				tablename:'task',isedit:lx,
				params:{int_filestype:'sort,status'},
				submitfields:'fenlei,name,url,sort,status,type,time,ratecont,todoid,todoname',
				items:[{
					labelText:'分类',name:'fenlei',required:true
				},{
					labelText:'名称',name:'name',required:true
				},{
					labelText:'运行地址',name:'url',required:true
				},{
					labelText:'频率',name:'type',required:true
				},{
					labelText:'运行时间',name:'time',required:true
				},{
					labelText:'说明',name:'ratecont'
				},{
					name:'status',labelBox:'启用',type:'checkbox',checked:true
				},{
					name:'todoid',type:'hidden'
				},{
					labelText:'通知给',type:'changeuser',changeuser:{
						type:'usercheck',idname:'todoid',title:'选择人员'
					},name:'todoname',clearbool:true
				},{
					labelText:'序号',name:'sort',type:'number',value:'0'
				}],
				success:function(){
					a.reload();
				}
			});
			if(lx==1){
				h.setValues(a.changedata);
			}
			h.getField('name').focus();
		},
		refresh:function(){
			a.reload();
		},
		creaefile:function(){
			js.msg('wait','创建中...');
			js.ajax(js.getajaxurl('creaefile','{mode}','{dir}'),{},function(s){
				js.msg('success','创建成功');
			});
		},
		yunx:function(){
			if(a.changedata.status!=1){
				js.msg('msg','没有启用不能运行');
				return;
			}
			js.msg('wait','运行中...');
			var url='task.php?m=runt&a=run&mid='+a.changeid+'';
			var ase = a.changedata.url.split(',');
			var url='task.php?m='+ase[0]+'|runt&a='+ase[1]+'&runid='+a.changeid+'';
			js.ajax(url,{},function(s){
				if(s.indexOf('success')>=0){
					if(s!='success'){
						js.msg();
						js.alert('运行成功，而你可能用记事本修改系统文件了，请到群里《@信呼客服 记事本》查看帮助');
					}else{
						js.msg('success','运行成功');
					}
					a.reload();
				}else{
					js.msg('msg','运行失败');
				}
			});
		},
		start:function(lx){
			js.msg('wait','处理中...');
			js.ajax(js.getajaxurl('starttask','{mode}','{dir}'),{lx:lx},function(ret){
				if(ret.success){
					js.msg('success', ret.data);
				}else{
					js.msg('msg', ret.msg);
				}
			},'get,json');
		},
		clearzt:function(){
			js.msg('wait','清空中...');
			js.ajax(js.getajaxurl('clearzt','{mode}','{dir}'),{},function(s){
				js.msg();
				a.reload();
			});
		},
		openanz:function(){
			js.open(js.getajaxurl('downbat','{mode}','{dir}'));
		},
		openqueue:function(){
			js.open('?a=queue&m=task&d=system');
		}
	};
	
	function btn(bo){
		if(ISDEMO)return;
		get('del_{rand}').disabled = bo;
		get('edit_{rand}').disabled = bo;
		get('yun_{rand}').disabled = bo;
	}
	js.initbtn(c);
	
	$('#randkstrt_{rand}').rockmenu({
		width:220,top:35,donghua:false,
		data:[{
			name:'使用我自己REIM服务端<font color=green>启动</font>',lx:'0'
		}/*,{
			name:'使用官网服务<font color=green>启动</font>(VIP专用)',lx:'1'
		},{
			name:'<font color=red>停止</font>用官网的计划任务',lx:'2'
		}*/],
		itemsclick:function(d, i){
			c.start(d.lx);
		}
	});
	if(ISDEMO)get('randkstrt_{rand}').disabled=true;
});
</script>


<div>


<table width="100%"><tr>
	<td nowrap>
		<button class="btn btn-primary" click="clickwin,0" type="button"><i class="icon-plus"></i> 新增</button> &nbsp; 
		<button class="btn btn-default" click="refresh" type="button"><i class="icon-refresh"></i> 刷新</button> &nbsp; 
		<button class="btn btn-default" click="clearzt" type="button">清空状态</button> &nbsp; 
		<button class="btn btn-success" id="randkstrt_{rand}" type="button"><i class="icon-stop"></i> 启动计划任务 <i class="icon-angle-down"></i></button>
	</td>
	
	
	
	<td width="80%">&nbsp;&nbsp;<a href="javascipt:;" click="openanz">[查看计划任务安装]</a>&nbsp;&nbsp;<a href="javascipt:;" click="openqueue">[计划任务队列]</a>&nbsp;&nbsp;<a href="<?=URLY?>view_taskrun.html"target="_blank">[帮助]</a></td>
	<td align="right" nowrap>
		
		<button class="btn btn-default" id="yun_{rand}" click="yunx" disabled type="button">运行</button> &nbsp; 
		
		<button class="btn btn-danger" id="del_{rand}" click="del" disabled type="button"><i class="icon-trash"></i> 删除</button> &nbsp; 
		<button class="btn btn-info" id="edit_{rand}" click="clickwin,1" disabled type="button"><i class="icon-edit"></i> 编辑 </button>
	</td>
</tr>
</table>
</div>
<div class="blank10"></div>
<div id="veiw_{rand}"></div>
<div class="tishi">提示：执行地址如[sys,beifen]也就是运行webmain/task/runt/sysAction.php文件的beifenAction方法，以此类推。频率d每天,i分钟,w周,m月,y年,h小时</div>
