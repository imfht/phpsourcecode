



$.fn.extend({
	
	 insertAtCursor : function(myValue) {
	      var $t = $(this)[0];
		             if (document.selection) {
		                this.focus();
		                sel = document.selection.createRange();
		                sel.text = myValue;
		                this.focus();
		            } else if ($t.selectionStart || $t.selectionStart == '0') {
		                var startPos = $t.selectionStart;
		                var endPos = $t.selectionEnd;
		                 var scrollTop = $t.scrollTop;
		                 $t.value = $t.value.substring(0, startPos) + myValue + $t.value.substring(endPos, $t.value.length);
		                 this.focus();
		                 $t.selectionStart = startPos + myValue.length;
		                 $t.selectionEnd = startPos + myValue.length;
		                 $t.scrollTop = scrollTop;
		             } else {
		                 this.value += myValue;
		                 this.focus();
		             }
		         },
	formAjax:function(){
		return this.each(function(){
			var $this = $(this);
			
			$this.submit(function(event){
				event.preventDefault();
				
				
				if($this.attr("editor")=='zswineditor'){
					//tinyMCE.triggerSave();
					
					$this.find('#content-editor').html($this.find('#content-editor').code());
				}
				var url = $this.attr("action"),data=$this.serialize();
				
				
				$this.find('button[type="submit"]').attr('disabled','true');
				  $.post(url, data,  function(data) {
					
	                    if (data.status) {
	                    	if(data.url==''){
	                    		layer.statusinfo(data.info,'success',function(){location.reload()});
	                    	}else{
	                    		layer.statusinfo(data.info+"&nbsp;&nbsp;2秒后跳转",'success',urllocation,data.url);
	                    	}
	                    	
	                    	
	                       
	                    } else {
	                    	$this.find('button[type="submit"]').removeAttr('disabled');
	                    	layer.statusinfo(data.info,'error');
	                    	
	                    	if($this.attr("verify")==1){
	                    		changeverify();
	                    	}
	                        
	                    }
	                }, "json"
	                );
				 
			});
			
		});
	},
	AjaxTodo:function(){
		return this.each(function(){
			var $this = $(this);
			
			$this.click(function(event){
				event.preventDefault();
				
				
				var url = $this.attr("href");
				var before = $this.attr("before");
				var title = $this.attr("title");
				var jumpurl=$this.attr("jumpurl");
				
				if (title) {
					layer.confirm(title,function(){
						
							ajaxTodo(url, $this.attr("callback"),before,jumpurl);
						}
					);
				} else {
					ajaxTodo(url, $this.attr("callback"),before,jumpurl);
				}
				 
			});
			
		});
	}
});

