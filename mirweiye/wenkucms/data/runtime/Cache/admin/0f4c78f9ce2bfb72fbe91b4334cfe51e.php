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
<fieldset class="layui-elem-field layui-field-title" style="margin-top:0px;">
    <legend><?php echo (getmenuname($menuid)); ?></legend>
</fieldset>
<form class="layui-form layui-form-pane" action="">
    <input type="hidden" name="menuid"  value="<?php echo ($menuid); ?>"/>
    <div class="layui-form-item">
        <label class="layui-form-label"><?php echo L('site_name');?></label>
        <div class="layui-input-block">
            <input type="text" name="setting[site_name]" placeholder="请输入网站名称" class="layui-input" value="<?php echo C('wkcms_site_name');?>">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">网站链接</label>
        <div class="layui-input-inline">
            <input type="text" name="setting[site_url]" placeholder="请输入网站链接" class="layui-input" value="<?php echo C('wkcms_site_url');?>">
        </div>
        <div class="layui-form-mid layui-word-aux">输入完整链接，必须加上http:// 并以 “<span style="color: #FF5722"> / </span>”结尾。  <span style="color: #FF5722">否则会导致图片不显示等问题</span></div>
    </div>
   
    <div class="layui-form-item">
        <label class="layui-form-label">网站备案</label>
        <div class="layui-input-block">
            <input type="text" name="setting[site_icp]" placeholder="请输入备案号" class="layui-input" value="<?php echo C('wkcms_site_icp');?>">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><?php echo L('score_name');?></label>
        <div class="layui-input-block">
            <input type="text" name="setting[score_name]" placeholder="请输入积分名称" class="layui-input" value="<?php echo C('wkcms_score_name');?>">
        </div>
    </div>
    <div class="layui-form-item" pane="">
        <label class="layui-form-label">允许充值</label>
        <div class="layui-input-block">
       <input type="radio" title="允许" <?php if(C('wkcms_score_pay.isscore') == '1'): ?>checked="checked"<?php endif; ?> value="1" name="setting[score_pay][isscore]">
       <input type="radio" title="不允许" <?php if(C('wkcms_score_pay.isscore') == '0'): ?>checked="checked"<?php endif; ?> value="0" name="setting[score_pay][isscore]">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">充值比例</label>
        <div class="layui-input-inline">
 <input type="number" name="setting[score_pay][getscore]" lay-verify="required" placeholder="请输入充值比例" class="layui-input" value="<?php echo C('wkcms_score_pay.getscore');?>">
        </div>
        <div class="layui-form-mid layui-word-aux">例如：填写10则为1元人民币兑换10个积分</div>
    </div>
 
    <div class="layui-form-item">
        <label class="layui-form-label">广告时间</label>
        <div class="layui-input-inline">
        <input type="number" name="setting[score_pay][adtime]" lay-verify="required" placeholder="请输入广告时间" class="layui-input" value="<?php echo C('wkcms_score_pay.adtime');?>">
        </div>
        <div class="layui-form-mid layui-word-aux">文档预览时的广告显示时间，单位为：秒！</div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">客服QQ</label>
        <div class="layui-input-inline">
        <input type="text" name="setting[site_qq]" placeholder="请输入联系人QQ" class="layui-input" value="<?php echo C('wkcms_site_qq');?>">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">客服电话</label>
        <div class="layui-input-inline">
        <input type="text" name="setting[site_tel]" placeholder="请输入联系人QQ" class="layui-input" value="<?php echo C('wkcms_site_tel');?>">
        </div>
    </div>

   
    <div class="layui-form-item">
        <label class="layui-form-label"><?php echo L('statistics_code');?></label>
        <div class="layui-input-block">
            <textarea placeholder="请输入网站统计代码" name="setting[statistics_code]" class="layui-textarea"><?php echo C('wkcms_statistics_code');?></textarea>
        </div>
    </div>
    <div class="layui-form-item" pane="">
        <label class="layui-form-label"><?php echo L('site_status');?></label>
        <div class="layui-input-block">
       <input type="radio" title="<?php echo L('open');?>" <?php if(C('wkcms_site_status') == '1'): ?>checked="checked"<?php endif; ?> value="1" name="setting[site_status]">
       <input type="radio" title="<?php echo L('close');?>" <?php if(C('wkcms_site_status') == '0'): ?>checked="checked"<?php endif; ?> value="0" name="setting[site_status]">
        </div>
    </div>
    <div <?php if(C('wkcms_site_status') == 1): ?>class="layui-hide"</else>class="layui-form-item"<?php endif; ?>>
        <label class="layui-form-label"><?php echo L('closed_reason');?></label>
        <div class="layui-input-block">
            <textarea placeholder="请输入网站关闭原因" name="setting[closed_reason]" id="closed_reason" class="layui-textarea"><?php echo C('wkcms_closed_reason');?></textarea>
        </div>
    </div>
    <div class="layui-form-item">
        <label class=""></label>
        <div class="layui-input-block">
        <button class="layui-btn" lay-submit="" lay-filter="upbtn">提交保存</button>
        </div>
    </div>
</form>

<script type="text/javascript" src="__PUBLIC__/admin/layui/layui.js"></script>
<script>
//Demo
layui.use(['layer','jquery','form'], function () {
   // 操作对象
    var layer = layui.layer,$ = layui.jquery,form = layui.form;
  
  // 提交
    form.on('submit(upbtn)', function (data) {
        // 提交到方法 默认为本身
        $.post("<?php echo u('global/edit');?>",data.field,function(res){
            if(res.status.status ==  1){
                layer.msg(res.status.info,{time:1800},function(){
                  location.href = "<?php echo u('global/index');?>&menuid=<?php echo ($menuid); ?>";
                });
            }else{
                layer.msg(res.status.info,{time:1800});
                // $('.verify_img').click();
            }
        });
        return false;
    });
});
</script>
</body>
</html>