$(function(){
	$.pjax.defaults.timeout = 5000;	//超时5秒(可选)
	$.pjax.defaults.maxCacheLength = 0;	//最大缓存长度(可选)
	$(document).pjax('a:not(a[target="_blank"])', {	//
		container:'#pjax-container',	//存储容器id
		fragment:'#pjax-container'	//目标id
	});

	$(document).on('pjax:click', function() {
		layer.load(2);  //加载状态
	  	$('.content-wrapper .content').x_open_dh('fadeOut');
	})
	$(document).on('pjax:beforeReplace', function(a,d) {
		$(d[1]).x_open_dh('fadeIn');
		layer.closeAll('loading'); //关闭加载层
	})

	/*返回顶部*/
	$(window).scroll(function(){
	    var sc=$(window).scrollTop();
	    var rwidth=$(window).width()+$(document).scrollLeft();
	    var rheight=$(window).height()+$(document).scrollTop();
	    if(sc>100){
	    	$("#goTop").removeClass("fadeOutDown x-yc");
	        $("#goTop").addClass("bounceInUp");
	    }else{
	        $("#goTop").removeClass("bounceInUp");
	        $("#goTop").addClass("fadeOutDown");
	    }
	});
	$("#goTop").click(function(){
	    $('body,html').animate({scrollTop:0},300);
	});
	/*返回顶部*/


    //提交
    $('body').off('click', '.submits');
    $('body').on("click", '.submits', function(event){
        var _this = $(this);
        _this.button('loading');
        var form = _this.closest('form');
        if(form.length){
            var ajax_option={
                dataType:'json',
                success:function(data){
//              	console.log(data);
                    if(data.status == '1'){
                        layer.msg(data.info,function(){
                        	_this.button('reset');
                        	var url = data.url;
                        	$.pjax({url: url, container: '#pjax-container', fragment:'#pjax-container'});
                        });
                    }else if(data.status == '2'){	//要登录
                        layer.msg(data.info,function(){
                        	_this.button('reset');
                        	poplogin(type='login');
                        });
                    }else if(data.status == '3'){	//要刷新页面
                        layer.msg(data.info,function(){
                        	_this.button('reset');
                        	location.reload();
                        });
                    }else{
                        _this.button('reset');
                        layer.msg(data.info);
                    }
                }
            }
            form.ajaxSubmit(ajax_option);
        }
    });

    //弹窗
    $('body').off('click', '.popups');
    $('body').on("click", '.popups', function(event){
        event.preventDefault();
        var _this = $(this),
        	dataobj = _this.data('dataobj');

        var title = dataobj.title ? dataobj.title : '提示';
        var urls = dataobj.url||'';
        var info = dataobj.info ? dataobj.info :'确认操作？';
        var datas = dataobj.data||'';
        if(datas && urls){
            BootstrapDialog.confirm({
                title: title,
                message: info,
                btnCancelLabel: '取消',
                btnOKLabel: '确定',
                callback: function(resultDel) {
                    if(resultDel === true) {
                        $.ajax({
                            type : "post",
                            url : urls,
                            dataType : 'json',
                            data : datas,
                            success : function(data) {
                                if(data.status == '1'){
                                    layer.msg(data.info,function(){
                                    	var url = data.url;
                                    	$.pjax({url: url, container: '#pjax-container', fragment:'#pjax-container'});
                                    });
                                }else if(data.status == '2'){
                                    poplogin('login');
                                }else if(data.status == '3'){
                                	layer.msg(data.info,function(){
				                    	location.reload();
				                    })
                                }else{
                                    layer.msg(data.info);
                                }
                            }
                        });
                    }
                }
            });
        }else{
        	layer.msg('DATA参数或URL不正确');
        }
    });

	//打开/隐藏
	$(document).on('click','.open-btn',function(){
		var ejihuifuid =  $(this).data('id');
		$(ejihuifuid).slideToggle('slow');
	})
	//鼠标划过图片动画
	$(document).on('mouseenter','img',function(){
		$(this).x_open_dh('pulse');
	})
	//鼠标划过加阴影
	$(document).on('mouseover','.huaguo_yinying',function(){
		$(this).addClass('x-yy-5');
	})
	$(document).on('mouseout','.huaguo_yinying',function(){
		$(this).removeClass('x-yy-5');
	})
	//点击注册/登录
	$(document).on('click','.goOpen',function(){
		$('.DlZck').collapse('toggle');
	})
	$(document).on('click','#goLoginBtn',function(){
		var dlk = $('#dlk').attr('aria-expanded');
		if( dlk != 'undefined'){
			if( dlk == 'false' ){
				$('.goOpen').click();
			}
		}
	})


})

//弹窗登录
function poplogin(type='login'){
	var k = $('#goLoginBtn').attr('aria-expanded');
	var dlk = $('#dlk').attr('aria-expanded');
	if(type == 'login'){
		if( k == 'true' ){
			if( dlk != 'undefined'){
				if( dlk == 'false' ){
					$('.goOpen').click();
				}
			}
		}else{
			$('#goLoginBtn').click();
			$('#goLoginBtn').x_open_dh('shake');
			$('.opendonghua').x_open_dh('shake');
			if( dlk != 'undefined'){
				if( dlk == 'false' ){
					$('.goOpen').click();
				}
			}
		}
	}else{
		var zck = $('#zck').attr('aria-expanded');
		if( k == 'true' ){
			if( dlk == 'undefined'){
				if( zck == 'false' ){
					$('.goOpen').click();
				}
			}
		}else{
			$('#goLoginBtn').x_open_dh('shake');
			$('.opendonghua').x_open_dh('shake');
			$('#goLoginBtn').click();
			if( dlk == 'undefined'){
				if( zck == 'false' ){
					$('.goOpen').click();
				}
			}
		}
	}
}

