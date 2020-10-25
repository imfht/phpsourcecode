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
 <blockquote class="layui-elem-quote">转换设置说明：<br/> 
七只熊云转换：七只熊在线云转换服务，获取appid、appsecret，请联系七只熊客服：<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&amp;uin=&amp;site=qq&amp;menu=yes" style="color: #1E9FFF">996403</a> 交流群：<a target="_blank" href="//shang.qq.com/wpa/qunwpa?idkey=4ca374dc4da225a81f2af37c6bc607487bc907ae2ae6306159f10867fa10510d" style="color: #1E9FFF"> 633871890 </a>
<br>
百度DOC转换：自行申请百度DOC转换服务，申请网址：
<a href="https://cloud.baidu.com/product/doc.html" target="_blank">https://cloud.baidu.com/product/doc.html</a>
</blockquote>
<form class="layui-form layui-form-pane" action="">
    <input type="hidden" name="menuid"  value="<?php echo ($menuid); ?>"/>
    <div class="layui-form-item" pane="">
        <label class="layui-form-label">转换工具</label>
        <div class="layui-input-block">
       <input type="radio" title="七只熊云转换" <?php if(C('wkcms_convert_type') != '2'): ?>checked="checked"<?php endif; ?> value="1" name="setting[convert_type]">
       <input type="radio" title="百度DOC转换" <?php if(C('wkcms_convert_type') == '2'): ?>checked="checked"<?php endif; ?> value="2" name="setting[convert_type]">
        </div>
    </div>
 

	<div class="layui-form-item convert_type type_1" <?php if(C('wkcms_convert_type') != '1'): ?>style="display:none;"<?php endif; ?>>
        <label class="layui-form-label">云转换地址</label>
        <div class="layui-input-block">
            <input type="text" name="setting[convert_site_1]" placeholder="请输入云转换地址" class="layui-input" value="<?php echo C('wkcms_convert_site_1');?>" disabled="">
        </div>
    </div>

    <div class="layui-form-item convert_type type_1" <?php if(C('wkcms_convert_type') != '1'): ?>style="display:none;"<?php endif; ?>>
        <label class="layui-form-label">appid</label>
        <div class="layui-input-block">
            <input type="text" name="setting[convert_appid_1]" placeholder="请输入Appid" class="layui-input" value="<?php echo C('wkcms_convert_appid_1');?>">
        </div>
    </div>

    <div class="layui-form-item convert_type type_1" <?php if(C('wkcms_convert_type') != '1'): ?>style="display:none;"<?php endif; ?>>
        <label class="layui-form-label">appsecret</label>
        <div class="layui-input-block">
             <textarea placeholder="请输入Appsecret" name="setting[convert_appsecret_1]" class="layui-textarea"><?php echo C('wkcms_convert_appsecret_1');?></textarea>
        </div>
        
    </div>

    <!-- 百度DOC使用说明 -->
    <div class="layui-form-item convert_type type_2" <?php if(C('wkcms_convert_type') != '2'): ?>style="display:none;"<?php endif; ?>>
        <div class="layui-input-block">
            <blockquote class="layui-elem-quote layui-quote-nm">百度DOC转换说明：<br/> 
        七只熊也可以使用百度的文档转换服务，使用百度DOC和使用七只熊效果一样。但他们价格较贵。详情参见：<a href="https://cloud.baidu.com/product/doc.html" target="_blank">https://cloud.baidu.com/product/doc.html</a><br/> 
        开启百度DOC转换服务，请联系七只熊客服：<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&amp;uin=&amp;site=qq&amp;menu=yes" style="color: #1E9FFF">996403</a> 交流群：<a target="_blank" href="//shang.qq.com/wpa/qunwpa?idkey=4ca374dc4da225a81f2af37c6bc607487bc907ae2ae6306159f10867fa10510d" style="color: #1E9FFF"> 633871890 </a>
        <br>
        百度DOC转换：自行申请百度DOC转换服务，申请网址：
        <a href="https://cloud.baidu.com/product/doc.html" target="_blank">https://cloud.baidu.com/product/doc.html</a>
        </blockquote>
        </div>
    </div>
    
    <!-- 隐藏百度转换设置 -->
    <!-- <div class="layui-form-item convert_type type_2" <?php if(C('wkcms_convert_type') != '2'): ?>style="display:none;"<?php endif; ?>>
        <label class="layui-form-label">appid</label>
        <div class="layui-input-block">
            <input type="text" name="setting[convert_appid_2]" placeholder="请输入Appid" class="layui-input" value="<?php echo C('wkcms_convert_appid_2');?>">
        </div>
    </div>

    <div class="layui-form-item convert_type type_2" <?php if(C('wkcms_convert_type') != '2'): ?>style="display:none;"<?php endif; ?>>
        <label class="layui-form-label">appsecret</label>
        <div class="layui-input-block">
            <textarea placeholder="请输入Appsecret" name="setting[convert_appsecret_2]" class="layui-textarea"><?php echo C('wkcms_convert_appsecret_2');?></textarea>
        </div>
     --></div>

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
  
    form.on('radio', function (data) {
        $(".convert_type").hide();
        $(".type_" + data.value).show();
    });
  // 提交
    form.on('submit(upbtn)', function (data) {
        // 提交到方法 默认为本身
        $.post("<?php echo u('global/edit');?>",data.field,function(res){
            if(res.status.status ==  1){
                layer.msg(res.status.info,{time:1800},function(){
                  location.href = "<?php echo u('global/index');?>&type=convert&menuid=<?php echo ($menuid); ?>";
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