function initUI(_box){

	var $p = $(_box || document);
	
if ($.fn.formAjax) $("form[target=formAjax]", $p).formAjax();
if ($.fn.AjaxTodo) $("a[target=AjaxTodo]", $p).AjaxTodo();





//form添加noEnter属性，禁止文本框回车提交
$('form[noEnter]', $p).each(function() {
    $(':text', $(this)).keypress(function(e) {
        var key = e.which;
         if(key == 13)
            return false;
    });
});
$('textarea.autosize', $p).autosize();
$('#cavatar',$p).bind({
	
	//mouseenter:function () {$('.avatarpos').animate({width: "78px",height:"78px"}, 2000 );},
	//mouseleave:function () {$('.avatarpos').animate({width: "0px",height:"78px"}, 2000 );}
	mouseenter:function () {$('.avatarpos').fadeIn();},
	mouseleave:function (){$('.avatarpos').fadeOut();}
});
//bootstrap - tags
if ($.fn.tags) {
    $(".tags-control", $p).each(function() {
        var $this = $(this);
        
        var url   = $this.data('url'),
            inputval   = $this.data('inputval') || '',
            type  = $this.data('type') || 'GET',
            param = $this.data('parametername') || 'tagName',
            max   = $this.data('max') || 0,
            clear = $this.data('clearnotfound') || false;
        $this.tags({
            url: url,
            inputval:inputval,
            type: type,
            parameterName: param,   // 生成的<input type='hidden' />的name属性
            max: max,              // 允许的最大标签个数(0=不限)
            clearNotFound: clear   // 是否清除未查找到的输入字符
        });
    });
}
//根据input[text|password]、textarea的size或cols属性固定宽度(以适应不同浏览器)
$(':text, :password, textarea', $p).each(function() {
    var $this = $(this);
    var $itemDetail = $this.closest('table.itemDetail');
    if (!$itemDetail.length) {
        var size = $this.attr('size') || $this.attr('cols');
        if (!size) return;
        var width = size * 10;
        if (width) $this.css('width', width);
    }
});

//$('.hiddenmenu').css('left',0-$('.hiddenmenu').width()+'px');
////$('.navbar-header').click(

//function(){
	//if(parseInt($('.hiddenmenu').css('left'))<0){
	//	$('.hiddenmenu').animate({'left':'0px'});	
	//}else{
		
	//	$('.hiddenmenu').animate({'left':'-300px'});	
	//}
		

//}




//);

//$('.hiddenmenu a').click(

		//function(){
			
				
			//	$('.hiddenmenu').animate({'left':0-$('.hiddenmenu').width()+'px'});	
			
				

		//}




	//	);


$('#get_verify_email').find('.get').click(function(){
	var url = $(this).attr("href");
	
	$('#get_verify_email').find('.get').hide();
	$('#get_verify_email').find('.wait').show();
	
	var mail=$('#email').val();
	
	if(mail==''){
		layer.statusinfo('邮箱为空','error');
		$('#get_verify_email').find('.wait').hide();
		$('#get_verify_email').find('.get').show();
	}else{
		
		$.post(url, {to:mail,type:1},  function(data) {
			$('#get_verify_email').find('.wait').hide();
			$('#get_verify_email').find('.get').show();
	         if (data.status) {
	         	
	         		layer.statusinfo(data.info,'success');
	         	
	         	
	            
	         } else {
	         	layer.statusinfo(data.info,'error');
	         	
	            
	             
	         }
	     }, "json"
	     );
	}
});

}	

function changemail(obj){
	//个人资料中修改邮箱用到此函数
	$("#email").removeAttr("disabled");
	$(obj).hide();
	$('#get_verify_email').find('.get').show();
	
}
function urllocation(url){
	
	 window.location.href = url;
	
}
function readmail(data){
	
	
	if (data.status) {
		layer.statusinfo(data.info,'success');
    
		$('#mail'+data.id).css('color','');
		$('#readmail'+data.id).hide();
        
     } else {
     	layer.statusinfo(data.info,'error');
     	
        
         
     }
	
	
	
}
function delmail(data){
	
	
	if (data.status) {
		layer.statusinfo(data.info,'success');
    
		
		$('#delmail'+data.id).closest('.stream-list').hide();
		$('#delmail'+data.id).closest('li').hide();
        
     } else {
     	layer.statusinfo(data.info,'error');
     	
        
         
     }
	
	
	
}
function dingcai(data){
	if (data.status) {
		layer.statusinfo(data.info,'success');
     
		var int=$('#'+data.id).text();
		
			$('#'+data.id).text(parseInt(int)+1);	
		
		
        
     } else {
     	layer.statusinfo(data.info,'error');
     	
        
         
     }
	
	
	
}
function focusnum(data){
	if (data.status) {
		layer.statusinfo(data.info,'success');
     
		var int=$('#'+data.mark).text();
		var alt=$('#focusnum'+data.id).attr('alt');
		var text=$('#focusnum'+data.id).text();
		
		
		if(data.del==1){
			$('#focusnum'+data.id).removeClass('btn-default');
			$('#focusnum'+data.id).addClass('btn-success');
			
			
			
			$('#focusnum'+data.id).attr('alt',text);
			$('#focusnum'+data.id).text(alt);
			
			$('#'+data.mark).text(parseInt(int)-1);	
		}else{
			$('#focusnum'+data.id).removeClass('btn-success');
			$('#focusnum'+data.id).addClass('btn-default');
			
			$('#focusnum'+data.id).attr('alt',text);
			$('#focusnum'+data.id).text(alt);
			
			$('#'+data.mark).text(parseInt(int)+1);	
		}
		
        
     } else {
     	layer.statusinfo(data.info,'error');
     	
        
         
     }
	
	
	
}
function focusevent(data){
	if (data.status) {
		layer.statusinfo(data.info,'success');
     
		var int=$('#focusevent'+data.id).text();
		var alt=$('#focusevent'+data.id).attr('alt');
		var text=$('#focusevent'+data.id).text();
		
		
		
		if(data.del==1){
			$('#focusevent'+data.id).removeClass('btn-default');
			$('#focusevent'+data.id).addClass('btn-success');
			$('#focusevent'+data.id).attr('alt',text);
			$('#focusevent'+data.id).text(alt);
			
			//$('#focusevent'+data.id).text('加关注');	
		}else{
			$('#focusevent'+data.id).removeClass('btn-success');
			$('#focusevent'+data.id).addClass('btn-default');
			$('#focusevent'+data.id).attr('alt',text);
			$('#focusevent'+data.id).text(alt);
			
			//$('#focusevent'+data.id).text('取消关注');	
		}
		
        
     } else {
     	layer.statusinfo(data.info,'error');
     	
        
         
     }
	
	
	
}


