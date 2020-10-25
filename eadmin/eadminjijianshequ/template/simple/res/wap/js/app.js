	 	$(document).on('pageInit', function(e,id,page) {

	 	if(id=='mainhtml'&&$("#hottopiclist").text()==''){
	 	
	 		gethottopiclist(1,false);//有条件的加载
	 	}
	 	
		$('.hrefa').click(function(){
			
			var url = $(this).data('url');
			$.router.loadPage(url);
			
		});	
	 		//
	 	});
	 	
	 	 function debounce(fn, delay) {

			  // 定时器，用来 setTimeout
			  var timer
			 // current_page = current_page+1;
			  // 返回一个函数，这个函数会在一个时间区间结束后的 delay 毫秒时执行 fn 函数
			  return function () {

			    // 保存函数调用时的上下文和参数，传递给 fn
			    var context = this
			    var args = arguments

			    // 每次这个返回的函数被调用，就清除定时器，以保证不执行 fn
			    clearTimeout(timer)

			    // 当返回的函数被最后一次调用后（也就是用户停止了某个连续的操作），
			    // 再过 delay 毫秒就执行 fn
			    timer = setTimeout(function () {
			    	fn.apply(context, args);
			    }, delay)
			  }
		}
	function showPassword(name,obj){
	    $('#'+name).attr('type','text');
	    $('#'+name+'icon').removeClass('icon-biyan');
	    $('#'+name+'icon').addClass('icon-yanjing');
	   $(obj).attr('onclick',"hidePassword('"+name+"',this);");
	   
	 //   api.parseTapmode();
	}
	function hidePassword(name,obj){
		$('#'+name).attr('type','password');
		  $('#'+name+'icon').removeClass('icon-yanjing');
		    $('#'+name+'icon').addClass('icon-biyan');
		   $(obj).attr('onclick',"showPassword('"+name+"',this);");
		   
		   
	 
	   //api.parseTapmode();
	}
	

	
		

	
	