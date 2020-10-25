<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title><?php echo L('website_manage');?></title>
	<link rel="stylesheet" href="__PUBLIC__/admin/static/css/style.css">
	<link rel="stylesheet" href="__PUBLIC__/admin/layui/css/layui.css">
	<link rel="icon" href="__PUBLIC__/admin/static/image/code.png">
</head>
<style type="text/css">
	.layui-laypage li {float:left !important;}
</style>
<script>

var Oper = {
	submit_yes : true,
	layui : false,
	layer : false,
	jq : false,
	form : false,
	element : false,
	table : false,
	key : 'id',
	//一些按钮的事件绑定
	config : {
		//开关
		'switch' : 'switch(status)',
		//添加
		'add' : {
			'element' : '#btn-add',
			'name' : '添加',
			'width' : '600px',
			'height' : '460px',
			'method' : 'add',
		},
		//编辑
		'edit' : {
			'element' : 'tbody#userList tr td #edit',
			'width' : '600px',
			'height' : '520px',
		},
		//删除
		'delete' : {
			'element' : 'tbody#userList tr td #delete',
			'all' : '#btn-alldel',
			'method' : 'delete',
		},
	},
	set : function(layui, name, module, key) {
		var self = this;
		self.layui = layui;
		self.layer = layui.layer;
		self.jq = layui.jquery;
		self.form = layui.form;
		self.element = layui.element;
		self.table = layui.table;
		self.name = name;
		self.module = module;
		self.checked = [];
		self.key = key;
	}

	//列表页
	,initList : function(layui, name, module, key) {
		var self = this;
		self.set(layui, name, module, key);

		self.jq(function() {
			self.switch();
			self.add();
			self.edit();
			self.delete();
			self.checkall();
		});
	}

	//更新页 暂未实现
	,initUpdate : function(layui, name, form_id, update_url, list_url, editor, tag) {
		var self = this;
		self.set(layui, name, false, false);
		self.jq(function() {
			self.tag(tag);
			self.editor(editor);
			self.submit(form_id, update_url, list_url, editor);
		});
	}

	,remove : function(a, v) {
		for (var i = 0; i < a.length; i++) {
			if (a[i] == v) a.splice(i, 1);
		}
		return -1;
	}

	,msg : function(msg, option, func) {
		if (!option) {
			option = {
				time:1800
			};
		}
		if (func == 'reload') {
			func = function() {
				location.reload();
			}
		} else {
			func = false;
		}
		this.layer.msg(msg, option, func);
	}

	,switch : function() {
		var self = this;
		self.form.on(self.config.switch, function(data) {
			var url = self.jq(this).attr('data-uri');
			var val = '';
			if (data.elem.checked == true) {
				val = 1;
			} else {
				val = 0;
			}

			self.jq.post(url + "&val=" + val, data.field, function(res) {
				if (res.status ==  1) {
					self.msg('更新成功');
				} else {
					self.msg('更新失败');
				}
			});
		});
	}

	//初始化标签
	,tag : function(tag) {
		//自动获取标签
		if (!tag) {
			return;
		}
		var self = this;
		self.jq(tag).on('click', function() {
			var title = self.jq.trim(self.jq('#J_title').val());
			if(title == ''){
				layer.msg('请先填写标题',{time:1800});
				return false;
			}
			self.jq.getJSON('<?php echo U("article/ajax_gettags");?>', {title:title}, function(result){
				if(result.status == 1){
					self.jq('#tpt_input').val(result.data);
				}else{
					self.layer.msg('获取失败',{time:1800});
				}
			});
		});
	}

	//初始化编辑器
	,editor : function(editor)
	{
		if (!editor) {
			return;
		}
		var self = this;
		self.editors = {};
		for (var i in editor) {
			//编辑器图片上传
		    self.layui.layedit.set({
		        uploadImage: {
		            url: '<?php echo U("attachment/editer_upload");?>',
		            type: 'post'
		        }
		    });
		  	//构建一个默认的编辑器
		    self.editors[editor[i]] = self.layui.layedit.build(editor[i]);
		}
	}

	//提交按钮
	,submit : function(form_id, update_url, list_url, editor) {
		var self = this;
		var loading = false;

		self.layui.form.on('submit('+form_id+')', function(data){
			if (self.submit_yes == false) {
				return false;
			}
			self.submit_yes = false;
			if (list_url) {
		    	loading = self.layer.load(2, {shade: [0.2,'#000']});
		    }
		    if (editor) {
		    	for (var i in editor) {
		    		data.field[editor[i]] = self.layui.layedit.getContent(self.editors[editor[i]]);
		    	}
		    }
		    var url = "<?php echo U('module/method');?>";
		    var temp = update_url.split('/');
			var post_url = url.replace('module', temp[0]);
			post_url = post_url.replace('method', temp[1]);
		    self.jq.post(post_url,data.field,function(data){
		        if(data.status ==  1){
		        	if (list_url) {
		        		if (loading) {
		        			self.layer.close(loading);
		        		}
			            self.layer.msg(data.msg,{time:1800},function(){
			            	var temp = list_url.split('/');
			            	list_url = url.replace('module', temp[0]);
							list_url = list_url.replace('method', temp[1]);
							self.submit_yes = true;
			            	location.href = list_url;
			            });
		        	} else {
		        		self.layer.msg(data.msg,{time:1800},function(){
		        			self.submit_yes = true;
		                    //关闭弹层后刷新父页面
		                    window.parent.location.reload();
		                    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引  
		                    parent.layer.close(index); 
		                });
		        	}
		        }else{
		        	self.submit_yes = true;
		        	if (list_url) {
			            self.layer.msg(data.msg,{time:1800});
			            if (loading) {
		        			self.layer.close(loading);
		        		}
			            return false;
			        } else {
			        	self.layer.msg(data.msg,{time:1800});
			        	return false;
			        }
		        }
		    });
		    return false;
		});
	}

	//添加
	,add : function() {
		var self = this;
		this.jq(self.config.add.element).click(function() {
			 var url = "<?php echo U('module/method');?>";
			 url = url.replace('module', self.module);
			 url = url.replace('method', self.config.add.method);
			 self.layer.open({
				type: 2,
				title: self.config.add.name + self.name,
				area: [self.config.add.width, self.config.add.height],
				fixed: true,
				maxmin: true,
				content: url
			});
		});
	}

	//编辑
	,edit : function() {
		var self = this;
		self.jq(self.config.edit.element).click(function() {
			var title   = self.jq(this).attr('data-title');
			var url   = self.jq(this).attr('data-uri');
			 self.layer.open({
				type: 2,
				title: title,
				area: [self.config.edit.width, self.config.edit.height],
				fixed: true,
				maxmin: true,
				content: url
			});
		});
	}


	//全选
	,checkall : function() {
		var self = this;
		self.form.on('checkbox(allChoose)', function(data) {  
			var child = self.jq(data.elem).parents('table').find('.doc_checkbox');  
			
			child.each(function(index, item) {  
				item.checked = data.elem.checked;
				if (item.checked) {
				  self.checked.push(self.jq(this).val());
				} else {
				  self.remove(self.checked, self.jq(this).val());
				}
			});
			self.form.render('checkbox'); 
		});
		self.form.on('checkbox(itemChoose)',function(data) {
			if (data.elem.checked) {
				self.checked.push(self.jq(this).val());
			} else {
				self.remove(self.checked, self.jq(this).val());
			}
		}); 
	}

	//删除
	,delete : function() {
		var self = this;
		self.jq(self.config.delete.element).click(function(data) {
			var id  = self.jq(this).attr('data-id');
			var url = self.jq(this).attr('data-uri');
			self.deleteAction(url);
		});

		self.deleteAll();
	}

	//批量删除
	,deleteAll : function() {
		var self = this;
		if (this.jq(self.config.delete.all).length) {
			this.jq(self.config.delete.all).click(function(data) {
				if (!self.checked || self.checked.length <= 0) {
					self.msg('请选择需要删除的数据');
				} else {
					var url = "<?php echo U('module/method', array('me' => 1));?>";
					url = url.replace('module', self.module);
					url = url.replace('method', self.config.delete.method);
					var url = url + "&"+self.key+"=" + self.checked.join(',');
					self.deleteAction(url);
				}
			});
		}
	}

	//确认删除
	,deleteAction : function(url, param) {
		if (!param) {
			param = {};
		}
		var self = this;
		self.layer.confirm('删除'+self.name+'将不能恢复，确认删除吗？'
			, {btn: ['确认','取消']}
			, function() {
				self.jq.post(url, param, function(res) {
					if(res.status ==  1) {
						self.msg(res.msg, false, 'reload');
					} else {
						self.msg(res.msg);
						return false;
					}
				});
			}
			, function() {
				self.msg('取消删除');  
				return false;
			}
		);
		return false; 
	}
};
</script>
<!--网站设置-->
<body class="body">
<style type="text/css">
    .red {color: #FF5722!important;}
    .green {color: #5FB878!important;}
</style>
<fieldset class="layui-elem-field layui-field-title" style="margin-top:0px;">
    <legend><?php echo (getmenuname($menuid)); ?></legend>
</fieldset>
     <div class="my-btn-box">
     <span class="fr">
       <a class="layui-btn layui-btn-normal" id="btn-add">添加导航</a>
     </span>
    </div> 
 
<form class="layui-form layui-form-pane" action="">
    <table class="layui-table">
        <colgroup>
          <col width="60">
            <col width="150">
            <col width="150">
            <col width="">
            <col width="120">
            <col width="120">
            
            <col width="120">
            <col width="120">
            <col>
        </colgroup>
        <thead>
        <tr>
            <th>ID</th>
            <th><?php echo L('nav_name');?></th>
            <th><?php echo L('alias');?></th>
            <th><?php echo L('nav_link');?></th>
            <th>导航位置</th>
            <th><?php echo L('sort_order');?></th>
             
            <th><?php echo L('enabled');?></th>
            <th><?php echo L('operations_manage');?></th>
        </tr>
        </thead>
         <tbody id="userList">
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr>
                      <td><span><?php echo ($val["id"]); ?></span></td>
                      <td><span><?php echo ($val["name"]); ?></span></td>
                      <td><span><?php echo ($val["alias"]); ?></span></td>
                      <td><span><?php echo ($val["link"]); ?></span></td>
                      <td><span><?php if($val["type"] == 'main'): ?>主导航<?php else: ?>底部导航<?php endif; ?></span></td>
                      <td><span><?php echo ($val["ordid"]); ?></span></td>
                       
                      <td>
                        <input type="checkbox" name="status" value="<?php echo ($val["status"]); ?>" data-uri="<?php echo U('nav/ajax_edit',array('id'=>$val['id'],'field'=>'status'));?>" lay-skin="switch" lay-filter="status" lay-text="启用|禁止" <?php if($val["status"] == 1): ?>checked<?php endif; ?>>
                      </td>
                      <td><a href="javascript:;" id="edit" data-uri="<?php echo U('nav/edit', array('id'=>$val['id']));?>" data-title="<?php echo L('edit');?> - <?php echo ($val["name"]); ?>"  data-id="edit" data-id="<?php echo ($val["id"]); ?>"><?php echo L('edit');?></a> | <a href="javascript:;"  id="delete" data-uri="<?php echo U('nav/delete', array('id'=>$val['id']));?>" data-id="<?php echo ($val["id"]); ?>"><?php echo L('delete');?></a></td>
                  </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>

    </table>
</form>
<div id="test-laypage-demo0">
  <div class="layui-box layui-laypage layui-laypage-default" id="layui-laypage-29">
  <?php echo ($page); ?>
  </div>
</div>
<script type="text/javascript" src="__PUBLIC__/admin/layui/layui.js"></script>
<script>
layui.use(['layer','jquery','form','element','table'], function () {
    //参数：layui、名称、模块名、主键（删除用）
    //新增弹窗
    Oper.config.add.width = '600px';
    Oper.config.add.height = '510px';

    //编辑弹窗
    Oper.config.edit.width = '600px';
    Oper.config.edit.height = '510px';
    Oper.initList(layui, '导航', 'nav', 'id');
});
</script>
</body>
</html>