function ajaxTodo(url, callback,before,jumpurl){
	
	var $callback = callback || function(data){
		
		 if (data.status) {
			
			if(data.url==''){
                
         		layer.statusinfo(data.info,'success');
         		
         		
         		
         		
         		
         	}else{
         		
                if(jumpurl!=undefined){
         			
         			layer.statusinfo(data.info+"&nbsp;&nbsp;2秒后跳转",'success',urllocation,jumpurl);
         			
         		}else{
         			layer.statusinfo(data.info+"&nbsp;&nbsp;2秒后跳转",'success',function(){location.reload()});
         		}
         		
         	}
            
         } else {
         	layer.statusinfo(data.info,'error');
         	
            
             
         }
		
	};
	var $before = before;
	if (! $.isFunction($before)) $before = eval('(' + before + ')');
	
	if (! $.isFunction($callback)) $callback = eval('(' + callback + ')');
	$.ajax({
		type:'POST',
		url:url,
		beforeSend:$before,
		dataType:"json",
		cache: false,
		success: $callback
		
	});
}
function userBrowser(){  //判断浏览器类型
    var browserName=navigator.userAgent.toLowerCase();  
    if(/msie/i.test(browserName) && !/opera/.test(browserName)){  
       
        return "IE";  
    }else if(/firefox/i.test(browserName)){  
       
        return "Firefox";  
    }else if(/chrome/i.test(browserName) && /webkit/i.test(browserName) && /mozilla/i.test(browserName)){  
        
        return "Chrome";  
    }else if(/opera/i.test(browserName)){  
       
        return "Opera";  
    }else if(/webkit/i.test(browserName) &&!(/chrome/i.test(browserName) && /webkit/i.test(browserName) && /mozilla/i.test(browserName))){  
        
        return "Safari";  
    }else{  
       
        return "unKnow";  
    }  
} 
function useIEversion(){  //判断ie的版本
    var browserName=navigator.userAgent.toLowerCase();  
    if(!$.support.opacity&&!$.support.style&&window.XMLHttpResquest==undefined){  
       
        return "6";  
    }else if(!$.support.opacity&&!$.support.style&&window.XMLHttpResquest!=undefined){  
       
        return "7";  
    }else if(!$.support.opacity&&$.support.style&&window.XMLHttpResquest!=undefined){  
       
        return "8";  
    }else{  
       
        return "unKnow";  
    }  
}
