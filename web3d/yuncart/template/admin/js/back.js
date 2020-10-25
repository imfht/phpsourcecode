(function($){
	$.page = {
		set:function() {
			this._form = $("#seaform");
			this._page = $("#page");
			this._do   = $("#do");
		},
		topage:function(page){
			this.set();
			this._page.val(page);
			this.submit();
		},
		next:function(){
			this.set();
			this._page.val(parseInt(this._page.val())+1);
			this.submit();
		},
		prev:function(){
			this.set();
			this._page.val(parseInt(this._page.val())-1);
			this.submit();
		},
		input:function(){
			this.set();
			this._page.val($("#inputpage").val());
			this.submit();
		},
		submit:function() {
			this._do.val('');
			this._form.submit();
		},
		reset:function(){
			this.set();
			this._page.val(1);
		}
	}
	$.ajaxpage = {
		next:function() {
			var page = parseInt($("#curpage").val()) || 1;
			$.ajaxpage.topage(page + 1);
		},
		prev:function() {
			var page = parseInt($("#curpage").val()) || 2;
			$.ajaxpage.topage(page - 1);
		},
		topage:function(page) {
			var $form = $("#seaform"),url = $form.attr("action");
			$.get(url,{page:page,inajax:true,q:$("#q").val()},function(data) {
				if(data == "error") {
					jAlert("发生错误,请刷新页面后重新执行该操作","警告");
				} else {
					$("#selectitem").html(data);
				}
			});
		}
	}
})(jQuery);

