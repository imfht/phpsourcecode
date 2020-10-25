// 拦截归还的 get请求
	$(".myrequest").on('click',function(){
	    var rentUrl = $(this).attr('href');// 获取当前标签 href 属性的值
	    var alink = $(this);
	    $.ajax({  
	        url: rentUrl,  // 请求的url
	        data: {},  //请求携带的数据
	        dataType: 'text',  // 请求携带的数据类型
	        type: 'get',   // 请求的方法
	        success: function(data,status){
	            layer.msg('信息提交成功 ！');
	            alink.parent().parent().hide();
	            },  
	        error: function(){
	        	layer.msg('信息提交失败 ！');
	        },  // 请求出错时的回调方法
	        complete: function(){}  
	    });
	   // $(this).parent().parent().hide();
	    return false;// 超链接标签本身不在加载该链接内容
	});