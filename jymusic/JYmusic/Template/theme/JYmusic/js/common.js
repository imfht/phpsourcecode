// on document ready
(function($){
	"use strict";
	var globalDfd = $.Deferred();
	$(window).bind('load',function(){
		// 加载所有的脚本
		globalDfd.resolve();
		//测试登录
		checkLogin ();
		
	});
	var scroll = $('.custom_scrollbar');
	if(scroll.length){
		var isVisible = setInterval(function(){
			if(scroll.is(':visible')){
				scroll.customScrollbar({
					preventDefaultScroll: true
				});
				clearInterval(isVisible);
			}
		},100);
	}
	$(function(){
		$.fx.speeds._default = 500;
    	$('#myTab a:last').tab('show')
		// 打开下拉
		$.fn.css3Animate = function(element){
			return $(this).on('click',function(e){
				var dropdown = element;
				$(this).toggleClass('active');
				e.preventDefault();
				if(dropdown.hasClass('opened')){
					dropdown.removeClass('opened').addClass('closed');
					setTimeout(function(){
						dropdown.removeClass('closed')
					},500);
				}else{
					dropdown.addClass('opened');
				}
			});
		}
		$('#lang_button').css3Animate($('#lang_button').next('.dropdown_list'));
		$('#currency_button').css3Animate($('#currency_button').next('.dropdown_list'));

		// 站点辅助函数
	
		$.fn.waypointInit = function(classN,offset,delay,inv){
			return $(this).waypoint(function(direction){
				var current = $(this);
				if(direction === 'down'){
					if(delay){
						setTimeout(function(){
							current.addClass(classN);
						},delay);
					}
					else{
						current.addClass(classN);
					}
				}else{
	            	if(inv == true){
	                    current.removeClass(classN);
	             	}
	            }
			},{ offset : offset })
		};

		// 同步 
	
		$.fn.waypointSynchronise = function(config){
			var element = $(this);
			function addClassToElem(el,eq){
				el.eq(eq).addClass(config.classN);
			}
			element.closest(config.container).waypoint(function(direction){
			 	element.each(function(i){
					if(direction === 'down'){
	
			 			if(config.globalDelay != undefined){
			 				setTimeout(function(){
			 					setTimeout(function(){
			 						addClassToElem(element,i);
			 					},i * config.delay);
			 				},config.globalDelay);
			 			}else{
			 				setTimeout(function(){
			 					addClassToElem(element,i)
			 				},i * config.delay);
			 			}
	
					}else{
						
						if(config.inv){
							setTimeout(function(){
								element.eq(i).removeClass(config.classN);
							},i * config.delay);
						}
	
					}
				});
			},{ offset : config.offset });
			return element;
		};

	// animation 主页
		(function(){
			globalDfd.done(function(){
			$('.products_container:not(.a_type_2) .photoframe.animate_ftb').waypointSynchronise({
				container : '.products_container',
				delay : 200,
				offset : 700,
				classN : "animate_vertical_finished"
			});
			$('.products_container.a_type_2 .photoframe.animate_ftb').waypointSynchronise({
				container : '.products_container',
				delay : 200,
				offset : 700,
				classN : "animate_vertical_finished"
			});
			$('.wfilter_carousel .photoframe.animate_ftb').waypointSynchronise({
				container : '.wfilter_carousel',
				delay : 200,
				offset : 700,
				classN : "animate_vertical_finished"
			});
			$('.bestsellers_carousel .photoframe.animate_ftb').waypointSynchronise({
				container : '.bestsellers_carousel',
				delay : 200,
				offset : 700,
				globalDelay : 400,
				classN : "animate_vertical_finished"
			});
			
			$('.bestuser_carousel .photoframe.animate_ftb').waypointSynchronise({
				container : '.bestuser_carousel',
				delay : 200,
				offset : 700,
				globalDelay : 400,
				classN : "animate_vertical_finished"
			});
			$('.banner_type_2[class*="animate_ft"]').waypointSynchronise({
				container : '.row',
				delay : 200,
				offset : 800,
				classN : "animate_vertical_finished"
			});
			$('.animate_half_tc').waypointSynchronise({
				container : '.row',
				delay : 0,
				offset : 830,
				classN : "animate_horizontal_finished"
			});
			$('.heading2').waypointInit('animate_sj_finished animate_fade_finished','800px');
			$('.nav_buttons_wrap.animate_fade').waypointInit('animate_sj_finished animate_fade_finished','800px');
			$('.product_brands a.animate_fade').waypointSynchronise({
				container : '.product_brands',
				delay : 200,
				offset : 830,
				classN : "animate_sj_finished animate_fade_finished"
			});
			$('.blog_carousel a.photoframe').waypointSynchronise({
				container : '.blog_animate.animate_ftr',
				delay : 0,
				offset : 830,
				classN : "animate_vertical_finished"
			});
			$('.blog_carousel .mini_post_content > .animate_ftr').waypointSynchronise({
				container : '.blog_animate.animate_ftr',
				delay : 200,
				offset : 830,
				classN : "animate_horizontal_finished"
			});
			$('.blog_animate.animate_ftr').waypointInit('animate_horizontal_finished','800px');
			$('.ti_animate.animate_ftr').waypointInit('animate_horizontal_finished','800px',1000);
			$('.testiomials_carousel .animate_ftr:first').waypointInit('animate_horizontal_finished','851px',1200);
			$('.testiomials_carousel .animate_ftr:nth-child(2)').waypointInit('animate_horizontal_finished','973px',1400);
			$('.testiomials_carousel .animate_ftr:nth-child(3)').waypointInit('animate_horizontal_finished','987px',1600);
			$('.heading1.animate_ftr').waypointInit('animate_horizontal_finished','1000px');
			$('.isotope_menu > li.animate_ftr').waypointSynchronise({
				container : '.isotope_menu',
				delay : 200,
				offset : 1000,
				classN : "animate_horizontal_finished"
			});
			$('.flexslider.animate_ftr').waypointInit('animate_horizontal_finished','1000px');
			setTimeout(function(){
				$('.s_banners .d_block.animate_ftr').waypointSynchronise({
					container : '.s_banners',
					delay : 300,
					offset : 830,
					classN : "animate_horizontal_finished"
				});
			},200);
			$('.widget.animate_ftr').waypointInit('animate_horizontal_finished','800px',200);
			$('.heading5').waypointInit('animate_horizontal_finished','800px');
			$('.banner.animate_ftr').waypointSynchronise({
				container : '.row',
				delay : 200,
				offset : 1000,
				globalDelay : 800,
				classN : "animate_horizontal_finished"
			});
			$('.nc_carousel .photoframe.animate_ftb').waypointSynchronise({
				container : '.nc_carousel',
				delay : 200,
				offset : 700,
				classN : "animate_vertical_finished"
			});
			$('.info_blocks_container .animate_ftr').waypointSynchronise({
				container : '.info_blocks_container',
				delay : 200,
				offset : 700,
				classN : "animate_vertical_finished"
			});
			$('.our_recent_work_carousel .animate_ftb').waypointSynchronise({
				container : '.our_recent_work_carousel',
				delay : 200,
				offset : 700,
				classN : "animate_vertical_finished"
			});
			$('.p_tables .animate_fade').waypointSynchronise({
				container : '.p_tables',
				delay : 200,
				offset : 700,
				classN : "animate_fade_finished"
			});
			$('.animate_corporate_container .animate_fade').waypointSynchronise({
				container : '.animate_corporate_container',
				delay : 200,
				offset : 700,
				classN : "animate_fade_finished"
			});
			
			// 粘性菜单
	
			window.sticky = function(){
				var container = $('[role=banner] .h_bot_part'),
					offset = container.closest('[role="banner"]').hasClass('type_5') ? 0 : -container.outerHeight(),
					menu = $('.menu_wrap'),
					mHeight = menu.outerHeight();
					console.log(mHeight);
				container.waypoint(function(direction){
					var _this = $(this);
					if(direction == "down"){
						menu.addClass('sticky');
						container.parent('[role="banner"]').css('border-bottom',mHeight + "px solid transparent");
					}else if(direction == "up"){
						menu.removeClass('sticky');
						container.parent('[role="banner"]').css('border-bottom','none');
					}
				},{offset :  offset});
	
				function getMenuWidth(){
					if(menu.hasClass('type_2')){
						menu.css('width',menu.parent().width());
					}
				}
				getMenuWidth();
				$(window).on('resize',getMenuWidth);
			};
			sticky();
	
			});
		})();

		// jackbox
	
		(function(){
	
			if($(".jackbox[data-group]").length){
				jQuery(".jackbox[data-group]").jackBox("init",{
					    showInfoByDefault: false,
					    preloadGraphics: true, 
					    fullscreenScalesContent: true,
					    autoPlayVideo: true,
					    flashVideoFirst: false,
					    defaultVideoWidth: 960,
					    defaultVideoHeight: 540,
					    baseName: ".jackbox",
					    className: ".jackbox",
					    useThumbs: true,
					    thumbsStartHidden: false,
					    thumbnailWidth: 75,
					    thumbnailHeight: 50,
					    useThumbTooltips: true,
					    showPageScrollbar: false,
					    useKeyboardControls: true 
				});
			}
	
		})();
		//回车提交
		$("#search_form").keydown(function(e){
			 var e = e || event,
			 keycode = e.which || e.keyCode;
			 if (keycode==13) {
			  $(this).submit();
			 }
		});

		// ie9 占位符
	
		(function(){
			if($('html').hasClass('ie9')) {
				$('input[placeholder]').each(function(){
					$(this).val($(this).attr('placeholder'));
					var v = $(this).val();
					$(this).on('focus',function(){
						if($(this).val() === v){
							$(this).val("");
						}
					}).on("blur",function(){
						if($(this).val() == ""){
							$(this).val(v);
						}
					});
				});
				
			}
		})();

		// 旋转木马与过滤器		
		(function(){
	
			var cwf = $('.wfilter_carousel'),
				prev = $('.wfilter_prev'),
				next = $('.wfilter_next'),
				filter = $('[data-carousel-filter]'),
				elements = [],
				item = cwf.find('.photoframe'),
				len = item.length;
	
			if(cwf.length){
	
				var cf = cwf.owlCarousel({
					itemsCustom : [[1199,4],[992,4],[768,3],[590,2],[300,1]],
			 		autoPlay : false,
			 		slideSpeed : 1000,
			 		autoHeight : true
				});
	
			 	prev.on('click',function(){
			 		cf.trigger('owl.prev');
			 	});
			 	next.on('click',function(){
			 		cf.trigger('owl.next');
			 	});
	
			 	for(var i = 0; i < len; i++){
			 		elements.push(item.eq(i)[0].outerHTML);
			 	}
			 	
	
			 	filter.on('click','li',function(){
			 		var	self = $(this),
			 			activeElem = self.children('[data-filter]').data('filter');
			 		cwf.addClass('changed').find('.owl-wrapper').animate({
			 			opacity : 0
			 		},function(){
			 			var s = $(this);
			 			cwf.children().remove();
			 			if(activeElem == "*"){
			 				$.each(elements,function(i,v){
			 					cwf.append(v);
				 			});
			 			}else{
				 			$.each(elements,function(i,v){
				 				if(v.indexOf(activeElem) !== -1){
				 					cwf.append(v);
				 				}
				 			});
			 			}
			 			cwf.data('owlCarousel').destroy();
			 			cwf.owlCarousel({
			 				itemsCustom : [[1199,4],[992,4],[768,3],[590,2],[300,1]],
					 		autoPlay : false,
					 		slideSpeed : 1000,
					 		autoHeight : true,
					 		afterInit: function(){
					 			cwf.addClass('no_children_animate');
					 		}
			 			});
			 			$(window).trigger('resize');
			 		});
			 		self.closest('li').addClass('active').siblings().removeClass('active');
			 	});
			}
	
		})();

		//旋转木马
	
		(function(){
	
			var bsc = $('.bestsellers_carousel');
			if(bsc.length){
				var bs = bsc.owlCarousel({
			 		itemsCustom : [[1199,5],[992,4],[768,3],[590,2],[300,1]],
			 		autoPlay : false,
			 		slideSpeed : 1000,
			 		autoHeight : true
			 	});
	
			 	$('.bestsellers_prev').on('click',function(){
			 		bs.trigger('owl.prev');
			 	});
	
			 	$('.bestsellers_next').on('click',function(){
			 		bs.trigger('owl.next');
			 	});
			}
	
		})();
		
		(function(){	
			var bsu = $('.bestuser_carousel');
			if(bsu.length){
				var bu = bsu.owlCarousel({
			 		itemsCustom : [[1199,6],[992,5],[768,4],[590,3],[300,2]],
			 		autoPlay : false,
			 		slideSpeed : 1000,
			 		autoHeight : true
			 	});
	
			 	$('.bestuser_prev').on('click',function(){
			 		bu.trigger('owl.prev');
			 	});
	
			 	$('.bestuser_next').on('click',function(){
			 		bu.trigger('owl.next');
			 	});
			}
	
		})();

		// our_recent_work_carousel
	
		(function(){
			var orw = $('.our_recent_work_carousel');
			if(orw.length){
				var orwc = orw.owlCarousel({
			 		itemsCustom : [[1199,3],[992,3],[768,3],[421,2],[10,1]],
			 		autoPlay : false,
			 		slideSpeed : 1000,
			 		autoHeight : true
			 	});
	
			 	$('.orw_prev').on('click',function(){
			 		orwc.trigger('owl.prev');
			 	});
	
			 	$('.orw_next').on('click',function(){
			 		orwc.trigger('owl.next');
			 	});
			}
		})();

		// 新集合旋转木马
	
		(function(){
	
			var ncc = $('.nc_carousel');
			if(ncc.length){
				var nc = ncc.owlCarousel({
			 		itemsCustom : [[1199,3],[992,3],[768,3],[575,2],[300,1]],
			 		autoPlay : false,
			 		slideSpeed : 1000,
			 		autoHeight : true
			 	});
	
			 	$('.nc_prev').on('click',function(){
			 		nc.trigger('owl.prev');
			 	});
	
			 	$('.nc_next').on('click',function(){
			 		nc.trigger('owl.next');
			 	});
			}
	
		})();

		// 相机的幻灯片
	
		(function(){
			var	cs = $('.camera_wrap');
	
			if(cs.length){
	
				cs.camera({ 
					height: '41%',
					navigation: true,
					pagination: true,
					playPause:false,
					thumbnails: false,
					time: 4000,
					transPeriod : 1000,
					navigationHover: false,
					onLoaded: function() {
						var image = $('.camera_wrap .camera_src > [data-src]'),
				   			len = image.length,
				   			bullet = $('.camera_wrap .camera_pag_ul > li');
				   		if(bullet.find('.custom_thumb').length) return;
				   		for(var i = 0; i < len; i++){
				   			bullet.eq(i).append('<div class="custom_thumb tr_all_hover"><img src="' + image.eq(i).data('custom-thumb') + '" alt=""></div>');
				   		}
				   		bullet.on("mouseenter mouseleave",function(){
				   			$(this).children('.custom_thumb').toggleClass("active");
				   		});
					}
				});
	
				cs.find('.camera_prev').append('<i class="fa fa-angle-left"></i>');
				cs.find('.camera_next').append('<i class="fa fa-angle-right"></i>');
			}
		})();

		// 评分
	
		$('body').on('click','.rating_list li',function(){
			$(this).siblings().removeClass('active');
			$(this).addClass('active').prevAll().addClass('active');
		});

		// 产品品牌的旋转木马
	
		(function(){
			if($('.product_brands').length){
			 	var pb = $(".product_brands").owlCarousel({
			 		itemsCustom : $('.product_brands').hasClass('with_sidebar') ? [[1199,4],[992,4],[768,3],[480,3],[300,2]] : [[1199,6],[992,5],[768,4],[480,3],[300,2]],
			 		autoPlay : true,
			 		stopOnHover : true,
			 		slideSpeed : 600,
			 		addClassActive : true
			 	});
	
			 	$('.pb_prev').on('click',function(){
			 		pb.trigger('owl.prev');
			 	});
	
			 	$('.pb_next').on('click',function(){
			 		pb.trigger('owl.next');
			 	});
			}
		})();

		// 博客的旋转木马
	
		(function(){
			if($('.blog_carousel').length){
				var blog = $('.blog_carousel').owlCarousel({
			 		singleItem : true,
			 		stopOnHover : true,
			 		slideSpeed : 600,
			 		autoHeight : true,
			 		transitionStyle : "backSlide"
			 	});
			}
	
			$('.blog_prev').on('click',function(){
				blog.trigger('owl.prev');
			});
	
			$('.blog_next').on('click',function(){
				blog.trigger('owl.next');
			});
	
		})();

		// 客户评价
	
		(function(){
			if($('.testiomials_carousel').length){
				var tc = $('.testiomials_carousel').owlCarousel({
			 		singleItem : true,
			 		autoPlay : false,
			 		stopOnHover : true,
			 		slideSpeed : 1000,
			 		autoHeight : true
			 	});
	
				$('.ti_prev').on('click',function(){
					tc.trigger('owl.prev');
				});
	
				$('.ti_next').on('click',function(){
					tc.trigger('owl.next');
				});
			}
	
		})();
		(function(){
			if($('.testiomials_carousel_2').length){
				var tc = $('.testiomials_carousel_2').owlCarousel({
			 		singleItem : true,
			 		autoPlay : false,
			 		stopOnHover : true,
			 		slideSpeed : 1000,
			 		autoHeight : true
			 	});
	
				$('.ti_2_prev').on('click',function(){
					tc.trigger('owl.prev');
				});
	
				$('.ti_2_next').on('click',function(){
					tc.trigger('owl.next');
				});
			}
	
		})();

		// 返回顶部按钮
	
		(function(){
			$('#go_to_top').waypointInit('animate_horizontal_finished','0px',0,true);
			$('#go_to_top').on('click',function(){
				$('html,body').animate({
					scrollTop : 0
				},500);
			});
		})();

		// 社交小工具
	
		(function(){
			$('.sw_button').on('click',function(){
				$(this).parent().toggleClass('opened').siblings().removeClass('opened');
			});
		})();

		// 响应菜单
	
		window.rmenu = function(){
	
			var menuWrap = $('[role="navigation"]'),
				menu = $('.main_menu'),
				button = $('#menu_button');
	
			function orientationChange(){
				if($(window).width() < 767){
						button.off('click').on('click',function(){
							menuWrap.stop().slideToggle();
							$(this).toggleClass('active');
						});
					menu.children('li').children('a').off('click').on('click',function(e){
						var self = $(this);
						self
							.closest('li')
							.toggleClass('current_click')
							.find('.sub_menu_wrap')
							.stop()
							.slideToggle()
							.end()
							.closest('li')
							.siblings('li')
							.removeClass('current_click')
							.children('a').removeClass('prevented').parent()
							.find('.sub_menu_wrap')
							.stop()
							.slideUp();
						if(!(self.hasClass('prevented'))){
							e.preventDefault();
							self.addClass('prevented');
						}else{
							self.removeClass('prevented');
						}
					});
				}else if($(window).width() > 767){
					menuWrap.removeAttr('style').find('.sub_menu_wrap').removeAttr('style');
					menu.children('li').children('a').off('click');
				}
			}
			orientationChange();
	
			$(window).on('resize',orientationChange);
	
		};
		rmenu();
	
		// 自定义 select
	
		(function(){
	
			$('.custom_select').each(function(){
				var list = $(this).children('ul'),
					select = $(this).find('select'),
					title = $(this).find('.select_title');
				title.css('min-width',title.outerWidth());
	
				// select items to list items
	
				if($(this).find('[data-filter]').length){
					for(var i = 0,len = select.children('option').length;i < len;i++){
						list.append('<li data-filter="'+select.children('option').eq(i).data('filter')+'" class="tr_delay_hover">'+select.children('option').eq(i).text()+'</li>')
					}
				}
				else{
					for(var i = 0,len = select.children('option').length;i < len;i++){
						list.append('<li class="tr_delay_hover">'+select.children('option').eq(i).text()+'</li>')
					}
				}
				select.hide();
	
				// 开启列表
				
				title.on('click',function(){
					list.slideToggle(400);
					$(this).toggleClass('active');
				});
	
				// 选择选项
	
				list.on('click','li',function(){
					var val = $(this).text();
					title.text(val);
					list.slideUp(400);
					select.val(val);
					title.toggleClass('active');
				});
	
			});
	
		})();

		// 小工具
	
		(function(){
			var slider;
			if($('#price').length){
				slider = $('#price').slider({ 
				 	orientation: "horizontal",
					range: true,
					values: [ 0, 237 ],
					min: 0,
					max: 250,
					slide : function(event ,ui){
						$(this).next().find('.first_limit').val('$' + ui.values[0]);
						$(this).next().find('.last_limit').val('$' + ui.values[1]);
					}
				});
			}
	
			var color = $('.select_color').on('click',function(){
				$(this).addClass('active').parent().siblings().children('button').removeClass('active');
			});
	
			$('.close_fieldset').on('click',function(){
				$(this).closest('fieldset').animate({
					'opacity':'0'
				},function(){
					$(this).slideUp();
				});
			});
	
			$('button[type="reset"]:not(#styleswitcher button[type="reset"])').on('click',function(){
				color.eq(0).addClass('active').parent().siblings().children('button').removeClass('active');
				slider.slider( "option", "values", [ 0, 237 ] );
			});
	
			$('.categories_list').on('click','a',function(e){
				if($(this).parent().children('ul').length){
					$(this).parent().toggleClass('active').end().next().slideToggle();
					e.preventDefault();
				}
			});
	
			$('.categories_list > li > a').on('click',function(e){
				if($(this).parent().children('ul').length){
					$(this).toggleClass('scheme_color').toggleClass('color_dark');
					e.preventDefault();
				}
			});
	
		})();

		// 快速视图旋转木马
	
		(function(){
			var qvc = $('.qv_carousel'),
				qvcsingle = $('.qv_carousel_single');
			if(qvc.length){
				var qv = qvc.owlCarousel({
					items: 3,
			 		autoPlay : false,
			 		slideSpeed : 1000,
			 		autoHeight : true
			 	});
	
				$('.qv_btn_prev').on('click',function(){
					qv.trigger('owl.prev');
				});
	
				$('.qv_btn_next').on('click',function(){
					qv.trigger('owl.next');
				});
			}
			if(qvcsingle.length){
				var qvcs = qvcsingle.owlCarousel({
					itemsCustom : [[1199,3],[992,3],[768,3],[480,3],[300,3]],
			 		autoPlay : false,
			 		slideSpeed : 1000,
			 		autoHeight : true
			 	});
	
				$('.qv_btn_single_prev').on('click',function(){
					qvcs.trigger('owl.prev');
				});
	
				$('.qv_btn_single_next').on('click',function(){
					qvcs.trigger('owl.next');
				});
			}
	
		})();

		// quantity
	
		(function(){
			
			$('.quantity').on('click','button',function(){
				var data = $(this).data('direction'),
					i = $(this).parent().children('input[type="text"]'),
					val = i.val();
				if(data == "up"){
					val++;
					i.val(val);
				}else if(data == "down"){
					if(val == 1) return;
					val--;
					i.val(val);
				}
			});
	
		})();

		// popup  
	
		(function(){
	
			$('.popup').on('popup/position',function(){
				var _this = $(this),
				pos = setTimeout(function(){
					var mt = _this.outerHeight() / -2,
						ml = _this.outerWidth() / -2;
					_this.css({
						'margin-left':ml,
						'margin-top':mt
					});
					clearTimeout(pos);
				},100);
			});
	
			var close = $('.popup > .close');
			if($('[data-popup]').length){
				$("body").on('click','[data-popup]',function(e){
					var popup = $(this).data('popup'),
						pc = $(popup).find('.popup');
	
					pc.trigger('popup/position');
	
					$(popup).fadeIn(function(){					
						$(popup).on('click',function(e){
							if($(e.target).hasClass('popup_wrap')){
								$(this).fadeOut();
							}
						});
					});
					e.preventDefault();
				});
			}
			close.on('click',function(){
				$(this).closest('.popup_wrap').fadeOut();
			});
		})();
	
		(function(){
	
			var aItem = $('.accordion:not(.toggle) .accordion_item'),
				link = aItem.find('.a_title'),
				aToggleItem = $('.accordion.toggle .accordion_item'),
				tLink = aToggleItem.find('.a_title');
	
			aItem.add(aToggleItem).children('.a_title').not('.active').next().hide();
	
	
			link.on('click',function(){
	
				 $(this).removeClass('bg_light_color_1 color_dark')
					.addClass('active color_light')
					.next().stop().slideDown()
					.parent().siblings()
					.children('.a_title')
					.removeClass('active color_light')
					.addClass('bg_light_color_1 color_dark')
					.next().stop().slideUp();
	
			});
	
			tLink.on('click',function(){
				$(this).toggleClass('active color_light bg_light_color_1 color_dark')
						.next().stop().slideToggle();
	
			})
	
		})();

		// 相关链接
	
		(function(){
			var rp = $('.related_projects');
			if(rp.length){
				var qv = rp.owlCarousel({
					itemsCustom : rp.hasClass("product_full_width") ? [[1199,4],[992,4],[768,3],[480,1],[300,1]] : [[1199,3],[992,3],[768,3],[480,1],[300,1]],
			 		autoPlay : false,
			 		slideSpeed : 1000,
			 		autoHeight : true
			 	});
	
				$('.rp_prev').on('click',function(){
					qv.trigger('owl.prev');
				});
	
				$('.rp_next').on('click',function(){
					qv.trigger('owl.next');
				});
			}
	
		})();

		// block select
	
		(function(){
			var b = $('.block_select');
	
			b.each(function(){
				var _this = $(this);
				if(_this.find('input[type="radio"]').is(':checked')) _this.addClass('selected');
			});
			b.on('click',function(){
				$(this)
					.addClass('selected')
					.find('input[type="radio"]')
					.prop('checked',true)
					.end()
					.siblings('.selected')
					.removeClass('selected')
			});
	
		})();

		// 特殊的旋转木马
	
		(function(){
	
			var sc = $('.specials_carousel');
			if(sc.length){
				var spc = sc.owlCarousel({
					// singleItem : true,
					itemsCustom : [[1199,1],[992,1],[768,1],[480,2],[300,1]],
			 		autoPlay : false,
			 		slideSpeed : 500,
			 		autoHeight : true,
			 		transitionStyle : "backSlide"
			 	});
	
				$('.sc_prev').on('click',function(){
					spc.trigger('owl.prev');
				});
	
				$('.sc_next').on('click',function(){
					spc.trigger('owl.next');
				});
			}
	
		})();

		function ellipsis(){
			var el = $('.ellipsis').hide();
				el.each(function(){
					var self = $(this);
					self.css({
						'width': self.parent().outerWidth(),
						'white-space' : 'nowrap'
					});
					self.show();
				});
		}
		ellipsis();
		$(window).on('resize',ellipsis);

		// contact form

		(function(){

			var cf = $('#contactform');
			cf.append('<div class="message_container d_none m_top_20"></div>');

			cf.on("submit",function(event){

				var self = $(this),text;

				var request = $.ajax({
					url:"bat/mail.php",
					type : "post",
					data : self.serialize()
				});

				request.then(function(data){
					if(data == "1"){

						text = "Your message has been sent successfully!";

						cf.find('input:not([type="submit"]),textarea').val('');

						$('.message_container').html('<div class="alert_box r_corners color_green success"><i class="fa fa-smile-o"></i><p>'+text+'</p></div>')
							.delay(150)
							.slideDown(300)
							.delay(4000)
							.slideUp(300,function(){
								$(this).html("");
							});

					}
					else{
						if(cf.find('textarea').val().length < 20){
							text = "Message must contain at least 20 characters!"
						}
						if(cf.find('input').val() == ""){
							text = "All required fields must be filled!";
						}
						$('.message_container').html('<div class="alert_box r_corners error"><i class="fa fa-exclamation-triangle"></i><p>'+text+'</p></div>')
							.delay(150)
							.slideDown(300)
							.delay(4000)
							.slideUp(300,function(){
								$(this).html("");
							});
					}
				},function(){
					$('.message_container').html('<div class="alert_box r_corners error"><i class="fa fa-exclamation-triangle"></i><p>Connection to server failed!</p></div>')
							.delay(150)
							.slideDown(300)
							.delay(4000)
							.slideUp(300,function(){
								$(this).html("");
							});
				});


				event.preventDefault();
			});

		})();

		// 时事通讯

		(function(){

			var subscribe = $('#newsletter');
			subscribe.append('<div class="message_container_subscribe d_none m_top_20"></div>');
			var message = $('.message_container_subscribe'),text;

			subscribe.on('submit',function(e){
				var self = $(this);
				
				if(self.find('input[type="email"]').val() == ''){
					text = "Please enter your e-mail!";
					message.html('<div class="alert_box r_corners error"><i class="fa fa-exclamation-triangle"></i><p>'+text+'</p></div>')
						.slideDown()
						.delay(4000)
						.slideUp(function(){
							$(this).html("");
						});

				}else{
					self.find('span.error').hide();
					$.ajax({
						type: "POST",
						url: "bat/newsletter.php",
						data: self.serialize(), 
						success: function(data){
							if(data == '1'){
								text = "Your email has been sent successfully!";
								message.html('<div class="alert_box r_corners color_green success"><i class="fa fa-smile-o"></i><p>'+text+'</p></div>')
									.slideDown()
									.delay(4000)
									.slideUp(function(){
										$(this).html("");
									})
									.prevAll('input[type="email"]').val("");
							}else{
								text = "Invalid email address!";
								message.html('<div class="alert_box r_corners error"><i class="fa fa-exclamation-triangle"></i><p>'+text+'</p></div>')
									.slideDown()
									.delay(4000)
									.slideUp(function(){
										$(this).html("");
									});
							}
						}
					});
				}
				e.preventDefault();
			});

		})();

		//快速预览弹出
		
		(function(){
			var pr = $('.popup_wrap .qv_preview > img');
			$('.popup_wrap .qv_carousel .owl-item:first-child li').addClass('active');
			$('.popup_wrap .qv_carousel').on('click','li:not(.active)',function(){
				$(this).addClass('active').parent().siblings().children('li').removeClass('active');
				var src = $(this).data('src');
				if(!($('html').hasClass('ie9'))){
					pr.addClass('hide');
					setTimeout(function(){
						pr.attr('src',src).removeClass('hide');
					},400);
				}else{
					pr.attr('src',src);
	 			}
				$('.popup_wrap [class*="qv_carousel"]').each(function(){
					var pr = $(this).closest('.qv_carousel_wrap').prev('.qv_preview').children('img');
					$(this).on('click','li',function(){
						var src = $(this).data('src');
						if(!($('html').hasClass('ie9'))){
							pr.addClass('hide');
							setTimeout(function(){
								pr.attr('src',src).removeClass('hide');
							},400);
						}else{
		 				pr.attr('src',src);
						}
					});
		  		});
		  	});
	  
	  	})();

		//提高放大

		(function(){

			if($('[data-zoom-image]').length){

				var button = $('.qv_preview [class*="button_type_"]');

				$("#zoom_image").elevateZoom({
					gallery:'qv_carousel_single',
				    zoomWindowFadeIn: 500,
					zoomWindowFadeOut: 500
				}); 

				button.on("click", function(e){
				  var ez = $('#zoom_image').data('elevateZoom');
					$.fancybox(ez.getGalleryList());
				  	e.preventDefault();
				});
			}

		})();

		// first letter

		(function(){

			var dp = $('[class*="first_letter"]');

			dp.each(function(){
				var self = $(this),
				fl = self.text().charAt(0);
				self.text(self.text().substr(1)).prepend('<span class="fl r_corners t_align_c f_left d_block">'+fl+'</span>');
			});

		})();

	});

	
	
	$(window).load(function(){

		function randomSort(selector,items){

			var sel = selector,
				it = items,
				random = [],
				len = it.length;
			it.removeClass('random');
			if(selector === ".random"){
		  		for(var i = 0; i < len; i++){
		  			random.push(+(Math.random() * len).toFixed());
		  		}
		  		$.each(random,function(i,v){
		  			items.eq(Math.floor(Math.random() * v - 1)).addClass('random');
		  		});
		  	}

		}

		// 同位素

		(function(){
			if($('.products_container').length){

				var container = $('.products_container');

				container.isotope({
				 	itemSelector : '.product_item',
					layoutMode : 'fitRows'
				});

				// filter items when filter link is clicked

				$('.isotope_menu').on('click','button',function(){
					var self = $(this),
					selector = self.attr('data-filter');
					randomSort.call(self,self.data('filter'),container.find('.product_item'));
				  	self.closest('li').addClass('active').siblings().removeClass('active');
				  	container.isotope({ filter: selector });
				});
			}

			// 作品集

			if($('.portfolio_isotope_container').length){

				var container = $('.portfolio_isotope_container');

				container.isotope({
					itemSelector : '.portfolio_item',
					layoutMode : 'fitRows'
				});

				$('#filter_portfolio').on('click','li',function(){
					var self = $(this),
					selector = self.data('filter');
				  	container.isotope({ filter: selector });
				});

			}

			if($('.portfolio_masonry_container').length){

				var container1 = $('.portfolio_masonry_container');

				container1.isotope({
					itemSelector : '.portfolio_item',
					layoutMode : 'masonry',
					masonry: {
					  columnWidth: 10,
					  gutter:0
					}
				});

				$('#filter_portfolio').on('click','li',function(){
					var self = $(this),
					selector = self.data('filter');
				  	container1.isotope({ filter: selector });
				});

			}

		})();

		// 简易的 幻灯片

		(function(){

			var flx = $('.simple_slide_show');

			function showTitle(){
				var f = $(this),
					c = f.data('flexslider').currentSlide;
					
					f.find('.slides')
					.children('li')
					.eq(c+1)
					.children('.simple_s_caption')
					.addClass('active')
					.parent()
					.siblings()
					.children('.simple_s_caption')
					.removeClass('active');
			}
			if(flx.length){
				flx.each(function(){
					var curr = $(this);
					curr.flexslider({
						animation : "slide",
						animationSpeed : 1000,
						prevText: "<i class='fa fa-angle-left'></i>",
						nextText: "<i class='fa fa-angle-right'></i>",
						slideshow:true,
						controlNav:false,
						start:function(){
							showTitle.call(curr);
						},
						after:function(){
							showTitle.call(curr);
						}
					});
				});
			}

		})();

	});
	

})(jQuery);

