$.fn.extend({
	formAjax:function(){
		return this.each(function(){
			var $this = $(this);
			
			$this.submit(function(event){
				event.preventDefault();
				
				
				var url = $this.attr("action"),data=$this.serialize();
				
				  $.post(url, data,  function(data) {
					
					 
	                    if (data.status) {
	                    	if(data.url==''){
	                    		layer.statusinfo(data.info,'success',function(){location.reload()});
	                    	}else{
	                    		layer.statusinfo(data.info+"&nbsp;&nbsp;2秒后跳转",'success',urllocation,data.url);
	                    	}
	                    	
	                    	
	                       
	                    } else {
	                    	layer.statusinfo(data.info,'error');
	                    	
	                       
	                        
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
				var title = $this.attr("title");
				if (title) {
					layer.confirm(title,function(index){
						layer.close(index);
						
						AjaxTodo(url, $this.attr("callback"));
						}
					);
				} else {
					AjaxTodo(url, $this.attr("callback"));
				}
				
			});
		});
	},

	dwzExport: function(){
		function _doExport($this) {
			
			var $form = $("#formsearch");
			var url = $this.attr("href");
			window.location = url+(url.indexOf('?') == -1 ? "?" : "&")+$form.serialize();
		}
		
		return this.each(function(){
			var $this = $(this);
			$this.click(function(event){
				var title = $this.attr("title");
				if (title) {
					alertMsg.confirm(title, {
						okCall: function(){_doExport($this);}
					});
				} else {_doExport($this);}
			
				event.preventDefault();
			});
		});
	},
	checkedExport: function() {
        function _doCheckedExport($this) {
          
            var url = $this.attr('href'),
                idname = $this.attr('idname');
            if (!idname) {
            	alertmessage('error','未定义选中项的id名称[复选框的name]！','','',3);
                
                return;
            }
            var ids = [];
            var $check = $(':checkbox[name='+ idname +']:checked');
            if ($check.length == 0) {
            	alertmessage('error','未选中任何一项！','','',3);
            
                return;
            }
            var ids = [];
            $check.each(function() {
                ids.push($(this).val());
            });
            window.location = url + (url.indexOf('?') == -1 ? '?' : '&') + 'ids='+ ids.join(',');
        }
        return this.each(function(){
            var $this = $(this);
            $this.click(function(event){
                var title = $this.attr('title');
                
                if (title) {
					layer.confirm(title,function(index){
						layer.close(index);
						
						_doCheckedExport($this);
						}
					);
				} else {
					_doCheckedExport($this);
				}
               
                event.preventDefault();
            });
        });
    },
    
    checkedAjaxTodo:function(){
        return this.each(function(){
            var $this = $(this);
            
            $this.click(function(event){
                var url = $this.attr("href"),
                    idname = $this.attr('idname');
                if (!idname) {
                	alertmessage('error','未定义选中项的id名称[复选框的name]！','','',3);
                   
                    return false;
                }
                var ids = [];
                var $check = $(':checkbox[name='+ idname +']:checked');
                if ($check.length == 0) {
                    
                    alertmessage('error','未选中任何一项！','','',3);
                    return false;
                }
                var ids = [];
                $check.each(function() {
                    ids.push($(this).val());
                });
                url = url + (url.indexOf('?') == -1 ? '?' : '&') + 'ids='+ ids.join(',');
                var title = $this.attr("title");
                if (title) {
					layer.confirm(title,function(index){
						layer.close(index);
						
						AjaxTodo(url, $this.attr("callback"));
						}
					);
				} else {
					AjaxTodo(url, $this.attr("callback"));
				}
              
                event.preventDefault();
            });
        });
    }
});
function ajaxform(index,obj){
	
	
	var url = $(obj).attr("action"),datax=$(obj).serialize();
	
	
	  $.post(url, datax,  function(data) {
		 
		  if (data.statusCode=='200') {
              layer.close(index);
              
            	  alertmessage('success',data.message,'','',2,'','','',function(){window.location.reload();});  
              
        	  
             
          } else {
          	
        	  alertmessage('error',data.message,'','',3);
             
              
          }
      }, "json"
      );
	
	
	
	
}
function zsalert(type,text,closetype,layout,time,onShow,afterShow,onClose,afterClose,onCloseClick){
	
	var type=type||'success',
	    text=text||'nothing',
	    closetype=closetype||'click',
	    layout=layout||'center',
	    time=time||0;
	var icon;
	if(type='info'){
		type='notification';
	}
	
	if(type=='success'){
		icon='<div><i class="glyph-icon icon-check font-size-23"></i> 温馨提示：</div>';
	}
	if(type=='error'||type=='alert'){
		icon='<div><i class="glyph-icon icon-exclamation-triangle font-size-23"></i> 温馨提示：</div>';
	}
	if(type=='notification'){
		icon='<div><i class="glyph-icon icon-info font-size-23"></i> 温馨提示：</div>';
	}
	
	
	
	var n=noty({
		text: icon+'<div>'+text+'</div>', 
		type: type,
		closeWith: [closetype,'backdrop'],
		theme: 'agileUI',
        layout: layout,
        animation: {
            open: {height: 'toggle'}, // or Animate.css class names like: 'animated bounceInLeft'
            close: {height: 'toggle'}, // or Animate.css class names like: 'animated bounceOutLeft'
            easing: 'swing',
            speed: 500 // opening & closing animation speed
        },
        callback: {
            onShow: function() { onShow},
            afterShow: function() {afterShow},
            onClose: function() {onClose},
            afterClose: function() {afterClose},
            onCloseClick: function() {onCloseClick},
        },
	
	});
	
	if(time>0){
		
		
		setTimeout(function(){n.close();},time*1000);
	}
	
	
	
	
}

function alertmessage(type,text,closetype,layout,time,onShow,afterShow,onClose,afterClose,onCloseClick){
	
	 
	   
	var type=type||'success',
    text=text||'nothing',
    closetype=closetype||'click',
    layout=layout||'topcenter',
    time=time||0;
var icon,icontext,icontextcolor;
if(type=='info'){
	type='notification';
}

if(type=='success'){
	icon='<h3 class="content-box-header bg-blue-alt">';
	icontext='<span class=" font-size-18 "><i class="glyph-icon icon-check font-size-23"></i> 操作成功</span></h3>';
	icontextcolor='<div class="content-box-wrapper noty_text font-blue font-bold">';
}
if(type=='error'||type=='alert'){
	icon='<h3 class="content-box-header bg-red">';
	icontext='<span class=" font-size-18 "><i class="glyph-icon icon-exclamation-triangle font-size-23"></i> 操作失败</span></h3>';
	icontextcolor='<div class="content-box-wrapper noty_text font-red font-bold">';
}
if(type=='notification'){
	icon='<h3 class="content-box-header primary-bg">';
	icontext='<span class=" font-size-18 "><i class="glyph-icon icon-info font-size-23"></i> 信息提示 </span></h3>';
	icontextcolor='<div class="content-box-wrapper noty_text font-black font-bold">';
}

var html=icon;
html +=icontext;
html +=icontextcolor;
html +='</div>';        
    
   



    



var n=noty({
	text: text, 
	type: type,
	closeWith: [closetype,'backdrop'],
	theme: 'agileUI',
	template: html,
    layout: layout,
    animation: {
        open: {height: 'toggle'}, // or Animate.css class names like: 'animated bounceInLeft'
        close: {height: 'toggle'}, // or Animate.css class names like: 'animated bounceOutLeft'
        easing: 'swing',
        speed: 300 // opening & closing animation speed
    },
    callback: {
        onShow: function() { onShow},
        afterShow: function() {afterShow},
        onClose: function() {onClose},
        afterClose: afterClose,
        onCloseClick: function() {onCloseClick},
    },

});

if(time>0){
	
	
	setTimeout(function(){n.close();},time*1000);
}
	
	
	
	
}


function AjaxDone(data){
	
	
	
	if(data.statusCode=='200'){
		
		
		layer.closeAll();
		alertmessage('success',data.message,'','',2,'','','',function(){window.location.reload();});
	}else{
		alertmessage('error',data.message,'','',3);
	}
	
	
	
	
}
function TabAjaxDone(data,url){
	
	
	
	if(data.statusCode=='200'){
		
		
	
		alertmessage('success',data.message,'','',2,'','','',function(){window.location=url});
	}else{
		alertmessage('error',data.message,'','',3);
	}
	
	
	
	
}
function AjaxError(data){
	
	if(data.status=='200'){
		
	}else{
		
	}
	
	
	
	
}

function clearQuery(obj){
	var url=$('#formsearch').attr('action');
	//$('input').val('');
	//$('select').val('');
	//var data=$('#formsearch').serialize();
	//alert(data);
	window.location=url;
		//$('#formsearch').submit();
	
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
function AjaxTodo(url, callback){
	
	var $callback = callback || AjaxDone;
	if (! $.isFunction($callback)) $callback = eval('(' + callback + ')');
	$.ajax({
		type:'POST',
		url:url,
		dataType:"json",
		cache: false,
		success: $callback,
		error: AjaxError
	});
}
function str2Func(method) {
    if (!method || method.length == 0) return undefined;
    if ($.isFunction(method)) return method;
    var m_arr = method.split('.');
    var fn    = this;
    for (var i = 0; i < m_arr.length; i++) {
        fn = fn[m_arr[i]];
    }
    if (typeof fn === "function") {
        return fn;
    }
    return undefined;
}
function layoutFormatter (){

    /* Layout Formatter */

    setTimeout(function() {

        var windowH = $(window).height();
        var docH = $(document).height();

        var documentH = $('#page-main').height();
        
        var headerH = $('#header-logo').height();
        var searchH = $('#sidebar-search').height();
        var sidebarH = $('#page-sidebar').height();

        var menuHeight = windowH - headerH - searchH - 21;

        if ( $( 'body' ).hasClass( "fixed-sidebar" ) ) {

            $('#page-sidebar').height(windowH);
            $("#sidebar-menu").height(menuHeight);

          } else {

            $("#page-sidebar").css("min-height", documentH);

          }

        if ( sidebarH > documentH ) {

            $('#page-main').height(sidebarH);

        }

    }, 499);

  };
  $(window).resize(function(){

	  layoutFormatter();

	});
  function validateCallback(form, callback) {
		var $form = $(form);
		
		
		
		var $callback = callback;
		
	if($callback=='TabAjaxDone'){
		var _submitFn = function(){
			$.ajax({
				type: form.method || 'POST',
				url:$form.attr("action"),
				data:$form.serializeArray(),
				dataType:"json",
				cache: false,
				success: function(data){
					var url=$form.attr('url');
					
					TabAjaxDone(data,url);
					
				},
				error: AjaxError
			});
		}
		
		
		
		
	}else{
		
		var _submitFn = function(){
			$.ajax({
				type: form.method || 'POST',
				url:$form.attr("action"),
				data:$form.serializeArray(),
				dataType:"json",
				cache: false,
				success: function(data){
					
					
					AjaxDone(data);
					
				},
				error: AjaxError
			});
		}
		
		
		
		
	}
		
		
		
		
			_submitFn();
			
		
		return false;
	}
  function validateform(){
	  
	  //
	    $("form.form-validate").each(function() {
	        var $this       = $(this);
	        var overflowDIV = $this.attr('overflowDIV') || '.form-validate';
	        var callback    = $this.attr('callback') || 'AjaxDone';
	    
	      
	        
	        $this.validationEngine({
	            isOverflown: true,
	            overflowDIV: overflowDIV,
	            promptPosition: 'Right',
	            onValidationComplete: function(form, valid) {
	        	
	                if (valid) {
	                	
	                    return validateCallback(form, callback);
	                } else {
	                    return false;
	                };
	            }
	        });
	    });
	  
	  
	  
  }
  
  function setactive(id){
	  $('#sidebar-menu li').removeClass('current-page');
	  $('#sidebar-menu li').removeClass('active');
	  $('a[zs-id='+id+']').closest('li').addClass('current-page');
	  $('a[zs-id='+id+']').closest('li.sub-menu').addClass('active');
	
  }
  
  
  
  
function initUI(){
	
	validateform();
	
	layoutFormatter();
	  $(".scrollable-content").niceScroll({
	      cursorborder: "transparent solid 2px",
	      cursorwidth: "4",
	      cursorcolor: "#363636",
	      cursoropacitymax: "0.4",
	      cursorborderradius: "2px"
	    });
	  $(".dataTables_scrollBody").niceScroll({
	      cursorborder: "transparent solid 2px",
	      cursorwidth: "4",
	      cursorcolor: "#363636",
	      cursoropacitymax: "0.4",
	      cursorborderradius: "2px"
	    });
	  $('#responsive-open-menu').click(function(){
		    $('#sidebar-menu').toggle();
		  });
	 $('#sidebar-menu li').click(function(){

		    if($(this).is('.active')) {

		      $(this).removeClass('active');

		      $('ul', this).slideUp();

		    } else {

		      $('#sidebar-menu li ul').slideUp();

		      $('ul', this).slideDown();

		      $('#sidebar-menu li').removeClass('active');

		      $(this).addClass('active');

		    }

		  });
	 var url = window.location;

	  $('#sidebar-menu a[href="'+ url +'"]').parent('li').addClass('current-page');

	  $('#sidebar-menu a').filter(function() {
	    return this.href == url;
	  }).parent('li').addClass('current-page').parent('ul').slideDown().parent().addClass('active');
	  
	 $('#close-sidebar').click(function(){

		    $('body').addClass('close-sidebar');
		    closeSidebarCookie = $.cookie('closesidebar', 'close');
		    $(this).addClass('hidden');
		    $('#rm-close-sidebar').removeClass('hidden');

		  });

		  $('#rm-close-sidebar').click(function(){

		    $('body').removeClass('close-sidebar');
		    closeSidebarCookie = $.cookie('closesidebar', 'rm-close');
		    $(this).addClass('hidden');
		    $('#close-sidebar').removeClass('hidden');

		  });

		  var closeSidebarCookie = $.cookie('closesidebar');

		  if (closeSidebarCookie == 'close') {
		    $('#close-sidebar').addClass('hidden');
		    $('#rm-close-sidebar').removeClass('hidden');
		    $('body').addClass('close-sidebar');
		  }
	 
	if ($.fn.formAjax) $("form[target=formAjax]").formAjax();
	
	if ($.fn.AjaxTodo) $("a[target=AjaxTodo]").AjaxTodo();	
	if ($.fn.checkedExport) $("a[target=checkedExport]").checkedExport(); //选中导出
    if ($.fn.checkedAjaxTodo) $("a[target=checkedAjaxTodo]").checkedAjaxTodo(); //选中项ajaxPost
	if ($.fn.dwzExport) $("a[target=dwzExport]").dwzExport();
	 $('.form-row').each(function() {
	        var $this = $(this);
	        var size = $this.attr('size');
	        if (!size) return;
	        var width = size * 10;
	        if (width) $this.css('width', width);
	        
	    });
	//编辑器
    $('textarea.j-content').each(function() {
        var editor = $(this);
            pasteType       = editor.attr('pasteType'),
            uploadJson      = editor.attr('uploadJson'),
            fileManagerJson = editor.attr('fileManagerJson'),
            items           = editor.attr('items'),
            minHeight       = editor.attr('minHeight') || 260,
            autoHeight      = editor.attr('autoHeight'),
            afterUpload     = editor.attr('afterUpload') || null,
            afterSelectFile = editor.attr('afterSelectFile') || null;
           
        if (items) {
            items = items.split(',');
        } else {
            items = KindEditor.options.items;
        }
        if (afterUpload) {
            afterUpload = str2Func(afterUpload);
        }
        if (afterSelectFile) {
            afterSelectFile = str2Func(afterSelectFile);
        }
        
        if (autoHeight && autoHeight != 'true') autoHeight = false;
        var htmlTags = {
            font : [/*'color', 'size', 'face', '.background-color'*/],
            span : ['.color', '.background-color', '.font-size', '.font-family'
                    /*'.color', '.background-color', '.font-size', '.font-family', '.background',
                    '.font-weight', '.font-style', '.text-decoration', '.vertical-align', '.line-height'*/
            ],
            div : ['.margin', '.padding', '.text-align'
                    /*'align', '.border', '.margin', '.padding', '.text-align', '.color',
                    '.background-color', '.font-size', '.font-family', '.font-weight', '.background',
                    '.font-style', '.text-decoration', '.vertical-align', '.margin-left'*/
            ],
            table: ['align', 'width'
                    /*'border', 'cellspacing', 'cellpadding', 'width', 'height', 'align', 'bordercolor',
                    '.padding', '.margin', '.border', 'bgcolor', '.text-align', '.color', '.background-color',
                    '.font-size', '.font-family', '.font-weight', '.font-style', '.text-decoration', '.background',
                    '.width', '.height', '.border-collapse'*/
            ],
            'td,th': ['align', 'valign', 'width', 'height', 'colspan', 'rowspan'
                    /*'align', 'valign', 'width', 'height', 'colspan', 'rowspan', 'bgcolor',
                    '.text-align', '.color', '.background-color', '.font-size', '.font-family', '.font-weight',
                    '.font-style', '.text-decoration', '.vertical-align', '.background', '.border'*/
            ],
            a : ['href', 'target', 'name'],
            embed : ['src', 'width', 'height', 'type', 'loop', 'autostart', 'quality', '.width', '.height', 'align', 'allowscriptaccess'],
            img : ['src', 'width', 'height', 'border', 'alt', 'title', 'align', '.width', '.height', '.border'],
            'p,ol,ul,li,blockquote,h1,h2,h3,h4,h5,h6' : [
                'class', 'align', '.text-align', '.color', /*'.background-color', '.font-size', '.font-family', '.background',*/
                '.font-weight', '.font-style', '.text-decoration', '.vertical-align', '.text-indent', '.margin-left'
            ],
            pre : ['class'],
            hr : ['class', '.page-break-after'],
            'br,tbody,tr,strong,b,sub,sup,em,i,u,strike,s,del' : []
        }
        KindEditor.create(editor, {
            pasteType                : pasteType,
            minHeight                : minHeight,
            autoHeightMode           : autoHeight,
            items                    : items,
            uploadJson               : uploadJson,
            fileManagerJson          : fileManagerJson,
            allowFileManager         : true,
            fillDescAfterUploadImage : false,
            afterUpload              : afterUpload,
            afterSelectFile          : afterSelectFile,
            htmlTags                 : htmlTags,
            cssPath                  : _PUBLIC_+'/admin/css/editor-content.css',
            afterBlur                : function() {this.sync();}
        });
    });
	 $('textarea.autosize').autosize();
	  //form添加noEnter属性，禁止文本框回车提交
    $('form[noEnter]').each(function() {
        $(':text', $(this)).keypress(function(e) {
            var key = e.which;
             if(key == 13)
                return false;
        });
    });
	$('.btn-close').each(function(){
	
		$(this).click(function(){
			
			window.location=$(this).attr('url');
			
		});
		
	});
	
    

	
	
$('.j-icheck').iCheck({
    	
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
        increaseArea: '20%' // optional
    });
	 $(':checkbox.checkboxCtrl').on('ifChanged', function(event) {
	    	
	        var checked = event.target.checked == true ? 'check' : 'uncheck';
	        var group = $(this).attr('group');
	        $(":checkbox[name='"+ group +"']").iCheck(checked);
	    });
		
		$("a[target=dialog]").each(function(){
			$(this).click(function(event){
				var $this = $(this);
				var title = $this.attr("title") || $this.text();
				var url = $this.attr("href");
				var rel = $this.attr("rel") || "_blank";
				var options = {};
				var w = $this.attr("width")|| "500px";
				var h = $this.attr("height")|| "400px";
	            var html;
				$.ajax({
					type:'POST',
					url:url,
					dataType:"html",
					cache: false,
					success: function(data){
						$.layer({
							   type: 1,   //0-4的选择,
							    title: title,
							    border: [0],
							    closeBtn: [0,true],
							    shadeClose: false,
							    area: [w, h],
							   
							    page: {
							        html: data,
							        
							    },
							    yes:function(index){
							    	
							    	if($('.xubox_yes').closest('form').hasClass('form-validate')){
							    		//validateform(true);
							    		
							    		$('.xubox_yes').closest('form').submit();
							    	
							    		
							    	}else{
							    		ajaxform(index,$('.xubox_yes').closest('form'));
							    	}
							    	
							    	
							    	//layer.close(index);
							    
							    
							    }
						
						});
						$('select.selectpicker').selectpicker();	
						validateform();
						$('.j-icheck').iCheck({
					    	
					        checkboxClass: 'icheckbox_square-green',
					        radioClass: 'iradio_square-green',
					        
					    });
						
					},
					error: AjaxError
				});
				
				
				
				return false;
			});
		});
	
	$("#zstable").dataTable({  
	
       "bPaginate": true, //开关，是否显示分页器  
       "bInfo": true, //开关，是否显示表格的一些信息  
       "bFilter": false, //开关，是否启用客户端过滤器  
       "bJQueryUI": true, //开关，是否启用JQueryUI风格  
       "bSort": true, //开关，是否启用各列具有按列排序的功能  
       "bSortClasses": true,  
       "bStateSave": true, //开关，是否打开客户端状态记录功能。这个数据是记录在cookies中的， 打开了这个记录后，即使刷新一次页面，或重新打开浏览器，之前的状态都是保存下来的- ------当值为true时aoColumnDefs不能隐藏列  
       "aoColumnDefs": [ { 'bSortable': false, 'aTargets':['nosort']},{ "bSearchable": false, "aTargets":['nosearch']  }]  ,
       "fnInitComplete": function(oSettings, json) {
    
    	  
    	},
    	"sDom":'<"H"lfrpi><"F">t',
       "sPaginationType": "full_numbers",  
       "oLanguage": {  
           "sProcessing": "正在加载中......",  
           "sLengthMenu": "每页显示 _MENU_ 条记录",  
           "sZeroRecords": "对不起，查询不到相关数据！",  
           "sEmptyTable": "表中无数据存在！",
           "sInfoEmpty": "真的没有数据了！",
           "sInfo": "当前显示 _START_ 到 _END_ 条，共 _TOTAL_ 条记录",  
           "sInfoFiltered": "数据表中共为 _MAX_ 条记录",  
           "sSearch": "全局搜索",  
           "oPaginate": {  
               "sFirst": "首页",  
               "sPrevious": "上一页",  
               "sNext": "下一页",  
               "sLast": "末页"  
           }  
       } //多语言配置  



   });  
	
	
	
	 $('select[name=zstable_length]').addClass('selectpicker show-tick');
	 $('select[name=zstable_length]').attr('data-style','btn medium bg-blue-alt');
	 $('select[name=zstable_length]').attr('data-width','auto');
	 $('select[name=zstable_length]').attr('data-container','body');
	
	$('select.selectpicker').selectpicker();	

	
	   //dragsort
    if ($.fn.dragsort) {
        $('.zs-dragsort').each(function() {
            var $this = $(this);
            var selector    = $this.data('selector') || 'div',
                exclude     = $this.data('exclude') || 'input, textarea',
                dragend     = $this.data('dragend'),
                dragBetween = $this.data('between') || false,
                placeholder = $this.data('placeholder'),
                s_container = $this.data('scrollcontainer') || window,
                otherBox    = $this.data('otherbox') || null;
            if (placeholder) {
                placeholder = $(placeholder).html();
            } else {
                placeholder = '<li class="placeHolder"><div></div></li>';
            }
            if (dragend) {
                dragend = str2Func(dragend) || function() {};
            } else {
                dragend = function() {};
            }
            if (s_container && s_container != window) {
                s_container = $this.closest(s_container);
                if (!s_container.length) s_container = window;
            }
            var $dragBox = $this;
            if (otherBox && $(otherBox).length) $dragBox = $this.add(otherBox);
            $dragBox.dragsort({
                dragSelector        : selector, // 需要拖动的子元素选择器
                dragSelectorExclude : exclude,  // 需要排除的可拖动元素
                dragEnd             : dragend,  // 拖动结束回调
                dragBetween         : dragBetween,  // 是否允许在多个容器间互相拖拽
                placeHolderTemplate : placeholder,  // 拖动时[目的地]的占位模板
                scrollContainer     : s_container
            });
        });
    }
    
    //bootstrap - tooltips
    $('.zs-date').each(function() {
        var $this = $(this);
        var dateformat   = $this.data('date-format') || "yyyy-mm-dd";
        var autoclose   = $this.data('autoclose') || true;
        var todayBtn   = $this.data('todayBtn') || true;
        var pickerPosition   = $this.data('pickerPosition') || "bottom-right";
        var forceParse   = $this.data('forceParse') || true;
        var maxView  = parseInt($this.attr('data-maxView')) || 4;
        var minView   = parseInt($this.attr('data-minView')) || 0;
        var startView   = parseInt($this.attr('data-startView')) || 2;
        var todayHighlight   = $this.data('todayHighlight') || true;
      
        $this.datetimepicker({
        	container: $this.closest(".form-input"),
        	format: dateformat,
    		todayHighlight: todayHighlight,
    		startView:   startView,
    		minView: minView,
    		maxView:  maxView,
    		forceParse: forceParse,
        	autoclose: autoclose,
            todayBtn: todayBtn,
            pickerPosition: pickerPosition});
        
        $this.datetimepicker()
        .on('changeDate', function(ev){
        	
        	var start=$this.val();
        	
            //if (ev.date.valueOf() < date-start-display.valueOf()){
            	//alert(start);
        	$('.zs-date').datetimepicker('setStartDate',start);
           // }
        });//
    });
    $('.zs-tooltip').each(function() {
        var $this = $(this);
        var html      = $this.data('html') || false;
        var placement = $this.data('placement') || 'auto';
        var content   = $this.data('content') || $($this.data('el-content')).html() || $this.attr('title') || false;
        $this.tooltip({html:html, placement:placement, title:content, container:'body'});
    });
    //bootstrap - popover
    $('.zs-popover').each(function() {
        var $this = $(this);
        var html      = $this.data('html') || false;
        var content   = $this.data('content') || $($this.data('el-content')).html() || false;
        var placement = $this.data('placement') || 'auto';
        var trigger   = $this.data('trigger') || 'click';
        $this.popover({html:html, placement:placement, content:content, trigger:trigger});
    });
	  //bootstrap - tags
    if ($.fn.tags) {
        $(".tags-control").each(function() {
            var $this = $(this);
            
            var url   = $this.data('url'),
                type  = $this.data('type') || 'GET',
                param = $this.data('parametername') || 'tagName',
                max   = $this.data('max') || 0,
                clear = $this.data('clearnotfound') || false;
            $this.tags({
                url: url,
                type: type,
                parameterName: param,   // 生成的<input type='hidden' />的name属性
                max: max,              // 允许的最大标签个数(0=不限)
                clearNotFound: clear   // 是否清除未查找到的输入字符
            });
        });
    }
	
	
}
	

