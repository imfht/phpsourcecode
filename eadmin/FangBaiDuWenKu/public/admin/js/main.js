layui.use(['layer','jquery','form','upload'],function(){
  var layer = parent.layer === undefined ? layui.layer : parent.layer
  , form = layui.form
  ,upload = layui.upload
  ,$ = layui.jquery;
  


  
  
  var uploadurl=$('#uploadfile').data('url');
  upload.render({
 	    url: uploadurl
 	    ,elem:'#uploadfile'
 		,ext: 'jpg|png|gif'
 		
 		,area: ['100px', '100px']
 	    ,before: function(input){
 	    	
 	    	
 	      loading = layer.load(2, {
 	        shade: [0.2,'#000'] //0.2透明度的白色背景
 	      });
 	    }
 	    ,done: function(res, input){
 	      layer.close(loading);
 	     
 	   var eleid=jq(input).data('id');
 	      $('#'+eleid).val(res.path);
 		 // headedit.src = res.headpath;
 	      layer.msg(res.msg, {icon: 1, time: 1000});
 	    }
 	  }); 

  form.on('switch(switchTest)', function(data){
	  loading = layer.load(2, {
	      shade: [0.2,'#000']
	    });
	  var url=$(data.elem).data('url');
	  //var val=$(data.elem).data('val');
	  var status=0;
	  var field=$(data.elem).attr('name');
	 
	  
	    if(data.elem.checked){
	    	status=1;
	    	
	    }else{
	    	status=0;
	    	
	    }
	    var url=url+'?status='+status+'&field='+ field ;
	   
	   
	    $.get(url,function(data){
	    	
	      if(data.code == 1){
	        layer.close(loading);
	        layer.msg(data.msg, {icon: 1, time: 1000}, function(){ });
	      }else{
	        layer.close(loading);
	        layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
	      }
	    });
	    return false;
	  });

  $('.cstatus').click(function(){
	  
	  loading = layer.load(2, {
	      shade: [0.2,'#000']
	    });
	  
	  var url=$(this).data('url');
	  
	  var status=0;
	  
	  var val=$(this).data('val');
	  
	  if(val==1){
		  
	    	status=0;
	    	
	    }else{
	    	
	    	status=1;
	    	
	    }
	  
	  var field=$(this).data('field');
	  
	  var url=url+'?status='+status+'&field='+ field ;
	  
	    $.get(url,function(data){
	    	
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
		    return false;
	  
  });
  
  form.on("submit(formadd)",function(data){
	  var locationurl=$('form').attr('localtion-url');
	  var url=$('form').data('url');
	    loading = layer.load(2, {
		      shade: [0.2,'#000']
		    });
		
	  
		   // var param = data.field;
		  
		   
		    $.post(url,$('form').serialize(),function(data){
		    	
		      if(data.code == 1){
		        layer.close(loading);
		        layer.msg(data.msg, {time: 1000}, function(){
		        	parent.location.reload();
		        	setTimeout(function(){
		                layer.closeAll("iframe");
		            },200);
		        });
		      }else{
		        layer.close(loading);
		        layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
		      }
		    });
		    return false;
	  
	  
	  
	  
	  
      
		
	})
	 form.on("submit(dialogadd)",function(data){
	  var locationurl=$('form').attr('localtion-url');
	  var url=$('form').data('url');
	    loading = layer.load(2, {
		      shade: [0.2,'#000']
		    });
		
	   
		   // var param = data.field;
		  
		   
		    $.post(url,$('form').serialize(),function(data){
		    	
		      if(data.code == 1){
		        layer.close(loading);
		        layer.msg(data.msg, {time: 400}, function(){
		        	
		        	
		                layer.closeAll("iframe");
		           
		        });
		      }else{
		        layer.close(loading);
		        layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
		      }
		    });
		    return false;
	  
	  
	  
	  
	  
      
		
	})
	$('.closebtn').click(function(){
		layer.closeAll();
		
		
	});
  $('.pagination').find('a').click(function(){
	  var url = $(this).attr('href');
	  
	//  var query  = $('form#searchform').serialize();
	  var query  = $('.search-form').find(':enabled').serialize();
      query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g,'');
      query = query.replace(/^&/g,'');
      url += '&' + query;
      window.location.href = url;
      event.preventDefault();
      return false;
      
	  
  });
  //搜索功能
  $("#search").click(function(){
          var url = $(this).data('url');
         // var query  = $('#searchform').serialize();
          var query  = $('.search-form').find(':enabled').serialize();
          query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g,'');
          query = query.replace(/^&/g,'');
          if( url.indexOf('?')>0 ){
              url += '&' + query;
          }else{
              url += '?' + query;
          }
          
          window.location.href = url;
  });

	$(".Add_btn,.users_edit").click(function(){
		
		var title=$(this).data('title');
		var url=$(this).data('url');
		var index = layui.layer.open({
			title : title,
			type : 2,
			content : url,
			success : function(layero, index){
				setTimeout(function(){
					layui.layer.tips('点击此处返回', '.layui-layer-setwin .layui-layer-close', {
						tips: 3
					});
				},200)
			}
		})
		//改变窗口大小时，重置弹窗的高度，防止超出可视区域（如F12调出debug的操作）
		$(window).resize(function(){
			layui.layer.full(index);
		})
		layui.layer.full(index);
		return false;
	})
  
  
  //全选
	form.on('checkbox(allChoose)', function(data){
		var child = $(data.elem).parents('table').find('tbody input[lay-filter="choose"]');
		child.each(function(index, item){
			item.checked = data.elem.checked;
			
			
		});
		form.render('checkbox');
	});

	//通过判断文章是否全部选中来确定全选按钮是否选中
	form.on("checkbox(choose)",function(data){
		var child = $(data.elem).parents('table').find('tbody input[lay-filter="choose"]');
		var childChecked = $(data.elem).parents('table').find('tbody input[lay-filter="choose"]:checked')
		
		
		
		if(childChecked.length == child.length){
			$(data.elem).parents('table').find('thead input#allChoose').get(0).checked = true;
		}else{
			$(data.elem).parents('table').find('thead input#allChoose').get(0).checked = false;
		}
		form.render('checkbox');
	})  
	
$("body").on("click",".users_del",function(){  //删除
		 var _this = $(this);
		 var url= $(this).data('url');
		 var page= $('.pagination li.active span').html();
		  var length= $('.users_content tr').length;
	    
	  
	  
		layer.confirm('确定删除此数据？',{icon:3, title:'提示信息'},function(index){
		    loading = layer.load(2, {
			      shade: [0.2,'#000']
			    });
		   
			
			    $.getJSON(url,function(data){
			    	
			      if(data.code == 1){
			        layer.close(loading);
			        layer.msg(data.msg, {icon: 1, time: 1000}, function(){
			        	 if(length-1>0){
			        		 location.reload();
			        	  }else{
			        		  if(page>1){
			        			  page=page-1;
			        			  
			        		  }
			        		  location.href = window.location.href+'?page='+page;// '{:url("admin_user/index")}'+page;
			        	  }
			        	//_this.closest('tr').hide();
			        });
			      }else{
			        layer.close(loading);
			        layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
			      }
			    });
		});
	})
	
	  $('.ajaxget').click(function(){
	  var url=$(this).data('url');
	  loading = layer.load(2, {
	      shade: [0.2,'#000']
	    });
	 
	  $.get(url,function(data){
	    	
	      if(data.code == 1){
	        layer.close(loading);
	        layer.msg(data.msg, {icon: 1, time: 1000}, function(){
	        	 
	        });
	      }else{
	        layer.close(loading);
	        layer.msg(data.msg, {icon: 2, anim: 6, time: 1000}, function(){
      		 
		        });
	      }
	    });
	    return false;
	  
  });
$(".getbtn").click(function(){
	var url= $(this).data('url');
	loading = layer.load(2, {
	      shade: [0.2,'#000']
	    });
    $.get(url,function(data){
  	  
        if(data.code == 1){
          layer.close(loading);
          layer.msg(data.msg, {icon: 1, time: 1000}, function(){
        	  //parent.location.reload();
        	  location.reload();
          });
        }else{
          layer.close(loading);
          layer.msg(data.msg, {icon: 2,anim: 6, time: 1000});
        }
      });
	
});
//批量审核
$(".batchSh").click(function(){
	
	var $checkbox = $('.users_list tbody input[type="checkbox"][name="checked"]');
	var $checked = $('.users_list tbody input[type="checkbox"][name="checked"]:checked');
	var tipconfirm=$(this).attr('tipconfirm');
	var tiperror=$(this).attr('tiperror');
	
	if($checkbox.is(":checked")){
		
		
		
		layer.confirm(tipconfirm,{icon:3, title:'提示信息'},function(index){
			loading = layer.load(2, {
			      shade: [0.2,'#000']
			    });
			  var checkboxid=[];
			 $checked.each(function(){
				
				 checkboxid.push($(this).val());
			 });
			
			 var url= $('.batchSh').data('url');
			 
		      $.post(url,{ids:checkboxid},function(data){
		    	  
		          if(data.code == 1){
		            layer.close(loading);
		            layer.msg(data.msg, {icon: 1, time: 1000}, function(){
		            	location.reload();
		            });
		          }else{
		            layer.close(loading);
		            layer.msg(data.msg, {icon: 2,anim: 6, time: 1000});
		          }
		        });
			
        })
		
		
	}else{
		layer.msg(tiperror);
	}
	
	
});
$(".change_btn").click(function(){
	
	var $checkbox = $('.users_list tbody input[type="checkbox"][name="checked"]');
	var $checked = $('.users_list tbody input[type="checkbox"][name="checked"]:checked');
	var tipconfirm=$(this).attr('tipconfirm');
	var tiperror=$(this).attr('tiperror');
	
	if($checkbox.is(":checked")){
		
		
		
		layer.confirm(tipconfirm,{icon:3, title:'提示信息'},function(index){
			layer.close(index);
			  var checkboxid=[];
			 
			 $checked.each(function(){
				
				 checkboxid.push($(this).val());
			 });
			 var Str=checkboxid.join("-");
				var title=$(this).data('title');
				
				var url=$(".change_btn").data('url')+'?ids='+Str;
				
				var index = layui.layer.open({
					title : title,
					type : 2,
					content : url,
					success : function(layero, index){
						setTimeout(function(){
							layui.layer.tips('点击此处返回', '.layui-layer-setwin .layui-layer-close', {
								tips: 3
							});
						},200)
					}
				})
				//改变窗口大小时，重置弹窗的高度，防止超出可视区域（如F12调出debug的操作）
				$(window).resize(function(){
					layui.layer.full(index);
				})
				layui.layer.full(index);
				return false;
				
				
			
        })
	}else{
		layer.msg(tiperror);
	}
	
	
});

$(".tuisong_btn").click(function(){
	
	var $checkbox = $('.users_list tbody input[type="checkbox"][name="checked"]');
	var $checked = $('.users_list tbody input[type="checkbox"][name="checked"]:checked');
	var tipconfirm=$(this).attr('tipconfirm');
	var tiperror=$(this).attr('tiperror');
	
	if($checkbox.is(":checked")){
		
		
		
		layer.confirm(tipconfirm,{icon:3, title:'提示信息'},function(index){
			layer.close(index);
			  var checkboxid=[];
			 
			 $checked.each(function(){
				
				 checkboxid.push($(this).val());
			 });
			 var Str=checkboxid.join("-");
				var title=$(this).data('title');
				
				var url=$(".tuisong_btn").data('url')+'?ids='+Str;
				
				var index = layui.layer.open({
					title : title,
					type : 2,
					content : url,
					success : function(layero, index){
						setTimeout(function(){
							layui.layer.tips('点击此处返回', '.layui-layer-setwin .layui-layer-close', {
								tips: 3
							});
						},200)
					}
				})
				//改变窗口大小时，重置弹窗的高度，防止超出可视区域（如F12调出debug的操作）
				$(window).resize(function(){
					layui.layer.full(index);
				})
				layui.layer.full(index);
				return false;
				
				
			
        })
	}else{
		layer.msg(tiperror);
	}
	
	
});



//批量删除
	$(".batchDel").click(function(){
		var $checkbox = $('.users_list tbody input[type="checkbox"][name="checked"]');
		var $checked = $('.users_list tbody input[type="checkbox"][name="checked"]:checked');
		
		
		
		if($checkbox.is(":checked")){
			var url= $(this).data('url');
			var page= $('.pagination li.active span').html();
			
			layer.confirm('确定删除选中的信息？',{icon:3, title:'提示信息'},function(index){
				loading = layer.load(2, {
				      shade: [0.2,'#000']
				    });
				  var checkboxid=[];
				 $checked.each(function(){
					
					 checkboxid.push($(this).val());
				 });
				
				
			      $.post(url,{ids:checkboxid},function(data){
			    	  
			          if(data.code == 1){
			            layer.close(loading);
			            layer.msg(data.msg, {icon: 1, time: 1000}, function(){
			            	 
				        		  if(page>1){
				        			  page=page-1;
				        			  
				        		  }
				        		  location.href = window.location.href+'?page='+page;
				        	  
			            	
			            	//$checked.closest('tr').hide();
			            });
			          }else{
			            layer.close(loading);
			            layer.msg(data.msg, {icon: 2,anim: 6, time: 1000});
			          }
			        });
				
	        })
		}else{
			layer.msg("请选择需要删除的信息");
		}
	})



	  
  });