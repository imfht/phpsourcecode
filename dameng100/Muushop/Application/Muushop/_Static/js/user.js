$(function(){
	/**
	 * 获取订单数量
	 */
	order_status(1,'.status-1');//代付款
	order_status(2,'.status-2');//代发货
	order_status(3,'.status-3');//代收货
	order_status(4,'.status-4');//代评价

	function order_status(status,ele){
		
		var url = 'Muushop/api/orders/status/'+status;
		$.get(url,function (ret) {
	          if(ret.status==1){
	              $(ele).find('[data-toggle="num"]').text(ret.data.totalCount);
	          }
	      })
	}
	
});

$(function(){

	//支付回调地址
	var result_url;//初始化回调地址
	$.get('Muushop/api/result_url',function(ret){
		if(ret.status==1){
			result_url = encodeURI(ret.data);
		}else{
			result_url =  _ROOT_ + '/Muushop/user/orders';
		}
		
	})

	var status = '1,2,3,4';
	var url = 'Muushop/api/orders/status/'+status;
	$.get(url,function(ret){
		
		if(ret.status==1){
			//清除元素内HTML
			$('.index-orders-list').empty();
            //遍历数组
        	$.each(ret.data.list,function(n,value) { 

        		//获取支付方式
        		var pay_type = '';
				switch(value.pay_type)
				{
				case 'onlinepay':
				  pay_type = '在线支付';
				  break;
				case 'balance':
				  pay_type = '余额支付';
				  break;
				case 'delivery':
				  pay_type = '货到付款';
				  break;
				}

				//操作区html判断
				var operate_html='';//初始化变量
				switch(value.status){
					case '1':
					var param = ['order_no',value.order_no,'result_url', result_url];	
					var pay_url = U('Muushop/pay/charge',param);
					var status = '待付款';
					operate_html = '<a href="'+pay_url+'" class="btn btn-success btn-block" data-toggle="pay_order">立即支付</a>'+
						    	   '<a class="btn btn-warning btn-block" data-toggle="cannel_order" data-id="'+value.id+'">取消订单</a>';
					break;
					case '2':
					var status = '待发货';

					if(value.pay_type=='delivery'){
						operate_html += '<a class="btn btn-warning btn-block" data-toggle="cannel_order" data-id="'+value.id+'">取消订单</a>';
					}
					var url = U("Muushop/User/orders",["action","delivery_info","id",value.id]);
					operate_html += '<a class="btn btn-block" data-remote="'+url+'" data-toggle="modal">查看物流</a>';
					break;
					case '3':
					var status = '已发货';
					var url = U("Muushop/User/orders",["action","delivery_info","id",value.id]);
					operate_html = '<a class="btn btn-danger btn-block" data-toggle="do_receipt" data-id="'+value.id+'"}>确认收货</a>'+
						    	   '<a class="btn btn-block" data-remote="'+url+'" data-toggle="modal">查看物流</a>';
					break;
					case '4':
					var status = '待评价';
					var url = U('Muushop/User/comment',['id',value.id]);
					operate_html = '<a class="btn  btn-info btn-block" href="'+url+'">评价</a>';
					break;
					default:
					operate_html = '';
				}
				//组装html代码
		  		var html_str = '';
              	html_str += '<div class="order-item row clearfix">';
              	html_str += '<div class="order-base-info clearfix"><span class="pull-left">订单号：'+value.order_no+'</span><span class="order-status pull-right">'+status+'</span></div>';
              	html_str += '<div class="col-md-9">'+
						    '<div class="goods-box clearfix">';
			    //遍历订单商品
				var html_products = '';
				$.each(value.products,function(n,products) {
					html_products += '<div class="goods-item clearfix">';
			    		html_products += '<div class="p-img">'+
			                  				'<a href="/index.php?s=/muushop/index/product/id/'+products.sku_id+'.html" target="_blank">'+
			                  				'<img src="'+products.main_img+'">'+
			                  				'</a>'+
			                  			 '</div>';

			        	html_products += '<div class="p-msg">'+
			        					 '<div class="p-name">'+
				                  			'<a href="/index.php?s=/muushop/index/product/id/12.html" target="_blank">'+products.title+'</a>'+
				                    	 '</div>';

				    	html_products += '<div class="sku_box">';
				    	//遍历商品sku
				    	var html_sku = '';
				    	if(products.sku){
				    		$.each(products.sku,function(m,sku){
					    		html_sku += '<span class="sku_li_span">'+products.sku[m]+'</span>';
					    	})
				    	}
				    	
			            html_products += html_sku;
			            html_products += '</div>';

				        html_products += '<div class="goods-number">x'+products.quantity+'</div>';
			    		html_products += '</div>';
			    	html_products += '</div>';
				});

				html_str += html_products;
			    
			    html_str += '</div></div>';


			    html_str += '<div class="col-md-3">'+
							  '<div class="row">';
				html_str += '<div class="col-md-6 col-xs-12">';
					html_str += '<div class="amount text-center">'+
					                '合计：<span>¥'+ value.paid_fee+'</span>'+
					              '</div>';

					
					html_str += '<div class="paytype text-center">'+
					                '<span>'+pay_type+'</span>'+
					             '</div>';
				html_str += '</div>';
				html_str += '<div class="col-md-6 col-xs-12">'+
								'<div class="operate">';

				    html_str += operate_html;
				    html_str += '</div>'+
							'</div>';

				html_str += '</div></div></div>';
				html_str += '</div>';

				$('.index-orders-list').append(html_str);
		  	})
		}else{
			//清除元素内HTML
			$('.index-orders-list').empty();
			$('.index-orders-list').html('<span class="empty">还没有待处理的订单哦~</span>');
		}
              
    })
});