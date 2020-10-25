var $,tab,skyconsWeather;
function getRootPath_web() {
    //获取当前网址，如： http://localhost:8083/uimcardprj/share/meun.jsp
    var curWwwPath = window.document.location.href;
    //获取主机地址之后的目录，如： uimcardprj/share/meun.jsp
    var pathName = window.document.location.pathname;
    var pos = curWwwPath.indexOf(pathName);
    //获取主机地址，如： http://localhost:8083
    var localhostPaht = curWwwPath.substring(0, pos);
    //获取带"/"的项目名，如：/uimcardprj
    var projectName = pathName.substring(0, pathName.substr(1).indexOf('/') + 1);
    return (localhostPaht + projectName);
}

var urlArr = window.location.href.split('admin.php');

var base_url=urlArr[0];

layui.config({
	base : base_url+"/public/admin/js/"
}).use(['bodyTab','form','element','layer','jquery'],function(){
	var form = layui.form,
		layer = layui.layer,
		element = layui.element;
		$ = layui.jquery;
		tab = layui.bodyTab({
			openTabNum : "50",  //最大可打开窗口数量
			url : navs //获取菜单json地址
		});


	
	
	$('.update_cache').click(function(){
    loading = layer.load(2, {
      shade: [0.2,'#000']
    });
    $.getJSON($(this).data('url'),function(data){
      if(data.code == 1){
        layer.close(loading);
        layer.msg(data.msg, {icon: 1, time: 1000}, function(){
        	 location.reload();
        });
      }else{
        layer.close(loading);
        layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
      }
    });
    
  });

  $('.signOut').click(function(){
    loading = layer.load(2, {
      shade: [0.2,'#000']
    });
   
    $.getJSON($(this).data('url'),function(data){
      if(data.code == 1){
        layer.close(loading);
        layer.msg(data.msg, {icon: 1, time: 1000}, function(){
        	location.reload();
        });
      }else{
        layer.close(loading);
        layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
      }
    });
  });
  $('#changemima').on('click', function() { 
  

	  layer.open({
	  type: 2, 
	  offset: ['100px', '30%'],
	  area: ['600px', '400px'],
	  content: $(this).data('url'),
	 
  btnAlign: 'r',
  closeBtn:1
	 
	  ,cancel: function(){ 
	    //右上角关闭回调
	    
	    //return false 开启该代码可禁止点击该按钮关闭
	  }
	});
	  
	});
	//隐藏左侧导航
	$(".hideMenu").click(function(){
		$(".layui-layout-admin").toggleClass("showMenu");
		//渲染顶部窗口
		tab.tabMove();
	})

	//渲染左侧菜单
	tab.render();


	//锁屏
    $(document).on('keydown', function () {
        var e = window.event;
        if (e.keyCode === 76 && e.altKey) {
            //alert("你按下了alt+l");
            lock($, layer);
        }
    });
	$(".lockcms").on("click",function(){
		window.sessionStorage.setItem("lockcms",true);
		lock($, layer);
	})
	// 判断是否显示锁屏
	if(window.sessionStorage.getItem("lockcms") == "true"){
		lock($, layer);
	}


	//手机设备的简单适配
	var treeMobile = $('.site-tree-mobile'),
		shadeMobile = $('.site-mobile-shade')

	treeMobile.on('click', function(){
		$('body').addClass('site-mobile');
	});

	shadeMobile.on('click', function(){
		$('body').removeClass('site-mobile');
	});

	// 添加新窗口
	$("body").on("click",".layui-side .layui-nav-item a",function(){
		if($(this).attr("data-url")){
			//如果不存在子级
			if($(this).siblings().length == 0){
				addTab($(this));
				$('body').removeClass('site-mobile');  //移动端点击菜单关闭菜单层
			}
		}
		$(this).parent("li").siblings().removeClass("layui-nav-itemed");
	})




	//刷新后还原打开的窗口
	if(window.sessionStorage.getItem("menu") != null){
		menu = JSON.parse(window.sessionStorage.getItem("menu"));
		curmenu = window.sessionStorage.getItem("curmenu");
		var openTitle = '';
		for(var i=0;i<menu.length;i++){
			openTitle = '';
			if(menu[i].icon){
				if(menu[i].icon.split("-")[0] == 'fa'){
					openTitle += '<i class="fa '+menu[i].icon+'"></i>';
				}else{
					openTitle += '<i class="layui-icon">'+menu[i].icon+'</i>';
				}
			}
			openTitle += '<cite>'+menu[i].title+'</cite>';
			openTitle += '<i class="layui-icon layui-unselect layui-tab-close" data-id="'+menu[i].layId+'">&#x1006;</i>';
			element.tabAdd("bodyTab",{
				title : openTitle,
		        content :"<iframe src='"+menu[i].href+"' data-id='"+menu[i].layId+"'></frame>",
		        id : menu[i].layId
			})
			//定位到刷新前的窗口
			if(curmenu != "undefined"){
				if(curmenu == '' || curmenu == "null"){  //定位到后台首页
					element.tabChange("bodyTab",'');
				}else if(JSON.parse(curmenu).title == menu[i].title){  //定位到刷新前的页面
					element.tabChange("bodyTab",menu[i].layId);
				}
			}else{
				element.tabChange("bodyTab",menu[menu.length-1].layId);
			}
		}
		//渲染顶部窗口
		tab.tabMove();
	}

	//关闭其他
	$(".closePageOther").on("click",function(){
		if($("#top_tabs li").length>2 && $("#top_tabs li.layui-this cite").text()!="后台首页"){
			var menu = JSON.parse(window.sessionStorage.getItem("menu"));
			$("#top_tabs li").each(function(){
				if($(this).attr("lay-id") != '' && !$(this).hasClass("layui-this")){
					element.tabDelete("bodyTab",$(this).attr("lay-id")).init();
					//此处将当前窗口重新获取放入session，避免一个个删除来回循环造成的不必要工作量
					for(var i=0;i<menu.length;i++){
						if($("#top_tabs li.layui-this cite").text() == menu[i].title){
							menu.splice(0,menu.length,menu[i]);
							window.sessionStorage.setItem("menu",JSON.stringify(menu));
						}
					}
				}
			})
		}else if($("#top_tabs li.layui-this cite").text()=="后台首页" && $("#top_tabs li").length>1){
			$("#top_tabs li").each(function(){
				if($(this).attr("lay-id") != '' && !$(this).hasClass("layui-this")){
					element.tabDelete("bodyTab",$(this).attr("lay-id")).init();
					window.sessionStorage.removeItem("menu");
					menu = [];
					window.sessionStorage.removeItem("curmenu");
				}
			})
		}else{
			layer.msg("没有可以关闭的窗口");
		}
		//渲染顶部窗口
		tab.tabMove();
	})
	//关闭全部
	$(".closePageAll").on("click",function(){
		if($("#top_tabs li").length > 1){
			$("#top_tabs li").each(function(){
				if($(this).attr("lay-id") != ''){
					element.tabDelete("bodyTab",$(this).attr("lay-id")).init();
					window.sessionStorage.removeItem("menu");
					menu = [];
					window.sessionStorage.removeItem("curmenu");
				}
			})
		}else{
			layer.msg("没有可以关闭的窗口");
		}
		//渲染顶部窗口
		tab.tabMove();
	})
})