//检测登录
function checkLogin () {	
	$.post(U("/Member/getUser"), 
		function (data){
    		if(data.status) {
    			$('#user_info').html('<i class="fa fa-user m_right_6"></i><a class="color_blue_3" href="'+U('/User/Home/index/uid/'+data.uid)+'">'+data.username+'</a>');
    			$('.user-show').hide();
    			$('.user-hide').show();
    			if ($('.login_btn').size() > 0) {
    			  $('.login_btn').addClass('disabled').prop('disabled',true);
    			}	 			
    		}
  	}, "json");
 }
 
 
 
//ajax post submit请求
$(function(){		
	$('.ajax-post').click(function(){
        var target,query,form;
        var target_form = $(this).attr('target-form');       
        var that = this;
        var nead_confirm=false;
        if( ($(this).attr('type')=='submit') || (target = $(this).attr('href')) || (target = $(this).attr('url')) ){
            form = $(target_form);                   
            if ($(this).attr('hide-data') === 'true'){//无数据时也可以使用的功能
            	form = $('.hide-data');
            	query = form.serialize();            	
            }else if (form.get(0)==undefined){
            	return false;
            }else if ( form.get(0).nodeName=='FORM' ){            	
                if ( $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                if($(this).attr('url') !== undefined){
                	target = $(this).attr('url');

                }else{
                	target = form.get(0).action;
                }                 
                query = form.serialize();
            }else if( form.get(0).nodeName=='INPUT' || form.get(0).nodeName=='SELECT' || form.get(0).nodeName=='TEXTAREA') {                
                form.each(function(k,v){
                    if(v.type=='checkbox' && v.checked==true){
                        nead_confirm = true;
                    }
                })
                if ( nead_confirm && $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                query = form.serialize();
            }else{
                if ( $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                query = form.find('input,select,textarea').serialize();
            }
            $(that).addClass('disabled').attr('autocomplete','off').prop('disabled',true);
            $.post(target,query).success(function(data){
                if (data.status==1) {
                    if (data.url) {
                        infoAlert(data.info + ' 页面即将自动跳转~',true);
                    }else{
                        infoAlert(data.info,true);
                    }
                    setTimeout(function(){
                    	$(that).removeClass('disabled').prop('disabled',false);
                        if (data.url) {
                            location.href=data.url;
                        }
                    },1500);
                }else{
                    infoAlert(data.info);
                    setTimeout(function(){
                    	$(that).removeClass('disabled').prop('disabled',false);
                        if (data.url) {
                            location.href=data.url;
                        }
                    },1500);
                }
            });
        }
        return false;
    });
    
    //评分
	$(".rater").jRating({
		bigStarsPath : JY.PUBLIC+'/static/jRating/icons/stars2.png',
		smallStarsPath : JY.PUBLIC+'/static/jRating/icons/small.png', 
		phpPath : U('/music/rater'), 
		type : 'big', 
		rateMax: 10,
		nbRates : 3,
		//decimalLength:2,
		 //step:true,
		 length : 5, // 星星的数量
		 onSuccess : function(data){        	
		   infoAlert('评分成功,谢谢支持！',true);
		 }
	});	
	//ajax  无刷新登录
	$('.ajax-login').click(function () {
		var form =$('#loginFormBox');
		var target = form.get(0).action,
			query = form.serialize();
		$.post(target,query).success(function(data){
			if (data.status==1) {
				infoAlert(data.info,true);
				checkLogin ();
				$('.close').click();
			}else{
				infoAlert(data.info);
				 setTimeout(function(){
                        if (data.url) {
                            location.href=data.url;
                        }
                   },1500);
			}			 	
		})
		return false;	
	});    
    //ajax退出登录   
	$('#login_out').click(function () {
		$.post(U("/Member/logout"), 
		function (data){
			infoAlert(data.info,true);
    		if(data.status) {
    			$('.user-show').show();
    			$('.user-hide').hide();   			 			
    		}
  		}, "json");
		return false;
	})
	//
 	//公共添加按钮操作	
 	$(document).on("click", ".music-action-btn",function(){
		self = $(this);
	    var url,remove=false,callbck=false;
	    	m_id = self.attr('data-id'),
	    	m_action = self.attr('data-action'),
	    	m_type = self.attr('data-type');	    	
	    	m_type =  !m_type? 'song' : m_type;
	    switch (m_action) {
	    	case 'fav': url = U("/Music/addFav");	callbck= 'replacebtn';break;
			case 'delfav': url = U("/Music/delFav"); remove=true; callbck= 'replacebtn';break;
	    	case 'recommend': url = U("/Music/addRecommend");	callbck= 'replacebtn'; break;
			case 'delrecommend': url = U("/Music/delRecommend"); callbck= 'replacebtn'; remove=true; break;
	    	case 'like': url = U("/Music/addLike");callbck= 'replacebtn'; break;
			case 'dellike': url = U("/Music/delLike"); callbck= 'replacebtn'; remove=true; break;
	    	case 'follow': url = U("/Music/addFollow"); callbck= 'replacefollow';break;
			case 'delfollow':url = U("/Music/delFollow"); callbck= 'replacefollow';break;	    	
			case 'dellisten': url = U("/Music/delListen"); remove=true; break;
	    	case 'deldown': url = U("/Music/delDown"); remove=true; break;
			default: infoAlert('按钮设置不正确！');
	    	
	    }
	    $.ajax({type: "POST",url:url,data: {'id':m_id,'type':m_type,},dataType:"json",success: function(data){
	        	if(data){		        	
		        	if(data.status == 1){
		        		var numobj = self.find('.num');
		        		if (numobj.size() > 0){
		        			var num = numobj.html();
		        		 	numobj.html(Number(num)+1);
		        		}
		        		//判断是否删除元素
		        		if(remove){
		        			var classname = self.attr('remove-parent');
		        			var parent = self.parents(classname);
		        			if (parent.size() > 0) self.parents(classname).remove();
		        		}						
						if(callbck){eval(callbck+'()');}
		        		infoAlert(data.info,true);		        			        	
		        	}else{
		        		infoAlert(data.info);
		        	}
	        	}
	        }
	    });
	    return false;
	});
		
	$(".down_muisc").on("click",function(){
		var _this = $(this),
			_gold = $(this).attr('data-gold'),
			_type = $(this).attr('data-type'),
	    	_id = $(this).attr('data-id');
	    if ( _gold != '0' ) {
        	if(!confirm('下载本歌曲需要'+_gold+'积分，24小时不会重复扣除')){
            	return false;
        	}
		}  
	    $.ajax({
	        type: "POST",
	        url:U('/Down/check'),
	        data: {'id':_id,'type':_type},
	        dataType:"json",
	        success: function(data){
	            if(data.status==0){
	            	//提示错误
	            	infoAlert(data.info);
		        	return false;
	            }else if(data.status==1){
	            	var that = _this.find('.num');            	
	            	if(that.length > 0){
	            		var num = that.html();
	            		 num.html(Number(num)+1);
	            	}
					//
					setTimeout(function(){
						window.location.href=data.url;//免费歌曲											
					},1500);
	            }else if(data.status==2){
	            	//没有登录 弹出登录
		        	$('#login_btn').click();
		        	return false;
	            }
	        }
	    });
	    return false;
	});

});

//自定义弹出层
function infoAlert (text,type) {
	var infoClass,html,fa;
	if (type){
		infoClass = 'success color_green';
		fa = 'fa-smile-o';
	}else{
		infoClass = 'error';
		fa = 'fa-exclamation-triangle';
	}
	html = '<div role="alert" class=" alert alert_box r_corners '+infoClass+' m_bottom_10" style="display:none">'+
			    '<button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>'+								
				'<i class="fa '+fa+'"></i>'+
				'<span class="f_size_ex_large">'+text+' </span>'+
			'</div>';
	$('body').append(html);
	//alert($('.alert').height());
	var box=$('.alert');
	var _scrollHeight = $(document).scrollTop(),//获取当前窗口距离页面顶部高度 
	_windowHeight = $(window).height(),//获取当前窗口高度 
	_windowWidth = $(window).width(),//获取当前窗口宽度 
	_popupHeight = box.height(),//获取弹出层高度 
	_popupWeight = box.width();//获取弹出层宽度 
	_posiTop = (_windowHeight - _popupHeight)/2 + _scrollHeight; 
	_posiLeft = (_windowWidth - _popupWeight)/2; 
	box.css({"left": _posiLeft + "px","top":"0px",'position':'absolute','width':'auto','z-index':'9999'}).show();//设置position*/
	box.animate({top: '+'+_posiTop + "px"}, "slow");
	window.setTimeout(function () {box.alert('close')},3000); //定时关闭		
}


 /*关注替换*/
function replacefollow(){ 
	var m_action = self.attr('data-action');
	if ( m_action == 'delfollow') {
		self.attr('data-action','follow');
		self.addClass('green');
		self.html('<i class="fa fa-plus"></i>  添加关注');
	}else{
		self.attr('data-action','delfollow');
		self.removeClass('green');
		self.html('<i class="fa fa-times"></i>  取消关注');
	} 
} 

/*替换按钮*/
function replacebtn(){ 
	var m_action = self.attr('data-action');
	if (m_action.indexOf("del")) {
		var newstr=m_action.substring(0,m_action.length+3);
		self.attr('data-action',newstr);
	}else{
		self.attr('data-action','del'+m_action);
	} 
} 

/*js模拟think U方法*/
function U (url, vars, suffix){
		var info = parse_url(url), path = [], param = {}, reg;

		/* 验证info */
		info.path || $.error("url格式错误！");
		url = info.path;
		/* 组装URL */
		if(0 === url.indexOf("/")){ //路由模式
			JY.MODEL[0] == 0 && $.error("该URL模式不支持使用路由!(" + url + ")");
			
			/* 去掉右侧分割符 */
			if("/" == url.substr(-1)){
				url = url.substr(0, url.length -1)
			}
			url = ("/" == JY.DEEP) ? url.substr(1) : url.substr(1).replace(/\//g, this.DEEP);
			url = "/" + url;
		} else { //非路由模式
			
			/* 解析URL */
			path = url.split("/");
			path = [path.pop(), path.pop(), path.pop()].reverse();
			path[1] || $.error("U(" + url + ")没有指定控制器");

			if(path[0]){
				param[JY.VAR[0]] = JY.MODEL[1] ? path[0].toLowerCase() : path[0];
			}

			param[JY.VAR[1]] = JY.MODEL[1] ? parse_name(path[1]) : path[1];
			param[JY.VAR[2]] = path[2].toLowerCase();

			url = "?" + $.param(param);
		}
		
		/* 解析参数 */
		if(typeof vars === "string"){
			vars = parse_str(vars);
		} else if(!$.isPlainObject(vars)){
			vars = {};
		}

		/* 解析URL自带的参数 */
		info.query && $.extend(vars, parse_str(info.query));

		if($.param(vars)){
			url += "&" + $.param(vars);
			
		}
		if(0 != JY.MODEL[0]){
			url = url.replace("?" + (path[0] ? JY.VAR[0] : JY.VAR[1]) + "=", "/")
				     .replace("&" + JY.VAR[1] + "=", JY.DEEP)
				     .replace("&" + JY.VAR[2] + "=", JY.DEEP)
				     .replace(/(\w+=&)|(&?\w+=$)/g, "")
				     .replace(/[&=]/g, JY.DEEP);
			/* 添加伪静态后缀 */
			if(false !== suffix){
				suffix = suffix || JY.MODEL[2].split("|")[0];
				if(suffix){
					url += "." + suffix;
				}
			}
		}

		url = JY.APP + url;
		return url;
}
function parse_url (url){
	var parse = url.match(/^(?:([a-z]+):\/\/)?([\w-]+(?:\.[\w-]+)+)?(?::(\d+))?([\w-\/]+)?(?:\?((?:\w+=[^#&=\/]*)?(?:&\w+=[^#&=\/]*)*))?(?:#([\w-]+))?$/i);
	parse || $.error("url格式不正确！");
	return {
		"scheme"   : parse[1],
		"host"     : parse[2],
		"port"     : parse[3],
		"path"     : parse[4],
		"query"    : parse[5],
		"fragment" : parse[6]
	};
}

function parse_str (str){
	var value = str.split("&"), vars = {}, param;
	for(val in value){
		param = value[val].split("=");
		vars[param[0]] = param[1];
	}
	return vars;
}

function parse_name (name, type){
	if(type){
		/* 下划线转驼峰 */
		name.replace(/_([a-z])/g, function($0, $1){
			return $1.toUpperCase();
		});

		/* 首字母大写 */
		name.replace(/[a-z]/, function($0){
			return $0.toUpperCase();
		});
	} else {
		/* 大写字母转小写 */
		name = name.replace(/[A-Z]/g, function($0){
			return "_" + $0.toLowerCase();
		});

		/* 去掉首字符的下划线 */
		if(0 === name.indexOf("_")){
			name = name.substr(1);
		}
	}
	return name;
}