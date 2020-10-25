//点击所有授权的弹窗

$(function(){
	if (!$(".isAllowed").attr('data-ids')) {
	    $(".isAllowed").css({"background":"#C6C6C6","cursor":"not-allowed"});
	}
	//点击刷新，页面仍然保留当前页
	$("#sidebar-nav div .list-wrap ul ul li a").removeClass("selected");
	if (getCookie('url') == 'undefined' || getCookie('url') == '') {
		$("#mainframe").attr('src', home);
	} else{
		$("#mainframe").attr('src', getCookie('url'));
	}
	//自动获一iframe地址
	var indexbody = $('.indexbody', parent.document);
	var iframeUrl = indexbody.find('#mainframe').attr('src');
	var arr=[];
	var obj=[];
	var level_nav=[];
	var levelNav= indexbody.find('#main-nav a');
	var sub= indexbody.find('.sub_menu');
	
	levelNav.each(function(){
		level_nav.push($(this));
	});

	sub.each(function(){
		obj.push($(this))
		 
	})
	for(var i=0;i<arr.length;i++){
		if(arr[i]==iframeUrl){
			//控制垂直列表的selected
			obj[i].closest(".list-group").css({display:"block"}).addClass("selected");
			obj[i].closest(".list-group").siblings().css({display:"none"}).removeClass("selected");
			indexbody.find("#sidebar-nav div .list-wrap ul ul li a").removeClass("selected");
			obj[i].addClass("selected");

			var my_data=obj[i].closest(".list-group").attr("data-menu");
			//alert(my_data);
			var maina=indexbody.find('#main-nav a[data-menu="'+my_data+'"]');
			indexbody.find('#main-nav a').removeClass("selected");
			maina.addClass("selected");
		}else if($.inArray(iframeUrl, arr)==-1){
			//控制垂直方向
			indexbody.find("#sidebar-nav .list-wrap ul ul li a").removeClass("selected");
			indexbody.find(obj[getCookie('vertical_id')]).closest(".list-group").css({display:"block"});
			indexbody.find(obj[getCookie('vertical_id')]).closest(".list-group").siblings().css({display:"none"});
			indexbody.find(obj[getCookie('vertical_id')]).addClass("selected");
			//控制水平方向
			
			indexbody.find('#main-nav a').removeClass("selected");
			indexbody.find(level_nav[getCookie('level_id')]).addClass("selected");
		}
	}
	
	$(".add").click(function(){
		common_click();
		var dataUrl=$(this).attr("href");
		$('.indexbody', parent.document).find('#mainframe').attr("src", dataUrl);
		setCookie('url', dataUrl, 60);
	});

	//用户管理-用户列表-所有会员组
	$(".menu-list .select-items ul li").click(function(){
		common_click();
		if(!$(this).hasClass("not_active")){
			var index=	$(this).index();
			var value= $(this).html();
			var data_id=$("#ddlGroupId option").eq(index).attr("data-id");
			alert(select_list_url);
			var new_src=select_list_url+"/group_id/"+data_id+".html";
			indexbody.find('#mainframe').attr("src",new_src);
		}
		
	})
	//点击水平导航 存储cookies
	$("#main-nav a").click(function(event) {
		//如果水平导航有selected，去除重复的垂直导航selected
		$("#sidebar-nav ul li ul li a").removeClass("selected");
		var dataNum = $(this).attr("data-menu");
		$("#main-nav a").removeClass("selected");
		$(this).addClass("selected");
		$("#sidebar-nav>div").hide();
		$("#sidebar-nav div[data-menu='" + dataNum + "']").show();
		var list_url = $("#sidebar-nav div[data-menu='" + dataNum + "'] .list-wrap ul ul li:eq(0) a:eq(0)").attr("data-url");
		$("#sidebar-nav div").each(function() {
			if ($(this).attr("data-menu") == dataNum) {
				$(this).show();
			}
		})
		if(getCookie('url') !== ''){
			setCookie('old_url', getCookie('url'), 60);
		}
		$(".main-container iframe").attr("src", list_url);
		setCookie('url', list_url, 60);
	});
	
	//点击控制垂直列表的selected
	$("#sidebar-nav div").find('a').next().find('li').children('a').click(function(event){
		if(event.type=="click"){
			var getUrl=$(this).attr('data-url');
			$(this).closest(".list-group").find("ul li ul li a").removeClass("selected");
			$(this).addClass("selected")
		}
		if(getCookie('url') !== ''){
			setCookie('old_url', getCookie('url'), 60);
		}
		$('#mainframe').attr("src",getUrl);
		setCookie('url', getUrl, 60)
	});

	//竞拍盲抢搜索
	$(".type-list .select-items ul li").click(function(){
		common_click();
		if(!$(this).hasClass("not_active")){
			var index=	$(this).index();
			var value= $(this).html();
			var data_id=$("#ddltypeId option").eq(index).attr("data-id");
			var new_src=select_list_url+"/group_id/"+data_id+".html";
			indexbody.find('#mainframe').attr("src",new_src);
		}
	});
		//商城商品管理 是否开启分拥
	$(".brand-list .select-items ul li").click(function(){
		common_click();
		var index=	$(this).index();
		var value= $(this).html(); 
		var data_str = '';
		eval('data_str = ' +  $("#ddlBrandId option").eq(index).attr("data-id"));
		var new_src=select_list_url+"/is_fast/"+data_str.is_fast+"/is_reward/"+data_str.is_reward+"/type/"+data_str.type+".html";
		indexbody.find('#mainframe').attr("src",new_src);
	});

	//商城商品管理 是否开启快返
	$(".fast-list .select-items ul li").click(function(){
		common_click();
		var index=	$(this).index();
		var value= $(this).html(); 
		var data_str = '';
		eval('data_str = ' +  $("#ddlfastId option").eq(index).attr("data-id"));
		var new_src=select_list_url+"/is_fast/"+data_str.is_fast+"/is_reward/"+data_str.is_reward+"/type/"+data_str.type+".html";
		indexbody.find('#mainframe').attr("src",new_src);
	});

	//商城商品管理 类型搜索
	$(".manager-list .select-items ul li").click(function(){
		common_click();
		var index=	$(this).index();
		var value= $(this).html(); 
		var data_str = '';
		eval('data_str = ' +  $("#ddlGroupId option").eq(index).attr("data-id"));
		var new_src=select_list_url+"/is_fast/"+data_str.is_fast+"/is_reward/"+data_str.is_reward+"/type/"+data_str.type+".html";
		indexbody.find('#mainframe').attr("src",new_src);
	});
	//全选或取消以及批量删除的效果
	var Ids=[];
	$(".all").click(function(){
		Ids.length=0;
		//判断点击全选的时候如果一条列表都没有，就不执行全选取消的操作
		if($(".checkall").has("input").length>0){
			if ($(this).text() == "全选") {
		        $(this).children("span").text("取消");
		        $(".checkall input:enabled").prop("checked", true);
		        $(".checkall input").each(function(){
		        	Ids.push($(this).attr("value"));
		        })
		        $(".checkall input").attr("checked","checked");
		        				$(".isAllowed").attr({"data-ids":Ids}).css({"background":"#fafafa","cursor":"pointer"});
		      	$(".isAllowed").addClass("isConfirm");
		    } else if($(this).text() == "取消") {
		        $(this).children("span").text("全选");
		        $(".checkall input:enabled").prop("checked", false);
		        $(".checkall input").removeAttr("checked");
		        				$(".isAllowed").attr("data-ids",Ids).css({"background":"#C6C6C6","cursor":"not-allowed"});
		      	$(".isAllowed").attr("data-ids","");
		      	$(".isAllowed").removeClass("isConfirm")
		    }
		}
	})
	//点击判断如果有2个以上的input被选中，ids数组值的变化及删除节点的样式变化
  	$(".checkall input").each(function(){
	  	$(this).click(function(){
	  		if($(this).attr("checked")=="checked"){
	  			$(this).removeAttr("checked");
	  			if($.inArray($(this).attr("value"), Ids)==-1){
	  			}else{
	  				//如果Ids中已经含有这个value值，就删除它
	  				for(var i=0; i<Ids.length; i++) {
					    if(Ids[i] == $(this).attr("value")) {
					      Ids.splice(i, 1);
					      break;
					    }
					}
	  			}
  			  	if(Ids.length>=2){
  			  		dayu();
			  	}else{
			  		xiaoyu();
			  	}
	  			$(".isAllowed").attr("data-ids",Ids);
	  			$(".hid_value").attr("value",Ids);
	  		}else{
	  			$(this).attr("checked","checked");
	  			//如果Ids中已经不含有这个value值，就添加它
	  			if($.inArray($(this).attr("value"), Ids)==-1){
	  				Ids.push($(this).attr("value"));
	  			}
  			  	if(Ids.length>=2){
  			  		dayu();
  			  	}else{
  			  		xiaoyu();
  			  	}
	  			$(".isAllowed").attr("data-ids",Ids);
	  			$(".hid_value").attr("value",Ids);
	  		}
	  	})
	})
	$("body").on("click",".isConfirm",function() {
		if($(this).hasClass("btn-xs")){
			Ids.length=1;
		}
		$.confirm({
			dataType: $(this).attr("data-type"),
			dataUrl: $(this).attr("data-url"),
			dataIds: $(this).attr("data-ids"),
			tipName: $(this).text(),
			icon: "icon-warning-2",
			title: "温馨提示",
			message: "您确定要" + $(this).text() + "吗?<div class='model_listchecked_count'>已选择"+Ids.length+"条</div>",
			buttons: {
				"确认": {
					"class": "btnOK"
				},
				"取消": {
					"class": "btnCancel"
				}
			}
		})
	});
	//全选或取消时，判断Ids数组的长度大于2时，删除节点的样式
	function dayu(){
	  	$(".isAllowed").attr("data-ids",Ids).css({"background":"#fafafa","cursor":"pointer"});
	  	$(".isAllowed").addClass("isConfirm");
	}
	//全选或取消时，判断Ids数组的长度小于2时，删除节点的样式
	function xiaoyu(){
		$(".isAllowed").attr("data-ids",Ids).css({"background":"#C6C6C6","cursor":"not-allowed"});
		$(".isAllowed").removeClass("isConfirm");
	}
	//点击需要条页面时，书刷新之前记录离节点最近的水平及垂直的含有selected的属性，并存储cookies，刷新后还让其显示
	function common_click(){
		for(i=0;i<obj.length;i++){
				if(obj[i].hasClass("selected")){
					setCookie('vertical_id', i, 60);
				}
			}
			for(i=0;i<level_nav.length;i++){
				if(level_nav[i].hasClass("selected")){
					setCookie('level_id', i, 60);
				}
			}
	}
	//点击首页的时候跳转首页，以及水平导航和垂直导航的变化
	$(".home").click(function(){
		indexbody.find('#mainframe').attr("src",home)
	})
	
	$(".back").click(function(){
		if (getCookie('old_url') == 'undefined' || getCookie('old_url') == '') {
			indexbody.find('#mainframe').attr("src",home);
		}else{
			indexbody.find('#mainframe').attr("src",getCookie('old_url'));
		}
		
	})
	
	//点击收缩按钮，控制右侧垂直导航的宽度
	if(document.body.clientWidth<"1028"){
		$("#main-nav").css({"display":"none"});
	}else{
		$("#main-nav").css({"display":"block"})
	}
	window.onresize = function(){
		if(document.body.clientWidth<"1028"){
			$("body").addClass("lay-mini");
			$("#main-nav").css({"display":"none"});
		}
		if(document.body.clientWidth>="1028"){
			$("body").attr("class","indexbody");
			$("#main-nav").css({"display":"block"})
		}
	};
	//点击导航图标时，当前图标的类容显示
	$("#sidebar-nav .list-group h1").click(function(){
		$("#sidebar-nav").find(".list-group").hide();
		$(this).closest(".list-group").show();
		console.log($(this).closest(".list-group"));
	})
	
	$(".icon-menu").click(function(){
		if($("body").hasClass("lay-mini")){
			$("body").attr("class","indexbody");
			if(document.body.clientWidth<"1028"){
				$("#main-nav").css({"display":"none"})
			}else{
				$("#main-nav").css({"display":"block"})
			}
			
		}else{
			$("body").addClass("lay-mini");
			if(document.body.clientWidth>="1028"){
				$("#main-nav").css({"display":"block"})
			}else{
				$("#main-nav").css({"display":"none"})
			}
		}
			
	})
		//点击所有授权的弹窗
	$(".groupAuth").on("click", function() {
		var a = $(this).attr("data-url");
		parent.layer.open({
			type: 2,
			title:"后台权限管理",
			area:  ['580px','480px'],
			closeBtn: 1,
			content: a,
			success: function(b, d) {
				layer.iframeAuto(d);
				var c = parent.layer.getChildFrame("body", d);
				c.find(".btnOK").on("click", function() {
					var b = c.find("#GroupForm").serializeArray(),
						e = c.find("#uid").val();
					$.post(a, {
						fromData: b,
						uid: e
					}, function(a) {
						parent.layer.msg(a.info, {
							time: 2E3
						});
						a.url && "" != a.url && setTimeout(function() {
							location.href = a.url
						}, 2E3);
						"" == a.url && setTimeout(function() {
							location.href = a.url
						}, 1E3)
					})
					setTimeout(function(){
						window.location.reload();
					},1000)
					parent.layer.close(d);
				});
				c.find(".btnCancel,.close").on("click", function() {
					parent.layer.close(d)
				})
			}
		})

	})
	//首页4个账单水平，垂直跳转的公共js
	$(".recharge_fund").click(function(){
		var recharge_fund=$(this).attr("data-url");
		indexbody.find('#mainframe').attr("src",recharge_fund);
		setCookie('url',recharge_fund , 60)
	})
})	