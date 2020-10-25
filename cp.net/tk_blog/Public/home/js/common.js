$(function(){
	/*模态框垂直居中 START*/
	$('#myModal').on('show.bs.modal', function(e) {
		$(this).find('.modal-dialog').css({
			'margin-top': function() {
				var modalHeight = $('#yourModal').find('.modal-dialog').height();
				return ($(window).height() / 4 - (modalHeight / 4));
			}
		});
	});
	/*模态框垂直居中 END*/

	
	/*登录TAB表格切换*/
	$("#rl-modal-header").find("span").on('click',function(){
		var index = $(this).index();
		$(this).addClass("active-title").siblings().removeClass("active-title");
		$(".send_l").eq(index).show().siblings(".send_l").hide();
	});
	
	$("#comment").on('focus',function(){
		$(this).addClass('focus');
	});
	
	$("#comment").on('focusout',function(){
		$(this).removeClass('focus');
	});


	/**
	 * 处理表情
	 */
	$(".phiz").find("img").on('click',function(){
		var _comment = $("#comment");
		var comment = _comment.val();
		var sign = $(this).attr('alt');
		var str = "[" + sign + "]";
		var content = comment + str;
		_comment.focus();
		_comment.val(content);
	});

	/**
	 * 评论提交处理
	 */
	$("#commentform").on('submit',function(){
		 var datas = $(this).serialize();
		 var content = $("#comment").val();
		 var uid = $("#uids").val();
		 var count_comment = parseInt($("#count-conment").attr('value'));//评论数
		 var show_commentCount = $("#show-commentCount");//显示评论数
		 var newCount = count_comment + 1;
		 var strs = "共 " +  newCount + " 条评论";
		 if (!uid) {
			 layer.msg('请您先登录,再评论.^_^', {
				 icon: 6,
				 time: 2000
			 });
			 return false;
		 }
		 if (!content) {
			 layer.msg('评论内容不能为空.^_^', {
				 icon: 6,
				 time: 2000
			 });
			 return false;
		 }
		 var clearLoad = layer.load(2, {time: 5*1000});
		 $.post(sendCommentUrl,datas,function(datas){
			 if (datas.status) {
				 layer.msg(datas.msg, {
					 icon: 6,
					 time: 2000
				 });
				 if (!datas.level) {
					 //顶级
					 $("#show-content").find(".show-h3").after(datas.data);
				 } else {
					 //多级
					 $(datas.tag).before(datas.data);
				 }
				 //显示评论数
				 show_commentCount.html('');
				 show_commentCount.html(strs);
				 show_commentCount.attr('value',newCount);
				 //清空文本框
				 $("#comment").val('');
				 $("#commentform").fadeOut('slow');
				 layer.close(clearLoad);
			 } else {
				 layer.msg(datas.msg, {icon: 7,time: 2000});
			 }
		 },"json");
		 //阻止表单刷新
		 return false;
	});

	//点击回复评论
	$("#show-content").delegate(".comment-reply-link",'click',function(){
		//清空
		$("#comment").val('');
		var uid = $("#uids").val();
		if (!uid) {
			layer.msg('请您先登录,再评论.^_^', {
				icon: 6,
				time: 2000
			});
			return false;
		}
		$("#commentform").show("slow");
		$("#comment").focus();
		var parentId = $(this).attr('parentId');//父级ID
		var toUid = $(this).attr('toUid');//被评论的ID
		var mid = $(this).attr('comid');//用于追加的内容ID
		var level = $(this).attr('level');//回复层级
		var aid = $(this).attr('aid');//文章ID 用户个人评论中心
		var diff = $(this).attr('diff');//判断是否显示回调“回复”
		var postId = $(this).attr('postID');//上一次评论ID
		if (aid) {
			$("#msgAid").attr('value',aid);
		}
		if (diff) {
			$("#diff").attr('value',diff);
		}
		$("#parentId").attr('value',parentId);
		$("#Mid").attr('value',mid);
		$("#toUid").attr('value',toUid);
		$("#Level").attr('value',level);
		$("#post-id").attr('value',postId);
	});


	/**
	 * 欢迎留言
	 */
	$(".comment-reply-title-cp").on('click',function(){
		var uid = $(this).attr('uid');
		var status = $(this).attr('status');
		if (!uid) {
			layer.msg('请您先登录,再评论.^_^', {
				icon: 6,
				time: 2000 //2秒关闭（如果不配置，默认是3秒）
			});
			$("#commentform").hide();
			return;
		}
		$("#Level").attr('value','0');
		if (status == 1) {
			$("#commentform").show("slow");
			$("#comment").focus();
			$(this).attr('status','0');
			return true;
		} else if (status == 0) {
			$("#commentform").fadeOut("slow");
			$("#comment").focusout();
			$(this).attr('status','1');
			return true;
		}
	});


	//搜索表单提交
	$("#Search-click").on('click',function(){
		$("#global_search_form").submit();
	});

	//关闭
	$(".close").click(function(){
		$(this).parent().fadeOut('slow');
	});

	//点赞
	$("#praise").on('click',function(){
		//var Url = $(this).attr('url');
		var uid = $(this).attr('user');
		var aid = $(this).attr('aid');
		var nums = parseInt($("#praise-nums").html());
		var newNums = nums + 1;
		if (!aid) {
			layer.msg('非法操作.^_^', {
				icon: 6,
				time: 2000
			});
			return false;
		}
		var clearLoad = layer.load(2, {time: 5*1000});
		$.post('/pr',{'uid' : uid,'aid' : aid},function(data){
			if (data.status) {
				layer.close(clearLoad);
				layer.msg(data.msg, {icon: 6,time: 3000},function(){
					$("#praise-nums").html('');
					$("#praise-nums").html(newNums);
				});
			} else {
				layer.close(clearLoad);
				layer.msg(data.msg, {icon: 5,time: 3000});
			}
		},"json");
	});

	//点击刷新验证码
	var src  = $("#code-img").attr('src');
	$("#code-img").click(function(){
		var newSrc = src + '?mt' + Math.random();
		$("#code-img").attr('src',newSrc);
	});

	//用户登录失去焦点事件  邮箱和密码 START
	$("#cp-lg-email-v").on('focusout',function(){
		//邮箱
		var email = $(this).val();
		var _errorM = $("#cp-lg-email");
		_errorM.html('');
		if (!email) {
			_errorM.html('邮箱不能为空.^_^');
			return false;
		} else if (!email.match(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/)) {
			_errorM.html('邮箱格式错误.^_^');
			return false;
		}
	});

	$("#cp-lg-pwd-v").on('focusout',function(){
		//密码
		var pwd = $(this).val();
		var _errorP = $("#cp-lg-pwd");
		_errorP.html('');
		if(!pwd) {
			_errorP.html('密码不能为空.^_^');
			return false;
		}
	});
	//用户登录失去焦点事件  邮箱和密码 end


	//用户注册 失去焦点事件 邮件和验证码 START
	$("#cp-email").on('focusout',function(){
		//邮箱
		var email = $(this).val();
		var _errorM = $("#error-cp-email");
		_errorM.html('');
		if (!email) {
			_errorM.html('邮箱不能为空.^_^');
			return false;
		} else if (!email.match(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/)) {
			_errorM.html('邮箱格式错误.^_^');
			return false;
		}
	});

	$("#cp-code").on('focusout',function(){
		//验证码
		var code = $(this).val();
		var _errorC = $("#error-cp-code");
		_errorC.html('');
		if(!code) {
			_errorC.html('验证码不能为空.^_^');
			return false;
		}
	});
	//用户注册 失去焦点事件 邮件和验证码 END


	//用户注册
    $("#registerForm").on('click',function(){
		var email = $("#cp-email").val();
		var code = $("#cp-code").val();
		var _errorM = $("#error-cp-email");
		var _errorC = $("#error-cp-code");
		_errorM.html('');
		_errorC.html('');
		if (!email) {
			_errorM.html('邮箱不能为空.^_^');
			return false;
		} else if (!email.match(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/)) {
			_errorM.html('邮箱格式错误.^_^');
			return false;
		} else if(!code) {
			_errorC.html('验证码不能为空.^_^');
			return false;
		}
		var clearLoad = layer.load(2, {time: 5*1000});
		$.post('/Register/do_register',{'u_email' : email,'code' : code},function(data){
			if (data.status) {
				layer.close(clearLoad);
				layer.msg(data.msg, {icon: 6,time: 3000},function(){
					window.location.href = data.url;
				});
			} else {
				layer.close(clearLoad);
				layer.msg(data.msg);
			}
			layer.close(clearLoad);
		},"json");
	});




	//用户登录
	$("#loginForm").on('click',function(){
		var email = $("#cp-lg-email-v").val();
		var pwd = $("#cp-lg-pwd-v").val();
		var _errorM = $("#cp-lg-email");
		var _errorP = $("#cp-lg-pwd");
		_errorM.html('');
		_errorP.html('');
		if (!email) {
			_errorM.html('邮箱不能为空.^_^');
			return false;
		} else if (!email.match(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/)) {
			_errorM.html('邮箱格式错误.^_^');
			return false;
		} else if(!pwd) {
			_errorP.html('密码不能为空.^_^');
			return false;
		}
		var clearLoad = layer.load(2, {time: 5*1000});
		$.post('/Register/do_login',{'u_email' : email,'password' : pwd},function(data){
			if (data.status) {
				$("#myModalLabel").modal('hide');
				layer.close(clearLoad);
				layer.msg(data.msg, {icon: 6,time: 3000},function(){
					window.location.href = data.url;
				});
			} else {
				layer.close(clearLoad);
				layer.msg(data.msg);
			}
			layer.close(clearLoad);
		},"json");
	});

	//消息推送回调函数
	var t;
	t =setInterval(function(){
		if (user_id) {
			clearInterval(t);
			get_msg('/getMsg');
		}
	},1000);


	/**
	 * 异步轮询函数
	 */
	function get_msg (url) {
		$(".badge2").removeClass("animated pulse");
		$(".icon-msgs").removeClass("animated pulse");
		$.getJSON(url, function (data) {
			if (data.status) {
				if (data.total) {
					$(".badge2").addClass("animated pulse");
					$(".icon-msgs").addClass("animated pulse");
					$("#getMSG").html(data.total);
					$("#getMSG2").html(data.total);
				} else {
					$("#getMSG").html("");
					$("#getMSG2").html("");
				}
			} else {
				$(".badge2").addClass("animated pulse");
				$(".icon-msgs").addClass("animated pulse");
			}
			setTimeout(function () {
				get_msg(url);
			}, 5000);
		});
	}

	//点击消息跳转到指定消息页
	$(".icon-msgs,.badge2").on('click',function(){
		window.location.href = '/msg';
	});

	//右侧回到顶部
	$(window).scroll(function(){
		var top = $(this).scrollTop();
		//document.title = top;打印数据到DOM标题
		if (top > 130) {
			$("#back-to-top").fadeIn(500);
		}else{
			$("#back-to-top").fadeOut(500);
		}
	});

	$("#back-to-top").on('click',function(){
		$("html,body").stop().animate({
			scrollTop : 0
		},500);
	});

});
	