//打开新窗口
function addTab(_this){
	tab.tabAdd(_this);
}

var isShowLock = false;
function lock($, layer) {
	
	
    if (isShowLock)
        return;
    //自定页
    layer.open({
        title: false,
        type: 1,
        closeBtn: 0,
        anim: 6,
        content: $('#lock-temp').html(),
        shade: [0.9, '#393D49'],
        success: function (layero, lockIndex) {
    	
            isShowLock = true;
           
            //给显示用户名赋值
           
            layero.find('input[name=lockPwd]').on('focus', function () {
                var $this = $(this);
                if ($this.val() === '输入密码解锁..') {
                    $this.val('').attr('type', 'password');
                }
            })
                .on('blur', function () {
                    var $this = $(this);
                    if ($this.val() === '' || $this.length === 0) {
                        $this.attr('type', 'text').val('输入密码解锁..');
                    }
                });
           
            //绑定解锁按钮的点击事件
            layero.find('button#unlock').on('click', function () {
                var $lockBox = $('div#lock-box');

                var userName = $lockBox.find('div#lockUserName').text();
                var pwd = $lockBox.find('input[name=lockPwd]').val();
                if (pwd === '输入密码解锁..' || pwd.length === 0) {
                    layer.msg('请输入密码..', {
                        icon: 2,
                        time: 1000
                    });
                    return;
                }
                unlock(userName, pwd);
            });
			/**
			 * 解锁操作方法
			 * @param {String} 用户名
			 * @param {String} 密码
			 */
            var unlock = function (un, pwd) {
                //这里可以使用ajax方法解锁
            
				$.post(lockurl,{username:un,password:pwd},function(data){
					
				 	//验证成功
					if(data.code==1){
						window.sessionStorage.setItem("lockcms",false);
						//关闭锁屏层
						layer.close(lockIndex);
					}else{
						layer.msg('密码输入错误..',{icon:2,time:1000});
					}					
				},'json');
				
               
                //演示：默认输入密码都算成功
                //关闭锁屏层
                //layer.close(lockIndex);
            };
        }
    });
};
