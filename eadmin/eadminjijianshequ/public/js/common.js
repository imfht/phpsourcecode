
$(function(){

	
	
	
	$('#reader-evaluate-content-wrap .value-star').hover(
	function(){
		$('.value-star').removeClass('ic-star-t-off').addClass('ic-star-t-on');
		$(this).nextAll().removeClass('ic-star-t-on').addClass('ic-star-t-off');
		
		if($('#total-star').find('.ic-star-t-on').length==1){
			$('#doc-evaluate-tips').html('很差');
		}
		if($('#total-star').find('.ic-star-t-on').length==2){
			$('#doc-evaluate-tips').html('较差');
		}
		if($('#total-star').find('.ic-star-t-on').length==3){
			$('#doc-evaluate-tips').html('还行');
		}
		if($('#total-star').find('.ic-star-t-on').length==4){
			$('#doc-evaluate-tips').html('推荐');
		}
		if($('#total-star').find('.ic-star-t-on').length==5){
			$('#doc-evaluate-tips').html('力荐');
		}
		$('#total-star').attr('data-type',$('#total-star').find('.ic-star-t-on').length);
	},function(){
		
		
	}
	);
	$('#reader-evaluate-content-wrap #total-star').hover(function(){
		//$(this).attr('data-id',1);
	},function(){
		//$(this).attr('data-id',0);
		$('.value-star').removeClass('ic-star-t-on').addClass('ic-star-t-off');
		});
	
	$('.close-btn').click(function(){
		
		layer.close(layer.index);
	});
	
	
	
	
	  $('.statusajaxbtn').click(function(){
		  
		  
		   
		  
		    $.post($(this).data('url'),{sid:$(this).data('id'),type:$(this).data('type')},function(data){
		    
		    	
		    	
		    	
				      if(data.code == 1){
					        
					        layer.msg(data.msg, {icon: 1, time: 1000}, function(){
					          location.reload();
					        });
					      }else{
					        
					        layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
					        
					        
					        
					   }
				
		    	

		    });
		    return false;
		  
	  });
	var uploader;  
	  
	$('.es-tabli li,.es-tabli a').click(function(){
		
		var classname=$(this).data('class');
		if(classname==undefined){
			classname='current';
		}
		
		$(this).addClass(classname).siblings().removeClass(classname);
		var id=$(this).data('id');
		$('#'+id).removeClass('hide').siblings().addClass('hide');
		if('myavatar'==id){
			
			if(uploader==undefined){
				uploader = WebUploader.create({

			        // swf文件路径
			        swf: "__PUBLIC__" + '/js/webuploader/Uploader.swf',
			        
			        // 文件接收服务端。
			        server: $('#flash-upload-wrap').data('url'),
			        auto:true,
			        fileNumLimit:1,
			        //fileSingleSizeLimit:2048000,
			       // chunked:true,
			        // 选择文件的按钮。可选。
			        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
			        pick: '#flash-upload-wrap',

			        // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
			        resize: false
			    });
			    //uploader.on( 'fileQueued', function( file ) {
			       // $('.bd').append( '<div id="' + file.id + '" class="item">' +
			           // '<h4 class="info">' + file.name + '</h4>' +
			           // '<p class="state">等待上传...</p>' +
			        //'</div>' );
			    //});

			    // 文件上传过程中创建进度条实时显示。
			    uploader.on( 'uploadProgress', function( file, percentage ) {
			        
			        //$('.progress').show();
			       // $('#progressbar').css( 'width', percentage * 100 + '%' );
			        //$('#progressbar').html(percentage * 100 + '%' );
			    });
			    uploader.on( 'uploadSuccess', function( file,response ) {

			    	console.log(response);
			    	if(response.code==0){
			    		layer.msg(response.errormsg, {icon: 2, time: 1000}, function(){
			    			uploader.reset();
			            });
			    	}else{
			    		layer.msg('上传成功', {icon: 1, time: 1000}, function(){

			    			 $('#userheadimg').attr('src',response.headpath);
			    			 $('#userhead').val(response.userheadpath);
			            });
			    	}
			    	
			    	
			    });
			    uploader.on( 'uploadError', function( file,reason ) {
			    	console.log(reason);
			    	layer.msg('上传出错', {icon: 2, time: 1000}, function(){
			    		uploader.reset();
			        });
			    });
			    uploader.on( 'error', function(reason ) {
			    	var msg;
			    	layer.msg('上传出错代码'+reason, {icon: 2, time: 1000}, function(){
			    		uploader.reset();
			        });
			    	//if(reason=='F_EXCEED_SIZE'){
			    		//msg='';
			    	//}
			    });

			    uploader.on( 'uploadComplete', function( file ) {
			    	//$('.progress').fadeOut();
			    });
			}
			
			
		}
		
		
		
	});
	  $('.es-mouseover').hover(function(){
		  var id=$(this).data('id'); 
		  $('#'+id).show();
		  
	  },function(){
		  var id=$(this).data('id');
		  $('#'+id).hide();
		  
	  });
		$('.rank-tab li').click(function(){
			$(this).addClass('current').siblings().removeClass('current');
			var id=$(this).data('id');
			$('#'+id).addClass('current').siblings().removeClass('current');
			$('#'+id).removeClass('disabled').siblings().addClass('disabled');
		});
		
		
	  
	  $('.loginajax').click(function(){
		  
		   loading = layer.load(2, {
			      shade: [0.2,'#000']
			    });
		   
		    $.post($('form#loginform').data('url'),$('form#loginform').serialize(),function(data){
		    	
		      if(data.code == 1){
		        layer.close(loading);
		        layer.msg(data.msg, {icon: 1, time: 1000}, function(){
		          location.reload();
		        });
		      }else{
		        layer.close(loading);
		        layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
		        
		        $('#captcha').attr('src',$('#captcha').attr('src')+"?"+Math.random());
		        
		      }
		    });
		    return false;
		  
	  });
	 
	  $('.logoutajax').click(function(){
		 
		  loading = layer.load(2, {
		      shade: [0.2,'#000']
		    });
	    $.get($(this).data('url'),function(data){
	    	
	      if(data.code == 1){
	        layer.close(loading);
	        layer.msg(data.msg, {icon: 1, time: 1000}, function(){
	          location.href=data.url;
	        });
	      }else{
	        layer.close(loading);
	        layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
	        
	       
	        
	      }
	    });
	    return false;
		  
		  
	  });
	  
})
//插入固定在右边的悬浮按钮
$(function () {
	
	
	
	
    var html = '<div class="fixed_box">'
           
           
          + '<a class="to_top"><label></label></a>'
            + '</div>';
    $(".body-container").append(html);
    var wWidth = $(window).width();
    var wHeight = $(window).height();
    var imgLeft = 0;
    var scrollTop = 0;
    if ($(this).scrollTop() > 200) {
        $(".fixed_box .to_top").show()
    } else {
        $(".fixed_box .to_top").hide()
    }
    $(window).scroll(function () {
        scrollTop = $(this).scrollTop()
        if ($(this).scrollTop() > 200) {
            $(".fixed_box .to_top").fadeIn()
        } else {
            $(".fixed_box .to_top").fadeOut()
        }
    })

    $(".fixed_box").css("bottom", "10px")
    if (wWidth < 980) {
        imgLeft = (wWidth - 980) - 140;
    } else {
        imgLeft = (wWidth - 980) / 2 - 140
    }
    $(".fixed_box").css("right", imgLeft);
    $(window).resize(function () {
        wWidth = $(window).width();
        wHeight = $(window).height();
        if (wWidth < 980) {
            imgLeft = (wWidth - 980) - 140;
        } else {
            imgLeft = (wWidth - 980) / 2 - 140;
        }
        $(".fixed_box").css("bottom", "100px")
        $(".fixed_box").css("right", imgLeft);
    })

    $(".official").hover(function () {
        $(".official_pop").stop(true, true).fadeIn();
    }, function () {
        $(".official_pop").stop(true, true).fadeOut();
    })
    //	回到顶部
    $(".to_top").click(function () {
        $('body,html').animate({
            scrollTop: 0
        }, 200);
    })
})

//处理图片的宽高
function setImgWH(obj) {
    if (obj.width > obj.height) {
        $(obj).css('height', '100%');
    } else {
        $(obj).css('width', '100%');
    }
}

$(function () {
    //弹窗
    var H_ture = $(".switchbox .content").height();
    var flag = true;
    if (H_ture > 36) {
        $(".switchbox .content").css("height", "36px")
        $(".slideBtn a").click(function () {
            if (flag) {
                $(".switchbox .content").animate({
                    "height": H_ture
                })
                $(this).addClass("active")
            } else {
                $(".switchbox .content").animate({
                    "height": "36px"
                })
                $(this).removeClass("active")
            }
            flag = !flag;
        })
    }
    
})

$(function () {
    //图片变大变小
    $(".imgScale").on('mouseenter', '.content', function () {
        $(this).find("img").addClass("scale").removeClass("scale_return")
    });
    $(".imgScale").on('mouseleave', '.content', function () {
        $(this).find("img").addClass("scale_return").removeClass("scale")
    });
})

