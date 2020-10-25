var s = {
	_dom: $(document),
	alert : function($message) {
		// 打开提示dialog
		bootbox.alert($message);
	},
	del : function(o) {
		// 绑定删除事件
		var $url = o.data('url');
		if ($url == '' || $url == undefined)
			return;
		var $message = o.data('message') || '您确定要删除这条记录吗?';
		bootbox.confirm($message, function(result) {
			if (result == true) {
				$.getJSON($url, {
					isajax : 1
				}, function(json) {
					if (json.status == true) {
						// 删除成功
						$(o).parents('tr').remove();
					} else {
						// 执行失败,提示信息
						bootbox.alert(json.message);
					}
				});
			}
		});
	},
	todo : function(o) {
		s.del(o);
	},
	dialog : function(o) {
		var $type = o.data('type') || 'view';// 类型 form/view
		var $title = o.data('title') || '提示';
		var $url = o.data('url') || false;
		var $backfrop = o.data('backfrop') || true;
		var $closeButton = o.data('closeButton') || true;
		var $message = o.data('message') || '没有设置内容';
		var $form = o.data('form') || 'bootbox_form';
		var $dialog_class = o.data('class') || 'my-modal';

		if ($message.substr(0, 1) == '#') {
			// 已存在的表单dom
			$message = $($message).html();
		} else if ($url != false) {
			// 从URL ajax获取
			$.ajax({
				type : "get",
				url : $url,
				data : {},
				async : false,
				success : function(data) {
					$message = data
					// 是否有form
					var _ = $(data);
					if ($(_).find('form').length > 0) {
						// 只支持一个
						$form = $(_).find('form').attr('id');
					}
				}
			});
		}
		bootbox.dialog({
			// dialog的内容
			message : $message,
			// dialog的标题
			title : $title,
			// 退出dialog时的回调函数，包括用户使用ESC键及点击关闭
			onEscape : function() {
			},
			// 是否显示此dialog，默认true
			show : true,
			// 是否显示body的遮罩，默认true
			backdrop : $backfrop,
			// 是否显示关闭按钮，默认true
			closeButton : $closeButton,
			// 是否动画弹出dialog，IE10以下版本不支持
			animate : true,
			// dialog的类名
			className : $dialog_class,
			// dialog底端按钮配置
			buttons : {
				// 其中一个按钮配置
				success : {
					// 按钮显示的名称
					label : "确定",
					// 按钮的类名
					className : "btn-success",
					// 点击按钮时的回调函数
					callback : function() {
						if ($type == 'form')
							return s.bootbox_form($form);

						if ($type == 'view')
							return s.bootbox_view();
					}
				}
			}
		});
	},
	bootbox_form : function($form) {
		var form = $("#" + $form);
		// 回调
		var success_callback = form.data('success_callback') || false;
		var fail_callback = form.data('fail_callback') || false;
		if (form.length < 1)
			return;
		// 参数校验
		var params = form.serialize();
		$.ajax({
			type : "post",
			dataType : "json",
			url : form.attr('action'),
			data : params + '&isajax=1',
			success : function(json) {
				if (json.status == true) {
					// 添加成功
					redirect_url = json.data.redirect_url;
					if(redirect_url){
						var pageid = redirect_url.replace(/\//gi,'_');
						s.loadPageContent('',redirect_url,pageid);
					}
				} else {
					// 失败
					bootbox.alert(json.message);
				}
			},
			error : function() {
				bootbox.alert("数据提交错误");
			}
		});
	},
	bootbox_view : function() {
		return true;
	},
	menu_selected : function(c, a) {
		var $on_uri = '#_admin_' + c + '_' + a;
		// $on_uri = "#<{$controller}>_<{$action}>";
		$('.nav-list li').removeClass('active');
		$($on_uri).addClass('active');
		$($on_uri).parent().parent().addClass('active');
		$($on_uri).parent().parent().addClass('open');
	},
	submit : function(f) {
		var $url = f.attr('action');
		var $method = f.attr('method') || 'POST';
		if ($url == '' || $url == undefined) {
			bootbox.alert('表单没有提交地址!');
			return false;
		}
		// 参数校验
		var params = f.serialize();
		$.ajax({
			type : $method,
			url : $url,
			data : params + '&isajax=1',
			async : false,
			dataType : 'JSON',
			success : function(json) {
				if (json.status == true) {
					// 添加成功
					redirect_url = json.data.redirect_url;
					if(redirect_url){
						var pageid = redirect_url.replace(/\//gi,'_');
						s.loadPageContent('',redirect_url,pageid);
					}
				} else {
					// 失败
					bootbox.alert(json.message);
				}
			}
		});
		return false;
	},
	initDatePicker : function() {
		// 日期选择器
		$('.date-picker').datetimepicker({language:  'zh-CN',autoclose: 1,startDate: "2015-02-14 10:00"});
	},
	create_load:function(){
		$('#screen').width($(document).width());
		$('#screen').height($(document).height());
		$('#load_html_box').css({
			top: $(document).height() / 2  - 90,
			left: $(document).width() / 2 - 120
		});
		$('#screen').show();
	},
	close_load:function(){
		$('#screen').hide();
	},
	loadPageContent:function(title,url,pageid){
		// load html
		pageid = pageid.toLowerCase();
		$.ajax({
			url: url,
			context: $('.page-content'),
			data:{},
			dataType: 'html',
			error:function(data,e){
				console.log('有错误'+e)	
				s.close_load();	
			},
			success:function(data){
				// 查询之前是否含有
				//$('.main_target').hide();
				if($('#'+pageid).length > 0){
					// 存在	
					var html = $('#'+pageid);
					$(html).html(data)
				}else{
					// 不存在
					// 添加导航
					$('<li> <a data-toggle="tab" href="#'+ pageid +'" id="nav_'+ pageid +'" data-url="'+ url +'"> '+ title +' </a><span class="badge badge-info page-nav hide">x</span></li>').appendTo($('#pageNavs'));
					// 让其他的隐藏
					var html = $('<div class="main_target tab-pane in"></div>')
					$(html).attr('id',pageid);
					$(html).html(data)
					$(html).appendTo(this)
				}
				// 隐藏所有
				$('.main_target').removeClass('active');
				$('#pageNavs li').removeClass('active');
				// 显示当前
				$('#pageNavs a').each(function(){
					var ele =  $(this).attr('href');
					if(ele == '#'+pageid){
						$(this).parent().addClass('active');
					}
				});
				$('#'+pageid).addClass('active');
				s.close_load();	
				// 重新绑定事件
				s.publicInit();
			},
			beforeSend:function(){
				s.create_load();	
			}
		});
		// 关闭按钮
		this._dom.on('mouseover','#pageNavs li',function(){
			$(this).find('.page-nav').removeClass('hide');
		})	
		this._dom.on('mouseout','#pageNavs li',function(){
			$(this).find('.page-nav').addClass('hide');
		})
		// 关闭事件
		this._dom.on('click','.page-nav',function(){
			var delEle = $(this).siblings('a').attr('href');
			var isactive = $(this).parent().hasClass('active');
			if(delEle == '#mainIndex' && isactive == true) return false;
			// 移除内容
			$(delEle).remove();
			$(this).parent().remove();
			if(isactive == true) {
				// 删除页面
				var _ = $('#pageNavs li').eq(-1); 
				_.addClass('active');
				$(_.find('a').eq(0).attr('href')).addClass('active');
				$(_.find('a').eq(0).attr('href')).removeClass('fade');
			}
		})
	},
	// 页面加载时
	publicInit:function(){
		// 删除
		$(".delete").off("click"); 
		$('.delete').on('click', function() {
			// 发起ajax请求
			s.del($(this));
		})
		// 打开dialog
		$(".open_dialog").off("click"); 
		$('.open_dialog').on('click', function() {
			s.dialog($(this));
		});
		// 确认提交
		$('.form_ajax').submit(function() {
			return s.submit($(this));
		});
		s.initPageBind();
	},
	initPageBind:function(){
		// ajax 获取网页
		$(".page_btn").off("click"); 
		$('.page_btn').on('click',function(){
			var t = $(this);
			var url = t.data('url');
			pageid = t.data('pageid').toLowerCase();
			title = t.data('navname');
			if(pageid == 'this'){
				if(t.parents('.main_target').length > 0){
					// 查找到	
					pageid = t.parents('.main_target').attr('id');
					title =$('#nav_'+pageid).html();
					$('#'+pageid).remove();
					$('#nav_'+pageid).remove();
				}else{
					// 没找到
					alert('page id config error');
					return;	
				}
			}
			s.loadPageContent(title,url,pageid);
			// create html 
			return false;
		});
	}
}
$(function() {
	s._dom.on('click','a',function(){
		if($(this).attr('href') == '#') return false;	
	})
	s.publicInit();
})
