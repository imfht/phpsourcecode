$(function(){
    //菜单点击,超链接标签中 class 属性中有 J_menuItem 值的标签，将被拦截，超链接所指向的内容将被加载到 id 为 J_iframe 标签中。
    //J_iframe
    $(".J_menuItem").on('click',function(){
        var url = $(this).attr('href');// 获取当前标签 href 属性的值
        $("#J_iframe").attr('src',url);// id 为 J_iframe 的标签将加载该 url 内容
        return false;// 超链接标签本身不在加载该链接内容
    });
    
    // 每次加载外围页面框架时，都要加载一次未读的通知信息
    $.ajax({  
        url: '/admin/message/number',  // 请求的url
        data: {},  //请求携带的数据
        dataType: 'text',  // 请求携带的数据类型
        type: 'GET',   // 请求的方法
         /* 请求成功时的回调方法,当返回 200 状态码时，
          *data的值是返回的数据，而status 的值为 success
          */
        success: function(data,status){
        	if(data != 0){
        		$('#messageInfo').text(data);
        	}
        },  
        error: function(){},  // 请求出错时的回调方法
        complete: function(){}  
    });
    
    // 每次加载外围页面框架时，都要加载一次未读的待处理的物资申请信息数量
    $.ajax({  
    	url: '/admin/material/purchase/apply/counts',  // 请求的url
    	data: {},  //请求携带的数据
    	dataType: 'text',  // 请求携带的数据类型
    	type: 'GET',   // 请求的方法
    	/* 请求成功时的回调方法,当返回 200 状态码时，
    	 *data的值是返回的数据，而status 的值为 success
    	 */
    	success: function(data,status){
    		if(data == 0){
    			$('#purchaseInfo').hide();
    		}else{
    			$('#purchaseInfo').text(data);
    		}
    	},  
    	error: function(){},  // 请求出错时的回调方法
    	complete: function(){}  
    });
    // 每次加载外围页面框架时，都要加载一次未读的待处理的物资维修信息数量
    $.ajax({  
    	url: '/admin/material/repaire/wait/counts',  // 请求的url
    	data: {},  //请求携带的数据
    	dataType: 'text',  // 请求携带的数据类型
    	type: 'GET',   // 请求的方法
    	/* 请求成功时的回调方法,当返回 200 状态码时，
    	 *data的值是返回的数据，而status 的值为 success
    	 */
    	success: function(data,status){
    		if(data == 0){
    			$('#repaireInfo').hide();
    		}else{
    			$('#repaireInfo').text(data);
    		}
    	},  
    	error: function(){},  // 请求出错时的回调方法
    	complete: function(){}  
    });
    // 每次加载外围页面框架时，都要加载一次未读的待处理的物资维修信息数量
    $.ajax({  
    	url: '/admin/material/deliver/wait/counts',  // 请求的url
    	data: {},  //请求携带的数据
    	dataType: 'text',  // 请求携带的数据类型
    	type: 'GET',   // 请求的方法
    	/* 请求成功时的回调方法,当返回 200 状态码时，
    	 *data的值是返回的数据，而status 的值为 success
    	 */
    	success: function(data,status){
    		if(data == 0){
    			$('#deliverInfo').hide();
    		}else{
    			$('#deliverInfo').text(data);
    		}
    	},  
    	error: function(){},  // 请求出错时的回调方法
    	complete: function(){}  
    });
});
