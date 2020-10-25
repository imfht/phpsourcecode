if(typeof(Qibo)=='undefined'){

var Qibo = function () {
	//超级链接那里加上 class="_pop" 就可以实现弹窗, 设置 data-width="600" data-height="600" 就可以指定弹窗大小 , 设置  data-title="标题XXX" 就可以设置弹窗标题
	var pop = function(){
		jQuery(document).delegate('a._pop', 'click', function () {
			if((navigator.userAgent.match(/(iPhone|iPod|Android|ios|iPad)/i))||$("body").width()<1000){
				var default_width = "95%";
				var default_height = "90%";
			}else{
				var default_width = "1000px";
				var default_height = "650px";
			}
			var width = typeof($(this).data('width'))=='undefined'?default_width:$(this).data('width');
			var height = typeof($(this).data('height'))=='undefined'?default_height:$(this).data('height');
			var title = typeof($(this).data('title'))=='undefined'?'快速操作':$(this).data('title');
			layer.open({
			  type: 2,
			  title: title,
			  shade: [0.3,'#333'], 
			  area: [width, height],
			  anim: 1,
			  content: $(this).attr("href"),
			  end: function(){ //关闭事件	
			  }
			});
			return false;
		});
	}
	
	//超级链接那里加上  class="alert" 就可以实现弹窗确认. 设置 data-alert="你确认要修改吗?"  就可以指定提示语,这个参数也可以不设置.
	var _confirm = function(){
		jQuery(document).delegate('a.alert', 'click', function () {
			var url = $(this).attr("href");
			var msg = typeof($(this).data('alert'))=='undefined'?'你确认要删除吗?':$(this).data("alert");
			var title = typeof($(this).data('title'))=='undefined'?'提示':$(this).data('title');
			layer.confirm(msg, {title:title, btn : [ '确定', '取消' ]}, function(index) {
				window.location.href = url;
			});
			return false;
		});
	}
	
	//配合下面这个方法 _ajaxget 使用
	var _ajaxgoto = function(url,id){
		var index = layer.msg('请稍候...');
		$.get(url,function(res){
			layer.close(index);
			if(res.code==0){	//成功提示
				layer.msg(res.msg);
				setTimeout(function(){
					if(res.url!=''){
						window.location.href = res.url;
					}else{
						window.location.reload();
					}						
				},500);
			}else{	//错误提示
				if(res.url!=''){
					layer.confirm(res.msg, {title:'提示', btn : [ '确定', '取消' ]}, function(index) {
						window.location.href = res.url;
					});
				}else{
					layer.open({title: '提示!',content:res.msg});
					if(typeof(ajax_get)=='function'){
						ajax_get(res,id);		//页面里定义的函数
					}
				}
			}
		});
	}
	
	//超级链接那里加上  class="ajaxget" 就可以实现ajax访问. 设置 data-alert="你确认这么做吗?"  就可以指定提示语,这个参数也可以不设置.
	var _ajaxget = function(){
		
		jQuery(document).delegate('a.ajax_get', 'click', function () {
			var url = $(this).attr("href");
			var msg = $(this).data("alert");
			var id = $(this).data("id");
			if(typeof(msg)!='undefined'){
				layer.confirm(msg, {title:'提醒', btn : [ '确定', '取消' ]}, function(index) {
					_ajaxgoto(url,id);
				});
			}else{
				_ajaxgoto(url,id);
			}
			return false;
		});
	}
	
	//显示大图片
	var showimg = function(){
		$("img.showimg").click(function(){
			var url = $(this).attr("src");
			$("<img />").attr("src", url).on("load", function () {
				var imgw = this.width>$("body").width() ? $("body").width() : this.width ;
				var imgh = this.height * imgw/this.width;
				layer.open({
				  type: 1,
				  title: false,
				  shadeClose: true,
				  maxmin: true,
				  offset: 'auto', 
				  shade: 0.4,
				  area: [imgw+"px", imgh+"px"],
				  content: "<img src='"+url+"' style='width:"+imgw+"px;height:"+imgh+"px;'>",
				});
			});
		});
	}

	var check_back = function(url){
		if(typeof(api)=="object"){	//在APP中打开的情况
			api.execScript({
                    //frameName: 'iframe',
					name:"main",
                    script: 'app_back()'
             });
		}else{
			window.location.href = url;
		}		
	}
	
	//直接使用window.history.go(-1) window.history.back() 遇到新开的页面,就导致无法返回, 用这个函数可以给他默认指定一个返回页面
	var goBack = function(url) {		
		if ((navigator.userAgent.indexOf('MSIE') >= 0) && (navigator.userAgent.indexOf('Opera') < 0)) { // IE 
			if (history.length > 0) {
				window.history.go(-1);
			} else {
				check_back(url);
				//window.opener = null;
				//window.close();
			}
		} else { //非IE浏览器 
			if (navigator.userAgent.indexOf('Firefox') >= 0 || navigator.userAgent.indexOf('Opera') >= 0 || navigator.userAgent.indexOf('Safari') >= 0 || navigator.userAgent.indexOf('Chrome') >= 0 || navigator.userAgent.indexOf('WebKit') >= 0) {

				if (window.history.length > 1) {
					window.history.go(-1);
				} else {
					check_back(url);
					//window.opener = null;
					//window.close();
				}
			} else { //未知的浏览器 
				window.history.go(-1);
			}
		}
	}
	
	//下拉菜单开始
	var moreMenu = {
		showSonId:null,
		showObjWidth:0,
		showObjHeight:0,
		topObj:null,		
		init:function(){
			oo=document.body.getElementsByClassName("more-menu");
			for(var i=0;i<oo.length;i++){
				if(oo[i].getAttribute("click")!=null){
					if(oo[i].getAttribute("href")=="#")oo[i].href='javascript:';
					if (document.all) { //For IE
						oo[i].attachEvent("onmousedown",moreMenu.showdiv);
						oo[i].attachEvent("onmouseover",moreMenu.showstyle);
						oo[i].attachEvent("onmouseout",moreMenu.hidestyle);
					}else{ //For Mozilla
						oo[i].addEventListener("mousedown",moreMenu.showdiv,true);
						oo[i].addEventListener("mouseover",moreMenu.showstyle,true);
						oo[i].addEventListener("mouseout",moreMenu.hidestyle,true);
					}
				}else if(oo[i].getAttribute("url")!=null){
					if(oo[i].getAttribute("href")=="#")oo[i].href='javascript:';
					if (document.all) { //For IE
						oo[i].attachEvent("onmouseover",this.showdiv);
					}else{ //For Mozilla
						oo[i].addEventListener("mouseover",this.showdiv,true);
					}
				}
			}
		},
		getposition:function(o){
			var to=new Object();
			to.left=to.right=to.top=to.bottom=0;
			var twidth=o.offsetWidth;
			var theight=o.offsetHeight;
			while(o!=document.body){
				to.left+=o.offsetLeft;
				to.top+=o.offsetTop;
				o=o.offsetParent;
			}
			to.right=to.left+twidth;
			to.bottom=to.top+theight;
			return to;
		},
		showstyle:function(evt){
			var evt = (evt) ? evt : ((window.event) ? window.event : "");
			if (evt) {
				 ao = (evt.target) ? evt.target : evt.srcElement;
			}
			ao.style.border='1px dotted red';
			ao.style.cursor='pointer';
		},
		hidestyle:function(evt){
			var evt = (evt) ? evt : ((window.event) ? window.event : "");
			if (evt) {
				 ao = (evt.target) ? evt.target : evt.srcElement;
			}
			ao.style.border='0px dotted red';
		},
		showdiv:function(evt){
			var evt = (evt) ? evt : ((window.event) ? window.event : "");
			if (evt) {
				 ao = (evt.target) ? evt.target : evt.srcElement;
			}
			ao.style.cursor='pointer';
			moreMenu.topObj = ao;
			position=moreMenu.getposition(ao);	//获取坐标
			thisurl=ao.getAttribute("url");
			oid=thisurl.replace(/[^\w|^\-]/g,"").substr(0,200);
			ao.id = oid;
			moreMenu.showSonId = DivId = "clickEdit_"+oid;
			//thisurl=thisurl + "&TagId=" + oid;
			obj=document.getElementById(DivId);
			if(obj==null){
				obj=document.createElement("div");
				//obj.innerHTML='<table border="0" cellspacing="0" cellpadding="0" id="AjaxEditTable" class="AjaxEditTable"><tr><td class="head"><span onclick="moreMenu.cancel(\''+DivId+'\')">关闭</span></td></tr><tr> <td class="middle"></td></tr></table>';
				//objs=obj.getElementsByTagName("TD");
				//objs[1].id=DivId;
				obj.innerHTML='<div class="more-menu-wap"><div class="more-menu-in" id="'+DivId+'"></div></div>';
				obj.style.Zindex='9990';
				obj.style.display='none';	//网速慢的话,就把这行删除掉,直接先显示,再加载其它内容
				obj.style.position='absolute';
				obj.style.top=position.bottom+'px';
				obj.style.left=position.left+'px';
				//obj.style.height='100px';
				//obj.style.width=moreMenu.width+'px';
				document.body.appendChild(obj);
				//moreMenu.getparent(DivId).show("slow");
				//obj.innerHTML='以下是显示内容...';
				if(thisurl.indexOf('<')>-1){
					$("#"+DivId).html(thisurl);				
					if($(ao).width()>moreMenu.getparent(DivId).width()){
						moreMenu.getparent(DivId).css("width",$(ao).width()+"px");
					}
					moreMenu.getparent(DivId).show();
					setTimeout(function(){
						moreMenu.getparent(DivId).show(); //避免有时不显示
					},100);
					moreMenu.autohide(ao);
				}else{
					$.get(thisurl+(thisurl.indexOf("?")==-1?"?":"&")+Math.random(),function(res){
						if(res.code==0){
							$("#"+DivId).html(res.data);				
							if($(ao).width()>moreMenu.getparent(DivId).width()){
								moreMenu.getparent(DivId).css("width",$(ao).width()+"px");
							}
							moreMenu.getparent(DivId).show();
							moreMenu.autohide(ao);
						}else{
							moreMenu.getparent(DivId).hide();
							document.body.removeChild(obj);
							return ;
						}						
					});
				}
			}else{
				//兼容缩放窗口后,要重新定位
				moreMenu.getparent(DivId).css({"left":position.left+'px',"top":position.bottom+'px'});
				moreMenu.getparent(DivId).show();
				setTimeout(function(){
					moreMenu.getparent(DivId).show(); //避免有时不显示
				},100);
				moreMenu.autohide(ao);
			}
		},
		getparent:function(sonId){
			parentObj = $("#"+sonId).parent().parent();
			return parentObj;
		},
		cancel:function(sonId){
			moreMenu.getparent(sonId).hide();
		},
		autohide:function(eObj){
			parentObj = moreMenu.getparent(moreMenu.showSonId);
			//要提前赋值,不然渐变隐藏或显示,会引起宽高的变化
			w1 = $(eObj).width();
			w2 = parentObj.width();
			moreMenu.showObjWidth = w1>w2 ? w1 : w2;
			moreMenu.showObjHeight = parentObj.height();
			document.onmousemove = moreMenu.mouseMove;	//不想鼠标离开隐藏的话,就把这行删除掉
		},
		mouseMove:function(ev){
			ev = ev || window.event;
			var mousePos = moreMenu.mousePosition(ev);
			var x = mousePos.x;
			var y = mousePos.y;
			parentObj = moreMenu.getparent(moreMenu.showSonId);
			left1 = parseInt(parentObj.css("left"));
			top1 = parseInt(parentObj.css("top"))-$(moreMenu.topObj).height();
			left2 = left1 + moreMenu.showObjWidth ;
			top2 = top1 + moreMenu.showObjHeight+$(moreMenu.topObj).height();
			if ( x<left1 || x>left2 || y<top1 || y>top2){
				moreMenu.cancel(moreMenu.showSonId);
				//document.title=x+"-"+y+" 横 "+left1+"-"+left2+" 高 "+top1+"-"+top2 + "p高"+ parentObj.height();
			}
		},
		mousePosition:function(ev){	//获取鼠标所在坐标
			if(ev.pageX || ev.pageY){	//FF
				return {x:ev.pageX, y:ev.pageY};
			}
			return {	//IE
				x:ev.clientX + window.document.documentElement.scrollLeft,// - window.document.documentElement.clientLeft,
				y:ev.clientY + window.document.documentElement.scrollTop//  - window.document.documentElement.clientTop
			};
		}
	}//下拉菜单结束

	return {
			init:function(){
				pop();
				_confirm();
				_ajaxget();
				moreMenu.init();
				showimg();
			},
			goBack:function(url){
				goBack(url);
			},
	};
}();

$(document).ready(function(){
	Qibo.init();
});

}