function jssubmit(type,opertype,ispop) {
	var msg			= new Array();
	(function() {
		switch(type) {
			case "item":
				var typeid		= parseInt($("#typeid").val()) || 0,
					itemname	= $.trim($("#itemname").val()),
					price		= $.trim($("#price").val()),
					inventory   = $.trim($("#inventory").val()),
					img			= $(".forimg").find("img").length;

					if(!typeid)		{
						msg.push("请选择商品类型！");
					}
					if(!itemname)	{
						msg.push("请填写商品名称！");
					}
					if(!price)		{
						msg.push("请填写商品价格！");
					}
					if(!inventory)	{
						msg.push("请填写商品库存！");
					}
					if(!img) {
						msg.push("请上传商品图片！");
					}
				break;
			case "tag":
				var tagname = $.trim($('#tagname').val());
				if(!tagname) {
					msg.push("请填写标签名称！");
				}
				break;
			case "cat":
				var catname = $.trim($('#catname').val());
				if(!catname) {
					msg.push("请填写类别名称！");
				}
				break;
			case 'type':
				var typename = $.trim($('#typename').val());
				if(!typename) {
					msg.push("请填写类型名称！");
				}
				break;
			case 'brand':
				var brandname = $.trim($('#brandname').val());
				if(!brandname) {
					msg.push("请填写品牌名称！");
				}
				break;
			case 'spec':
				var name = $.trim($('#name').val());
				if(!name) {
					msg.push("请填写规格名称！");
				}
				break;
			case 'man':
				var subject		= $.trim($("#subject").val()),
					begintime	= $.trim($("#begintime").val()),
					endtime		= $.trim($("#endtime").val());
				if(!subject) {
					msg.push("请填写活动主题！");
				} 
				if(!begintime) {
					msg.push("请填写开始时间！");
				}
				if(!endtime) {
					msg.push("请填写结束时间！");
				}
				break;
			case 'gift':
				var begintime	= $.trim($("#begintime").val()),
					endtime		= $.trim($("#endtime").val()),
					itemid		= $.trim($("#itemid").val()),
					subject		= $.trim($("#subject").val()),
					gift		= $("#gift").find("tr");
				if(!subject) {
					msg.push("请输入主题！");
				}
				if(!begintime) {
					msg.push("请填写开始时间！");
				}
				if(!endtime) {
					msg.push("请填写结束时间！");
				}
				if(!itemid) {
					msg.push("请选择商品！");
				}
				if(!gift.length) {
					msg.push("请选择赠品！");
				}
				break;
			case 'meal':
				var title		= $.trim($("#title").val()),
					begintime	= $.trim($("#begintime").val()),
					endtime		= $.trim($("#endtime").val()),
					inventory	= parseInt($("#inventory").val()) || 0,
					price		= parseFloat($("#price").val()) ||0,
					mealitems	= $("#seltbody").find("tr");
				if(!title) {
					msg.push("请填写套餐名称！");
				}
				if(!begintime) {
					msg.push("请填写开始时间！");
				}
				if(!endtime) {
					msg.push("请填写结束时间！");
				}
				if(!inventory) {
					msg.push("请填写库存！");
				}
				if(!price) {
					msg.push("请填写套餐现价！");
				}
				if(mealitems.length <2) {
					msg.push("请选择套餐商品！");
				}
				break;
			case 'discount':
				var subject		= $.trim($("#subject").val()),
					begintime	= $.trim($("#begintime").val()),
					endtime		= $.trim($("#endtime").val()),
					dcitems		= $("#seltbody").find("tr");
				if(!subject) {
					msg.push("请填写活动主题！");
				}
				if(!begintime) {
					msg.push("请填写开始时间！");
				}
				if(!endtime) {
					msg.push("请填写结束时间！");
				}
				if(!dcitems.length) {
					msg.push("请选择折扣商品！");
				}
				break;
			case 'coupon':
				var deno			= parseFloat($("#deno").val()) || 0,
					subject			= $.trim($("#subject").val()),
					endtime			= $.trim($("#endtime").val()),
					total			= parseInt($("#total").val()) || 0,
					require			= parseFloat($("#require").val()) || 0;
				if(!subject) {
					msg.push("请填写主题！");
				}
				if(!deno) {
					msg.push("请填写优惠券面额！");
				}
				if(!endtime) {
					msg.push("请填写有效期！");
				}
				if(!total) {
					msg.push("请填写数量！");
				}
				if(!require) {
					msg.push("请填写使用条件！");
				}
				break;
			case 'tuan':
				var subject		= $.trim($("#subject").val()),
					begintime	= $.trim($("#begintime").val()),
					endtime		= $.trim($("#endtime").val()),
					price		= parseFloat($("#price").val()) ||0,
					itemid		= $.trim($("#itemid").val());
				if(!subject) {
					msg.push("请填写主题！");
				}
				if(!begintime) {
					msg.push("请填写开始时间！");
				}
				if(!endtime) {
					msg.push("请填写结束时间！");
				}
				if(!price) {
					msg.push("请填写团购价格！");
				}
				if(!itemid) {
					msg.push("请选择团购商品！");
				}
				break;
			case 'user':
				if(opertype == 'add') {
					var uname			= $.trim($("#uname").val()),
						pass			= $.trim($("#pass").val());
					if(!uname) {
						msg.push("请填写用户名！");
					}
					if(!pass) {
						msg.push("请填写密码！");
					}
				}
				break;
			case 'expressaddr':
				var linkman			= $.trim($("#linkman").val()),
					province		= $.trim($("#province").val()),
					city			= $.trim($("#city").val()),
					district		= $.trim($("#district").val()),
					address			= $.trim($("#address").val());
				if(!linkman) {
					msg.push("请填写联系人！");
				}
				if(!province) {
					msg.push("请填写所在地区省！");
				}
				if(!city) {
					msg.push("请填写所在地区市！");
				}
				if(!district) {
					msg.push("请填写所在地区县！");
				}
				if(!address) {
					msg.push("请填写街道地址！");
				}
				break;
			case 'expresstpl':
				var name			= $.trim($("#name").val());
				if(!name) {
					msg.push("请填写模版名称！");
				}
				break;
			case 'expresscom':
				var company			= $.trim($("#company").val());
				if(!company) {
					msg.push("请填写公司名称！");
				}
				break;
			case 'expressway':
				var name			= $.trim($("#name").val()),
				    feetype			= $("input[name='feetype']:checked").val(),
				    price2			= parseInt($("#price2").val()) || 0,
					defaultfee		= $("#defaultfee").prop("checked"),
					price1			= parseInt($("#price1").val()) || 0;
				if(!name) {
					msg.push("请填写配送方式名称！");
				}
				if(feetype == "gene"  && price2<=0) {
					msg.push("请填写默认配送费用");
				}
				if(feetype == "self" && defaultfee && price1<=0) {
					msg.push("请填写默认配送费用");
				}
				break;
			case 'navi':
				var naviname = $.trim($("#naviname").val()),
					tabletype = $("input[name='tabletype']:checked").val();
				if(!naviname) {
					msg.push("请填写栏目名称！");
				}
				if(tabletype == 1) {
					var naviurl = $.trim($("#naviurl").val());
					if(!naviurl || naviurl=='http://') {
						msg.push("请填写链接地址！");
					}
				} else if(tabletype == 2) {
					var articlesort = parseInt($("#articlesort").val()) || 0;
					if(!articlesort) {
						msg.push("请选择文章类别！");
					}
				} else if(tabletype == 3) {
					var itemcat = parseInt($("#itemcat").val()) || 0;
					if(!itemcat) {
						msg.push("请选择商品类别！");
					}
				}
				break;
			case 'articlesort':
				var sortname = $.trim($("#sortname").val());
				if(!sortname) {
					msg.push("请填写类别名称！");
				}
				break;
			case 'article':
				var subject		= $.trim($("#subject").val()),
					contenttype = $("input[name='contenttype']:checked").val();
				if(!subject) {
					msg.push("请填写主题！");
				}
				if(contenttype == 'cont') {
					var content = $.trim($("#content").val());
					if(!content) {
						msg.push("请填写内容！");
					}
				} else if(contenttype == 'link') {
					var link = $.trim($("#link").val());
					if(!link || link == 'http://') {
						msg.push("请填写链接地址！");
					}
				}
				break;
			case 'link':
				var linkname = $.trim($("#linkname").val()),
					linkurl	 = $.trim($("#linkurl").val());
				if(!linkname) {
					msg.push("请填写友链名称！");
				}
				if(!linkurl || linkurl == 'http://') {
					msg.push("请填写友链地址！");
				}
				break;
			case 'basic':
				var mallname = $.trim($("#mallname").val());
				if(!mallname) {
					msg.push("请填写商城名称！");
				}
				break;
			case 'cleartest':
				var uname			= $.trim($("#uname").val()),
					pass			= $.trim($("#pass").val());
				if(!uname) {
					msg.push("请填写超管用户名！");
				}
				if(!pass) {
					msg.push("请填写密码！");
				}
				break;
			case 'etao':
				if($("#etao_status").prop("checked")) {
					var etao_account			= $.trim($("#etao_account").val()),
						etao_postfee			= $.trim($("#etao_postfee").val());
					if(!etao_account) {
						msg.push("请填写一淘帐号！");
					}
					if(!etao_postfee) {
						msg.push("请填写默认物流费用！");
					}
				}
				break;
			case 'taobao':
				var taobao_key		= $.trim($("#taobao_key").val());
				var taobao_secret	= $.trim($("#taobao_secret").val());
				if(!taobao_key) {
					msg.push("请输入淘宝开放平台key");
				}
				if(!taobao_secret) {
					msg.push("请输入淘宝开放平台secret");
				}
				break;
			case 'role':
				var name			= $.trim($("#name").val()),
					privilege		= $("input[name='privilege[]']:checked");
				if(!name) {
					msg.push("请填写角色名称！");
				} 
				if(!privilege.length) {
					msg.push("请选择权限！");
				}
				break;
			case 'admin':
				if(opertype == "add") {
					var uname			= $.trim($("#uname").val()),
						pass			= $.trim($("#pass").val());
					if(!uname) {
						msg.push("请填写用户名！");
					}
					if(!pass) {
						msg.push("请填写密码！");
					}
				}
				var issuper = parseInt($("input[name='issuper']:checked").val()) || 0;
				if(!issuper) {
					var role = $("input[name='role[]']:checked");
					if(!role.length) {
						msg.push("请选择角色！");
					}
				}
				break;
			case 'messagetpl':
				var content			= $.trim($("#content").val());
				if(!content) {
					msg.push("请填写内容！");
				}
				break;
			case 'word':
				var word			= $.trim($("#word").val()),
					link			= $.trim($("#link").val());
				if(!word) {
					msg.push("请填写热门关键词！");
				}
				if(!link || link=='http://') {
					msg.push("请填写链接！");
				}
				break;
			case 'flink':
				var name		= $.trim($("#name").val()),
					tag			= $.trim($("#tag").val()),
					link		= $.trim($("#link").val()),
					pic			= $.trim($("#pic").val());
				if(!name) {
					msg.push("请选择广告名称！");
				}
				if(!tag) {
					msg.push("请选择广告标签！");
				}
				if(!link || link=='http://') {
					msg.push("请填写链接！");
				}
				if(!pic) {
					msg.push("请上传广告图片！");
				}
				break;
			case 'kuaidi':
				var kuaidi_status = $("#kuaidi_status").prop("checked"),
					kuaidi_key = $.trim($("#kuaidi_key").val());
				if(kuaidi_status) {
					if(!kuaidi_key) {
						msg.push("请填写快递100key！");
					}
				}
				break;
			case 'upyun':
				if($("#upyun_status").prop("checked")) {
					var upyun_space		= $.trim($("#upyun_space").val()),
						upyun_uname		= $.trim($("#upyun_uname").val()),
						upyun_pass		= $.trim($("#upyun_pass").val()),
						upyun_domain	= $.trim($("#upyun_domain").val());
					if(!upyun_space) {
						msg.push("请填写空间名称！");
					}
					if(!upyun_uname) {
						msg.push("请填写操作员名称！");
					}
					if(!upyun_pass) {
						msg.push("请填写操作员密码！");
					}
					if(!upyun_domain || upyun_domain=='http://') {
						msg.push("请填写域名！");
					}	
				}
				break;
			case 'shareset':
				var cont = $.trim($("#sharecontent").val());
				if(!cont) {
					msg.push("请填写分享内容！");
				}
				break;
			case 'agreeset':
				var cont = $.trim($("#agreecontent").val());
				if(!cont) {
					msg.push("请填写注册协议！");
				}
				break;
			case 'alipay':
				var name		= $.trim($("#name").val()),
					account		= $.trim($("#account").val()),
					paykey		= $.trim($("#paykey").val()),
					paysecret	= $.trim($("#paysecret").val());
				if(!name) {
					msg.push("请填写支付方式名称！");
				}
				if(!account) {
					msg.push("请填写支付宝账户！");
				}
				if(!paykey) {
					msg.push("请填写合作者身份ID！");
				}
				if(!paysecret) {
					msg.push("请填写交易安全校验码！");
				}
				break;
			case 'tenpay2':
			case 'tenpay':
				var name		= $.trim($("#name").val()),
					account		= $.trim($("#account").val()),
					paysecret	= $.trim($("#account").val());
				if(!name) {
					msg.push("请填写支付方式名称！");
				}
				if(!account) {
					msg.push("请输入财付通商户号！");
				}
				if(!paysecret) {
					msg.push("请填写交易安全校验码！");
				}
				break;
			case 'frontad':
				var title = $.trim($("#title").val());
				if(!title) {
					msg.push("请填写主题！");
				}
				var cont = $.trim($("#content").val());
				if(!cont) {
					msg.push("请填写内容！");
				}
				break;
			case 'district':
				var district = $.trim($("#district").val());
				if(!district) {
					msg.push("请填写地区！");
				}
				break;
			case 'pic':
				var spic = parseInt($("#spic").val()) || 0;
					mpic = parseInt($("#mpic").val()) || 0;
					bpic = parseInt($("#bpic").val()) || 0;
				if(!spic) {
					msg.push("请输入正确的小图尺寸（数字）");
				}
				if(!mpic) {
					msg.push("请输入正确的中图尺寸（数字）");
				}
				if(!bpic) {
					msg.push("请输入正确的大图尺寸（数字）");
				}
				break;
			case 'page':
				var pagetitle = $("#pagetitle").val(),
					content	  = $("#content").val();
				if(!pagetitle) {
					msg.push("请输入页面标题");
				}
				if(!content) {
					msg.push("请输入页面内容");
				}
				break;
		
			}
	})();
	if(msg.length) {
		jAlert(msg.join("\r\n")); 
		return false;
	}
	if(ispop) {
		$.oper.popsubmit();
	} else {
		$.oper.submit();
	}
}

