/**
 * CheckBox复选框标签组件
 * 
 * @author 牧羊人
 * @date 2018-11-23
 */
layui.define('jquery', function(exports){
	"use strict";
	var $ = layui.$
	,hint = layui.hint();
	var CheckBox = function(options){
		this.options = options;
	};
	
	/**
	 * 初始化组件
	 */
	CheckBox.prototype.init = function(elem){
		var that = this;
		elem.addClass('checkBox'); //添加checkBox样式
		that.checkbox(elem);
	};
	
	/**
	 * 节点解析
	 */
	CheckBox.prototype.checkbox = function(elem,children){
		var that = this, options = that.options;
		var nodes = children || options.nodes;
		layui.each(nodes, function(index, item){
			var li = $(['<li class="block'+(item.on?' on':'')+'" value="'+item.elemId+'[]" onmouseover="layui.layer.tips(\''+item.id+'\',this,{tips:2})" onmouseout="layui.layer.closeAll(\'tips\');">'+item.name,'<i class="choice"><i class="triangle"></i><i class="right layui-icon layui-icon-ok"></i></i><!--<i class="del"><i class="layui-icon layui-icon-delete"></i></i><span class="hide">-->'+(item.on?'<input type="hidden" name="'+item.elemId+'[]" value="'+item.id+'">':'')+'</span></li>'].join(''));
			elem.append(li);
			//触发点击节点回调
			typeof options.click === 'function' && that.click(li, item);
			//触发删除节点回调
			typeof options.del === 'function' && that.del(li, item);
		});
	};
	
	/**
	 * 点击节点回调
	 */
	CheckBox.prototype.click = function(elem, item){
		var that = this, options = that.options;
		elem.on('click', function(e){
			elem.toggleClass("on");
			if(elem.hasClass("on")){
				item.on = true;
				elem.children("span.hide").html('<input type="hidden" name="'+item.elemId+'[]" value="'+item.id+'">');
			}else{
				item.on = false;
				elem.children("span.hide").html('');
			}
			layui.stope(e);
			options.click(item);
		});
	};
	
	/**
	 * 点击删除节点回调
	 */
	CheckBox.prototype.del = function(elem, item){
		var that = this, options = that.options;
		elem.children('i.del').on('click', function(e){
			var index = layer.confirm('确定删除 ['+item.name+']标签吗？', {
				btn: ['删除','取消']
			}, function(){
				layer.close(index);
				if(options.del(item)){
					elem.closest(".block").remove();
					layui.stope(e);
				}
			});
			return false;
		});
	};

	/**
	 * 模块接口输出
	 */
	exports('checkbox', function(options){
		var checkbox = new CheckBox(options = options || {});
		var elem = $(options.elem);
		if(!elem[0]){
			return hint.error('layui.checkbox 没有找到'+ options.elem +'元素');
		}
		checkbox.init(elem);
	});
	
}).link('checkbox.css','checkboxcss');
