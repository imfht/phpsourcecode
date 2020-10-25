/**
 * muucmf-sku 1.0.0
 * 商城商品SKU选择
 * 
 * http://www.hoomuu.cn/
 * 
 * Copyright 2016, @严大蒙同学
 */
+ function($, window, document, undefined){
	'use strict';
	
	var Muusku = function(element, options) {
		this.element = element;
		this.options = options;
		this.opts = $.extend({},this.DEFAULTS,options);
		this.init('muusku',element, options);
    }

    Muusku.DEFAULTS = {
		//parent:'.sku-content', //选择按钮的父级盒子
		sku_data:'', //SKU的JSON数据
		//order_sku:''//接收选择好的sku参数的变量
    };

    Muusku.prototype.init = function(type,element, options) {
    	var that = this;
    	this.type = type;
	    if(this.opts.sku_data){
	        this.quantity();//初始化库存数
	        var sku_info = {};//规格参数
	        //var _li = $('.sku-content li');
	        var _li = element.find('li');
	        //点击事件
	        _li.click(function(){
	            var skuArr = [];
	            $.each(that.opts.sku_data.table, function(idx, obj) {
	                skuArr.push(idx);
	            });

	            var _this = $(this);
	            var dataSku = _this.parent().attr('data-sku');
	            
	            if(_this.hasClass('no_sku')){
	                delete sku_info[_this.attr('data-table')];
	                return;
	            }

	            if(_li.hasClass('no_sku')){
	                delete sku_info[_this.attr('data-table')];
	            }

	            sku_info[_this.attr('data-table')]=_this.attr('data-value');
	            _this.siblings().removeClass('selected');//同胞元素取消选择
	            _this.addClass("selected");
	            _li.removeClass('no_sku');

	            //调整规格参数对象顺序
	            var skuObj={};
	            skuArr.forEach(function(el){
	                skuObj[el]=sku_info[el];
	            });
	            sku_info = skuObj;
	            //console.log(skuObj);
	            //拼接数据查询字符串
	            var t='';
	            $.each(skuObj,function(a,b){
	                if(!b){
	                    b='';
	                }else{
	                    t += ';'+a+':'+b;
	                }
	                //去除t最前面的分号
	                if(t.substr(0,1)==';')t=t.substr(1);
	            });
	            //遍历JSON对象
	            $.each(that.opts.sku_data.info, function(idx, obj) {
	                //把IDX拆分成数组，避免indexOf时字符串的多次包含BUG
	                var idx = idx.split(';');
	                if(idx.indexOf(_this.attr('data-table')+':'+_this.attr('data-value'))>= 0){

	                    //alert(idx);
	                     for(var i=0;i<_li.length;i++){
	                    //定义查询条件a   
	                         var a = $(_li[i]).attr('data-table')+':'+$(_li[i]).attr('data-value');
	                         if(
	                             idx.indexOf(a)>= 0 && 
	                             _this.attr('data-table')!=$(_li[i]).attr('data-table') 
	                         ){
	                             if(obj.quantity=='' || obj.quantity==0){
	                                 $(_li[i]).removeClass('selected');
	                                 $(_li[i]).addClass('no_sku');
	                             }
	                         }
	                     }  
	                }
	            });

	            //获取读取库存参数
	            var selectedLi = element.find('li.selected');
	            var q='';
	            for(var i=0;i<selectedLi.length;i++){
	                q+=';'+$(selectedLi[i]).attr('data-table')+':'+$(selectedLi[i]).attr('data-value');
	            }
	            if(q.substr(0,1)==';')q=q.substr(1);
	            that.skuQuery(q);
	            that.quantity(q);
	            that.price(q);
	        })
	    }else{
	    	return this.orderSku;
	    }
    }
    /*
    确认商品规格选择完整
     */
    Muusku.prototype.skuQuery = function(t) {
    	var that = this;
    	$.each(this.opts.sku_data.info, function(idx, obj) {
            
            if(idx==t){
                //orderSku = t;
                that.element.find('input').val(t);
            }
        })
    }
    /*
    获取并设置库存数量
     */
    Muusku.prototype.quantity = function(t){
    	var that = this;
        var q = 0;
        $.each(this.opts.sku_data.info, function(idx, obj) {
            if(!t){
                q+=parseInt(obj.quantity);
            }else{
                if(t.indexOf(';')>=0){
                    if(idx==t){
                        q=parseInt(obj.quantity);
                    }
                }else{
                    var idx = idx.split(';');
                    if(idx.indexOf(t)>= 0){
                        q+=parseInt(obj.quantity);
                    }
                }
            }
        })

        $('.quantity span').text(q);
        return q;
    }
    Muusku.prototype.price = function(t){
        var that = this;
        t=$.trim(t);
        var price = 0;
        $.each(this.opts.sku_data.info, function(idx, obj) {
            
            if(idx==t){
                price=obj.price;
                $('.now-price').text(price);
            //}else{
            //    price='{$product.price}';
            }
        })
    }

    Muusku.prototype.sku = function(t){
    	return this.orderSku;
    }


    $.fn.Muusku = function(options){
	   var muusku = new Muusku(this,options);
	   return this;
	};

}(jQuery, window, document);


	