(function($){
	$.util = {
		len:function(str){ //判断字符长度
			return str.replace(/[^\x00-\xff]/g,"**").length;
		},
		checkedval:function(ele){
			var ret = [];
			ele.each(function(){
				ret.push(this.value);
			});
			return ret;
		},
		show:function(){
			$(".state_tip").html("<img src=\"template/admin/images/loading2.gif\" border=\"0\" align=\"absmiddle\"/>正在执行").fadeIn();
		},
		hide:function(){
			$(".state_tip").html("操作完成").fadeOut('slow');
		}
	}
})(jQuery);

(function($){
	$.oper = {
		defq:'',//页面搜索默认显示字
		url:'', //操作url
		init:function(url) { //初始化 
			this.url = url;
			return this;
		},
		edit:function(obj,field,id) { //ajax编辑
			var $this = $(obj);
			if($this.find("input").length == 0){
				var txt		 = $this.text(),
					size	 = parseInt($.util.len(txt)),
					$span	 = $("<input type = 'text' value='"+txt+"' size='"+ (size>70?70:size) +"'/>");
					setValue = function() { //向服务器提交修改后的数据
						var spanval = $span.val();
						if(spanval && spanval != txt) {
							$.util.show();
							var postdata = {opertype:"editfield",field:field,value:$.browser.msie?encodeURIComponent(spanval):spanval,id:id};
							$.post($.oper.url,postdata,function(data){
								$.util.hide();
								if(data == "success") { //数据更新成功
									$this.text(spanval);
								} else { //数据更新失败
									$this.text(txt);
									jAlert("发生错误,请刷新页面后重新执行该操作","警告");
								}
							})
						} else {
							$this.text(txt);
						}
					}
				$this.html($span);
				$span.focus().select();
				$span.keyup(function(event){ 
					if(event.keyCode == "13"){
						setValue();
					}
				}).blur(function(){
					setValue();
				});
			} else {
				return ;
			}
		},
		checksel:function(){ //检查是否有选中的checkbox
			if($("#listtbody").find("input:checked").length == 0) {
				jAlert("请选择需要操作的项","警告");
				return false;
			}
			return true;
		},
		selectall:function() { //全选
			$("#listtbody").find("input[type='checkbox']").prop("checked",$("#allselect").prop("checked"));
		},
		showiframe:function(showurl) {
			$("#myiframe").attr("src",showurl);
			$("#outerframe").removeClass("none");
		},
		hideiframe:function() {
			$("#outerframe").addClass("none");
		},
		reply:function(id) {
			jPrompt("","回复",function(val) {
				if(val == "") {
					return ;
				}
			})
		},
		empty:function(type) {
			var _this = this;
			jConfirm("确认清空吗？","确认信息",function(confirm){
				if(confirm){
					$.util.show();
					$.post(_this.url,{opertype:"editfield",field:'empty',type:type},function(data){
						$.util.hide();
						if(data == "success") {
							$("#listtbody tr").remove();
						} else if(data == "error") {
							jAlert("发生错误,请刷新页面后重新执行该操作","警告");
						} else{
							jAlert(data,"警告");
						}
					})
				}
			});
		},
		delist : function(id) { //商品下架
			if(!id && !this.checksel()) return false; 
			this.dooper("delist",id);
		},
		list : function(id) {  //商品上架
			if(!id && !this.checksel()) return false; 
			this.dooper("list",id);
		},
		setdefault : function(field,id) {//设置默认
			this.dooper(field,id,"refresh");
		},
		restore : function(id) { //恢复
			if(!id && !this.checksel()) return false; 
			this.dooper("restore",id);
		},
		remove : function(id){
			if(!id && !this.checksel()) return false; 
			jConfirm("确认移到至回收站吗？","确认信息",function(confirm){
				if(confirm){
					$.oper.dooper("remove",id);
				}
			})
		},
		copy:function(text) {
			if(window.clipboardData) {
				window.clipboardData.setData("Text",text); 
				jAlert("复制成功","复制地址");
			} else {
				$html = $("<div><input type='text' value='"+text+"' class='input_tx' size='20' onclick='this.select()' onfocus='this.select()' onblur='this.select()'/></div>");
				$html.find("input").click();
				jAlert($html.html() + "<br />点击文本框，CRTL+C复制","复制地址");
			}
		},
		cdelete : function(id) { //删除
			if(!id && !this.checksel()) return false; 
			jConfirm("确认删除吗？","确认信息",function(confirm){
				if(confirm){
					$.oper.dooper("delete",id);
				}
			});
		},
		dooper : function(field,id,onsuccess){//js 操作
			var ids = new Array();
			id && (ids.push(id)) || (ids = $.util.checkedval($("#listtbody").find("input:checked")));
			$.util.show();
			$.post(this.url,{opertype:"editfield",field:field,idstr:ids.join(",")},function(data) {
				$.util.hide();
				$("#allselect").prop("checked",false);
				if(data == "success"){ //操作成功
					if(!onsuccess) {	//如果未指定成功执行后的动作
						for(var i in ids) {
							$("#tr_"+ids[i]).remove();
						}
					} else if(onsuccess == "refresh") { //页面刷新
						$.oper.refresh();	
					}
				} else if(data == "failure") { //操作错误
					jAlert("发生错误,请刷新页面后重新执行该操作","警告");
				} else {
					jAlert(data,"警告");
				}
			});
		},
		up : function(obj) {//向上排序
			var $this = $(obj),$parent = $this.parent().parent(),_prev = $parent.prev();
			if(_prev.html() != null) {
				_prev.before($parent.clone());
				$parent.remove();
			}
		},
		down : function(obj) {//向下排序
			var $this = $(obj),$parent = $this.parent().parent(),_next = $parent.next();
			if(_next.html() !=null){
				_next.after($parent.clone());
				$parent.remove();
			}
		},
		del : function(obj) {  //删除
			$(obj).parent().parent().remove();
		},
		jstab : function(id) { //页面Tab
			var $this = $("#"+id);
			$("#infotab li").removeClass("cur");
			$this.parent().addClass("cur");
			$(".bk").hide();
			$("#for"+id.replace("a_","")).show();
		},
		submit : function() {//表单提交
			$("#submitform").submit();
		},
		popsubmit : function() {//pop表单提交
			$("#popform").submit();
		},
		seasubmit : function(cdo) { //搜索表单提交
			cdo && $("#do").val(cdo);//如果该表单需要执行其他操作
			$("#seaform").submit();
		},
		refresh : function() {//刷新
			$("#seaform").submit();
		},
		addcolor : function(obj){
			$(obj).addClass("trcolor");
		},
		removecolor : function(obj){
			$(obj).removeClass("trcolor");
		},
		bgcolor:function(obj,type) {
			$(obj).css('backgroundColor',type=='on' ? '#0b819f':'');
		},
		runjs : function(runurl) { //执行服务器端发送的js
			$.post(runurl,{},function(data){
				$(".state_tip").html(data);
			});
		},
		setdefq : function(defq) {//设置搜索默认的文字
			this.defq = defq;
		},
		checkq : function(event) {//提交表单时，检查q
			var $q = $('#q'), q = $.trim($q.val());
			(q == this.defq && (event == "focus" ||event == "submit") ) && $q.removeClass('hintinput').val('');
			(q == "" && event=="blur") && $q.addClass('hintinput').val(this.defq);
			return true;
		}
	}
})(jQuery);

$(function(){
	$("#sidebar_corner").click(function() {
		var $this = $(this);
		if($this.hasClass('sidebar_corner_r1')) {
			$this.removeClass('sidebar_corner_r1').addClass('sidebar_corner_r2');
			$("#side_nav").hide();
			$("#maincont").css({"marginLeft":"10px"});
		} else if($this.hasClass('sidebar_corner_r2')) {
			$this.removeClass('sidebar_corner_r2').addClass('sidebar_corner_r1');
			$("#side_nav").show();
			$("#maincont").css({"marginLeft":"202px"});
		}
	});
});