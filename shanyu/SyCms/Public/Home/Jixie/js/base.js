// JavaScript Document

(function($){
	  //tab 选项卡
$.fn.cityStarTab=function(option){
		var defaults={
					hdClass:"cityStarTab-hd",
					bdClass:"cityStarTab-bd",
					hoverClass:"oncurr",
					bindEvent:"click",  
					Index:0
			}
			var handle=function(objElem,currIndex,className){
				$.each(objElem,function(index,elem){
										if(index==currIndex){
												if(className){
													$(elem).addClass(className);
												}else{
													$(elem).css("display","block");
													}
										}else{
												if(className){
													$(elem).removeClass(className);
												}else{
													$(elem).css("display","none");
													}
										}
					})
	}
		var opts=$.extend(defaults,option);
		var $hdItems=$(this).find("."+opts.hdClass);
		var $bdItems=this.find("."+opts.bdClass);	
		$hdItems.eq(0).addClass(opts.hoverClass);
		$bdItems.eq(0).css("display","block");
		if($hdItems.length!=$bdItems.length) return false;
		$hdItems.each(function(){
					$(this).bind(opts.bindEvent,function(){
							
									opts.Index=$hdItems.index(this);
									handle($hdItems,opts.Index,opts.hoverClass);
									handle($bdItems,opts.Index);
					})
		})
		
};
})(jQuery);

//banner 的js
(function($){
$.fn.bannerSlider=function(options){

			var defaults={
											width:950,
											height:355,
											auto:true,
											effect:"slider",
											Pause:3000,
											Time:250,
											zIndex:10,
											pagination:{
														show:true,
														id:"J_bs-numlist",
													 className:"bs-numlist",
														oncurrClass:"oncurr"
												},
											dirBtn:{
													show:true,
													dynamic:true,
												active:false,
												prevClass:"bs-prev",
												nextClass:"bs-next",
												prevId:"J_bs-prev",
												nextId:"J_bs-next"  
											},
											describe:false			 
		};
		
		var opts=$.extend(true,defaults,options),$this=this;
			this.Index=0,
			this.slide_lis=$(this).children(),
			this.len=this.slide_lis.length,
						this.parent=this.parent();
						
						this.islock=false,
						this.dir=1,
					
						this.Timer=null,
						this.isFirst=true;
	
			this.prevBtn,
			this.nextBtn,
			this.pagination,
			this.pagination_lis,
			this.describe;
		
		var init=function(){
			   $this.parent.css({width:opts.width,height:opts.height,position:"relative",overflow:"hidden"});
						$this.css({width:opts.width,height:opts.height,position:"relative"});
						if(opts.effect=="slider"){
								$this.slide_lis.css("position","absolute").eq($this.Index).css({zIndex:opts.zIndex,display:"block"}).siblings().css({zIndex:opts.zIndex-1}).hide();
						}else if(opts.effect=="fade"){
							$this.slide_lis.css({zIndex:opts.zIndex-1,position:"absolute"}).stop(true).animate({"opacity":0}).eq($this.Index).css("z-index",opts.zIndex).stop(true).animate({"opacity":1});
						}
						
						var prevBtn,nextBtn,pagination,visibleStr;
						if(opts.dirBtn.show){
							   
										if(!opts.dirBtn.active){
													visibleStr="display:block";
										}else{
													visibleStr="display:none";
										}
										if(opts.dirBtn.dynamic){
								prevBtn=$('<a href="#" id="'+opts.dirBtn.prevId+'" class="'+opts.dirBtn.prevClass+'" style="'+visibleStr+'"></a>');
								nextBtn=$('<a href="#" id="'+opts.dirBtn.nextId+'" class="'+opts.dirBtn.nextClass+'" style="'+visibleStr+'"></a>');
								prevBtn.appendTo($this.parent); 
								nextBtn.appendTo($this.parent);          
										 }
								
								
								$this.prevBtn=$("#"+opts.dirBtn.prevId);
								$this.nextBtn=$("#"+opts.dirBtn.nextId);			 
					}
					if(opts.pagination.show){
								var pagination="<ul id="+opts.pagination.id+" class="+opts.pagination.className+">"
								for(var i=0;i<$this.len;i++){
											pagination+="<li></li>";	
								}
								pagination+="</ul>";
								$(pagination).appendTo($this.parent);
								$this.pagination=$("#"+opts.pagination.id);
								$this.pagination_li=$this.pagination.children();
								$this.pagination_li.eq($this.Index).addClass(opts.pagination.oncurrClass)
					}
				   if(opts.describe){
					     var $describe=$("<div id=\"J_bannerDescribe\" class=\"bannerDesc\"></div>");
						 $describe.appendTo($this.parent);
						 $this.describe=$describe;
						 if($.trim($this.slide_lis.eq($this.Index).attr("data-title"))==""){
							 $this.describe.hide() 
					     }else{
							    $this.describe.text($this.slide_lis.eq($this.Index).attr("data-title"));
						 }
				   }
			
		}
		init();
		
	
	function beginStart(){
					if($this.Index < 0){
								$this.Index=$this.len-1;
					}else if($this.Index==$this.len){
									$this.Index=0
						}	
					scrolling();
	};
	function slideScroll(li_dirW,this_dirW){
					 $this.slide_lis.eq($this.Index).css({left:li_dirW,zIndex:opts.zIndex,display:"block"});
					 
					$this.stop(false,true).animate({left:this_dirW},opts.Time,function(){
									$this.slide_lis.eq($this.Index).css("left",0).siblings().css({zIndex:opts.zIndex-1,display:"none"});
									$(this).css("left",0);
					})
	};
	function  switchDirEffect(dir,effectType){
		   if(effectType=="slider"){
								var li_dirW=null,this_dirW=null;
									switch(dir){
												 case 0://左按钮
																					li_dirW=-opts.width;
																					this_dirW=opts.width;
																				slideScroll(li_dirW,this_dirW);
													  break;
												 case 1://右按钮
															li_dirW=opts.width;
																				this_dirW=-opts.width; 
																					slideScroll(li_dirW,this_dirW)
													break;
									}
				}else if(effectType=="fade"){
						   $this.slide_lis.css("zIndex",opts.zIndex-1).stop(true).animate({opacity:0}).eq($this.Index).css("zIndex",opts.zIndex).stop(true).animate({opacity:1});
					}
		
					
			 
	};	
	function scrolling(){
				if($this.isFirst){
								$this.isFirst=false
				}else{
								switchDirEffect($this.dir,opts.effect)
								if(opts.pagination.show){	
									 $this.pagination_li.eq($this.Index).addClass(opts.pagination.oncurrClass).siblings().removeClass(opts.pagination.oncurrClass)
								}
			}
			if(opts.describe){
			if($.trim($this.slide_lis.eq($this.Index).attr("data-title"))==""){
					     
						 $this.describe.hide();
					  }else{
						 
						 $this.describe.text($this.slide_lis.eq($this.Index).attr("data-title")).show() 
					   }
			}
			if(!$this.islock && opts.auto){
						$this.Timer=setTimeout(function(){$this.Index++;beginStart();},opts.Pause)
				}
	}
	
	$this.bind("mouseover",function(){
	$this.islock=true;
	clearTimeout($this.Timer)
	}).bind("mouseout",function(){
	$this.islock=false;
	$this.isFirst=true;
	beginStart()
	})
	
	if(opts.dirBtn.active){
	$this.parent.bind("mouseover",function(event){
		var relateElem = event.relatedTarget;
		if($(relateElem).closest($this.parent).length > 0){
				return;
		}else{
							$this.prevBtn.stop(false,true).fadeIn();
							$this.nextBtn.stop(false,true).fadeIn()
		}
	}).bind("mouseout",function(event){
				var relateElem = event.relatedTarget;
				if($(relateElem).closest($this.parent).length > 0){
							return;
					}else{
							$this.prevBtn.stop(false,true).fadeOut();
							$this.nextBtn.stop(false,true).fadeOut()
						}
			
	});	
	
	} 	
	if(opts.dirBtn.show){			
	$this.prevBtn.bind("click",function(){
	if(!$this.is(":animated")){
		clearTimeout($this.Timer)
		$this.Index--;
		$this.dir=0;
	  
		beginStart();
	}
	return false;	
	}).bind("mouseover",function(){
		clearTimeout($this.Timer);
		$this.islock=true;
	}).bind("mouseout",function(){
		$this.isFirst=true;
		$this.islock=false;
		 
		$this.dir=1;
		beginStart()
	})
	$this.nextBtn.bind("click",function(){
	if(!$this.is(":animated")){
		clearTimeout($this.Timer)
		$this.Index++;
		$this.dir=1;
	   
		beginStart()	
	}
	return false;	
	}).bind("mouseover",function(){
		$this.islock=true;
		clearTimeout($this.Timer)
	}).bind("mouseout",function(){
	$this.isFirst=true;
	$this.islock=false;
		beginStart()
	})
	}
	if(opts.pagination.show){		
	$this.pagination_li.each(function(){
	$(this).bind("click",function(){
								$this.islock=true;
								clearTimeout($this.Timer)
								var lastIndex=$this.Index;
							$this.Index=$this.pagination_li.index(this);
							if(lastIndex < $this.Index){
										$this.dir=1;
											beginStart()	
							}else if(lastIndex > $this.Index){
										$this.dir=0;
											beginStart()	
							}else{
										return
								}		
	})	
	})
	}
	beginStart()	
																			
	};
	
})(jQuery)
//相册的js
;(function($){
	$.fn.staffGallery=function(option){
			
			var defaultsId={
				     bigPrevId:"J_staff-bigPrev",
									bigNextId:"J_staff-bigNext",
									bigPicTitId:"J_staff-bigPicTit",
									bigPicTxtId:"J_staff-bigPicTxt",
									smallListWrapId:"J_staff-smallListWrap",
									containerId:"J_staff-conBox"
									
							
		   }	
$.fn.staffGallery.defaults = {
									smallPrevId:"J_staff-smallPrev",
									smallNextId:"J_staff-smallNext",
									smallListInnerId:"J_staff-smallListInner",
									smallDisPrevClass:"staff-dis-smallPrev",
									smallDisNextClass:"staff-dis-smallNext",
									smallDataClass:"staff-smallItem",
									smallWrapId:"J_staff-smallWrap",
									bigPicTitClass:"staff-bigPicTit",
									arrowId:"J_staff-arrow",
									bigPicWrapId:"J_staff-bigWrap",
									bigPrevClass:"staff-bigPrev",
									bigNextClass:"staff-bigNext",
									bigPicWrapW:666,
									bigPicWrapH:483,
									fixLen:4,//第几张固定
									viewLen:6,//滚动可见的长度
									arrowH:35,
									scroW:103,
									dir:"left",
									isTitShow:true,
									isBigBtnShow:true //是否显示标题													
	}
		      		var opts = $.extend(defaultsId, $.fn.staffGallery.defaults, option);				     
							var Index=0,			
							       $smallPrev=$("#"+opts.smallPrevId),
							       $smallNext=$("#"+opts.smallNextId),
							       $container=$("#"+opts.containerId),
								   $smallListInner=$("#"+opts.smallListInnerId),
							       $arrow=$("#"+opts.arrowId),
							       $bigPicWrap=$("#"+opts.bigPicWrapId);
										 $smallListWrap=$("#"+opts.smallListWrapId);
										 $smallWrap=$("#"+opts.smallWrapId)
										
							var nextDistance=opts.viewLen-opts.fixLen,
                  $smallDataItems=$smallListInner.find("."+opts.smallDataClass);
								  totalLen=$smallDataItems.length;
							var	bigSrc,
							    bigTit,
									$bigPicTit,
									$bigNext,
									$bigPrev,
									$bigPicTit,
									$bigPicTxt,
		              $this=this;

										  
											 
				//获取图片信息和操作图片函数
								var changeBigImg=function(currIndex){
								        bigSrc=$smallDataItems.eq(currIndex).attr("href");
												bigTit=$smallDataItems.eq(currIndex).attr("data-title");
												$this.stop(false,true).fadeOut(200,function(){
												$this.attr("src",bigSrc).fadeIn()
												});
												if(opts.isTitShow){      
												   $bigPicTit.animate({top:opts.bigPicWrapH},200,function(){
																				if($.trim(bigTit) !=""){
																						 $bigPicTxt.text(bigTit);
																						 $bigPicTit.show();	
																				 }else{
																							$bigPicTit.hide();	
																					}		
																	       $bigPicTit.stop(false,true).animate({top:opts.bigPicWrapH-opts.arrowH})								
																	})
																	
													
												}
								};

				
				//初始化
				 var Init=function(){
					             if(opts.dir=="left"){
					               $smallWrap.css({width:opts.bigPicWrapW,overflow:"hidden"});
								 }
								   $this.parent().css({width:opts.bigPicWrapW-2,height:opts.bigPicWrapH-2,overflow:"hidden"})
					               if (totalLen == 1) {
                                       $smallWrap.hide();
									   
                                    }
						         $bigPicWrap.css({width:opts.bigPicWrapW,height:opts.bigPicWrapH,overflow:"hidden",position:"relative"})
								      bigSrc=$smallDataItems.eq(Index).attr("href");
											bigTit=$smallDataItems.eq(Index).attr("data-title");
											$this.attr("src",bigSrc);
											if(opts.isTitShow){//如果显示标题的话
											    var $bigPicTxtDom=$('<div class="'+opts.bigPicTitClass+'" id="'+opts.bigPicTitId+'"><span></span><b id="'+opts.bigPicTxtId+'"></b></div>')
												  $bigPicTxtDom.appendTo($bigPicWrap);
													$bigPicTit=$("#"+opts.bigPicTitId);
							            $bigPicTxt=$("#"+opts.bigPicTxtId);
													$bigPicTit.css({top:opts.bigPicWrapH-opts.arrowH}).hide();
													changeBigImg(Index)
											}
											if(opts.isBigBtnShow){//如果显示大图按钮的话
												         var $bigPrevBtn=$("<a href='#' class='"+opts.bigPrevClass+"' id='"+opts.bigPrevId+"'></a>");
																 var $bigNextBtn=$("<a href='#' class='"+opts.bigNextClass+"' id='"+opts.bigNextId+"'></a>");
																	$bigPrevBtn.appendTo($bigPicWrap);
																	$bigNextBtn.appendTo($bigPicWrap);  
											}
											$smallPrev.eq(Index).addClass(opts.smallDisPrevClass);
											if(totalLen <= opts.viewLen){
								        
								      }else{
												 if(opts.dir=="left"){ 
									        $smallListInner.width(totalLen*opts.scroW)
												 }else{
													  $smallListInner.height(totalLen*opts.scroW)
													}
								      }	
											 $smallListInner.css({"position":"absolute","overflow":"hidden"}); 
									    if(opts.dir=="left"){
												 $smallListWrap.css({"width":opts.viewLen*opts.scroW,"position":"relative","overflow":"hidden"});
											}else{
												 $smallListWrap.css({"height":opts.viewLen*opts.scroW,"position":"relative","overflow":"hidden"});
											}
									 
									 
							
					 };
					Init()
			
				      //处理index 范围的函数
					     var slideIndexChange=function(currIndex,Dir){
								   if(Dir=="left"){
									   if(totalLen <opts.viewLen){
										  $arrow.stop(false,true).animate({left:currIndex*opts.scroW},200);
										  $smallListInner.animate({left:0*opts.scroW})	
									    }else{
							            if(currIndex >=0 && currIndex <=opts.fixLen-1){//0-3
													   	$arrow.stop(false,true).animate({left:currIndex*opts.scroW},200);
															$smallListInner.animate({left:0*opts.scroW})		
													}else if(currIndex >(opts.fixLen-1) && currIndex <(totalLen-nextDistance)){//4
													
															  $arrow.stop(false,true).animate({left:(opts.fixLen-1)*opts.scroW},200);
																$smallListInner.animate({left:-(currIndex-opts.fixLen+1)*opts.scroW})	
													}else if(currIndex >=(totalLen-nextDistance)){//5-7  
													
														  	$arrow.stop(false,true).animate({left:(opts.viewLen-(totalLen-currIndex))*opts.scroW},200);
																$smallListInner.animate({left:-(totalLen-opts.viewLen)*opts.scroW})				
													}
										}
									  }else if(Dir=="top"){
										  if(totalLen <opts.viewLen){
											  $arrow.stop(false,true).animate({top:currIndex*opts.scroW},200);
											  $smallListInner.animate({left:0*opts.scroW})	
											}else{
											   if(currIndex >=0 && currIndex <=opts.fixLen-1){//0-3
															$arrow.stop(false,true).animate({top:currIndex*opts.scroW},200);
																$smallListInner.animate({top:0*opts.scroW})		
														}else if(currIndex >(opts.fixLen-1) && currIndex <(totalLen-nextDistance)){//4
																  $arrow.stop(false,true).animate({top:(opts.fixLen-1)*opts.scroW},200);
																	$smallListInner.animate({top:-(currIndex-opts.fixLen+1)*opts.scroW})	
														}else if(currIndex >=(totalLen-nextDistance)){//5-7   
																$arrow.stop(false,true).animate({top:(opts.viewLen-(totalLen-currIndex))*opts.scroW},200);
																	$smallListInner.animate({top:-(totalLen-opts.viewLen)*opts.scroW})				
														}
											   }
										  }
							 }
					
							//单击prev按钮的事件函数
								var prevBtnHander=function(currIndex){
							      if(currIndex !=totalLen-1){
													  $smallNext.removeClass(opts.smallDisNextClass);  	
													}
													if(currIndex==0){
														  $smallPrev.addClass(opts.smallDisPrevClass);
													}
												slideIndexChange(Index,opts.dir);
												
													changeBigImg(Index)
							};
						//单击next按钮的事件函数
							 var NextBtnHander=function(currIndex){
											    if(Index !=0){
																 $smallPrev.removeClass(opts.smallDisPrevClass);
															}
															if(Index==(totalLen-1)){
																		$smallNext.addClass(opts.smallDisNextClass) 
															}
														
															if(Index <=opts.fixLen-1){
																  if(opts.dir=="left"){ 
													          $arrow.stop(false,true).animate({left:"+="+opts.scroW},200)	
																	}else{
																		 $arrow.stop(false,true).animate({top:"+="+opts.scroW},200)	
																		}
												      }else{
																				if((totalLen-Index-nextDistance)<=0){
																							 if(opts.dir=="left"){ 	
																									$arrow.stop(false,true).animate({left:"+="+opts.scroW},200);
																							 }else{
																								 $arrow.stop(false,true).animate({top:"+="+opts.scroW},200);
																								 }
																					}else{
																						   if(opts.dir=="left"){ 	
																									 $smallListInner.animate({left:-(Index-opts.fixLen+1)*opts.scroW})
																							 }else{
																								 $smallListInner.animate({top:-(Index-opts.fixLen+1)*opts.scroW})
																								 } 																									
																					   
																					} 
												     }
															changeBigImg(Index)  	
														
									};
					
										
						//单击小图的左按钮	
					$smallPrev.bind("click",function(){
								if(Index==0){ return false}
								   Index--;
										 prevBtnHander(Index)
											return false   	
					});
					
					
						//单击小图的右按钮	
						$smallNext.bind("click",function(){
								  if(Index==(totalLen-1)){return false}
							    Index++;
											NextBtnHander(Index);
											return false;
						});
						$smallDataItems.each(function(){
					    $(this).bind("click",function(){
										   var lastIndex=Index
									    Index=$smallDataItems.index(this);
										   if(Index==0){$smallPrev.addClass(opts.smallDisPrevClass);}
             if(Index==totalLen-1){$smallNext.addClass(opts.smallDisNextClass)}
													if(Index!=0){$smallPrev.removeClass(opts.smallDisPrevClass);}
             if(Index!=totalLen-1){$smallNext.removeClass(opts.smallDisNextClass)}
													var distanceIndex=Math.abs(lastIndex-Index);
													changeBigImg(Index);
													slideIndexChange(Index,opts.dir);
													return false;
									})	
					})
					
				 if(opts.isBigBtnShow && totalLen !=1){	
				     $bigNext=$("#"+opts.bigNextId);
					 var bigNextH=$bigNext.height();
					   $bigPrev=$("#"+opts.bigPrevId);
					   var bigPrevH=$bigPrev.height();
					  $bigNext.css("top",(opts.bigPicWrapH-bigNextH)/2);
					  $bigPrev.css("top",(opts.bigPicWrapH-bigPrevH)/2)
				   //单击大图的右按钮
						$bigNext.bind("click",function(){
										   Index++;		
											 if(Index !=0){$bigPrev.show();}
											 if(Index==totalLen-1){$(this).hide()}
                       NextBtnHander(Index)
											 return false;
						})
				//单击大图的左按钮
					$bigPrev.bind("click",function(){
						  
								Index--;
								if(Index !=totalLen-1){$bigNext.show();}
								if(Index==0){$(this).hide()}
								prevBtnHander(Index);
								return false
					});
				//大图 $bigWrap hover的时候的效果
				$bigPicWrap.bind("mouseover",function(event){
							var relateElem = event.relatedTarget;
							if($(relateElem).closest($bigPicWrap).length > 0){
									return;
							}else{
										if(Index==totalLen-1){
												$bigPrev.stop(false,true).fadeIn();
										}else if(Index==0){
													$bigNext.stop(false,true).fadeIn()
										}else{
												$bigPrev.stop(false,true).fadeIn();
												$bigNext.stop(false,true).fadeIn()
											}
							}
					}).bind("mouseout",function(event){
									var relateElem = event.relatedTarget;
									if($(relateElem).closest($bigPicWrap).length > 0){
												return;
										}else{
												$bigPrev.stop(false,true).fadeOut();

												$bigNext.stop(false,true).fadeOut()
											}
								
						});
				}
												
}
})(jQuery)
//头部导航
;(function($){
   $.fn.CSMenu=function(options){
		   var defaults={
				    maxWidth:980,
					align:"left",
					isFloat:false,
					subNavClass:"nav-sub",
					hoverClass:"onHover",
				    navWrapId:"J_navWrap",
					arrowW:38,
					zIndex:990,
					pushMenuDownBy:36
			 }
			 var opts=$.extend(defaults,options);
			 var $navWrap=$("#"+opts.navWrapId),$this=this,$child_lis,$subNav,Index,isFirst=true;
			function bindData(navSubW,elem,elemW,posTop,posLeft,$navSub){
				    var arrowLeft=null;
						if(elemW > navSubW){navSubW=elemW};
						if(navSubW > opts.maxWidth){navSubW=opts.maxWidth;}
				    var floatNavLeft=null;
					var pos_C_L=posLeft+elemW/2;
			        var pos_C_R=opts.maxWidth-(posLeft+elemW/2);
				    var pos_L_R=opts.maxWidth-posLeft;
					if(opts.isFloat){
				       if($navSub.find("li").length==1){
						  $navSub.addClass("nav-subSingle"); 
					    } 
					}
						if(navSubW > opts.maxWidth){
							    arrowLeft=posLeft+(elemW-opts.arrowW)/2;
								floatNavLeft=0;
								$navSub.addClass("center")
						}else{
							if(opts.align=="center"){
								 if(pos_C_L < navSubW/2){  
									 //如果li的中心到达左岸的距离小于"子导航"的一半
									   if(pos_L_R >= navSubW){//如果li的左边到右岸的距离 大于navSubW的时候，左对齐
											  arrowLeft=(elemW-opts.arrowW)/2;
												floatNavLeft=posLeft;
												$navSub.addClass("left");
											}else if(pos_L_R < navSubW){//如果li的左边到右岸的距离 大于navSubW的时候，左岸对其
											  arrowLeft=posLeft+(elemW-opts.arrowW)/2;
												floatNavLeft=0;
												$navSub.addClass("center");
											}
								 }else if(pos_C_L >= navSubW/2){
									 //如果li的中心到达左岸的距离大于等于"子导航"的一半
									 if(pos_C_R >= navSubW/2){ 
										//如果li的中心到右岸的距离 大于navSubW一半的时候，剧中
											arrowLeft=(navSubW-opts.arrowW)/2;
												floatNavLeft=posLeft-(navSubW-elemW)/2;
												$navSub.addClass("center");
										}else if(pos_C_R < navSubW/2){ 
										   //如果li的中心到右岸的距离 小于navSubW一半的时候，直接右对齐
											arrowLeft=navSubW-(elemW+opts.arrowW)/2;
												floatNavLeft=posLeft+elemW-navSubW;
												$navSub.addClass("right");
										}
								 }
						    }else if(opts.align=="left"){
								  if(navSubW <=elemW){
									         arrowLeft=navSubW-(elemW+opts.arrowW)/2;
											 floatNavLeft=posLeft;
											 $navSub.addClass("center"); 
								  }else{	
										if(pos_L_R < navSubW){//右对齐
											 arrowLeft=navSubW-(elemW+opts.arrowW)/2;
											 floatNavLeft=posLeft+elemW-navSubW;
											 $navSub.addClass("right"); 
										}else if(pos_L_R >=navSubW){//左对齐
											 arrowLeft=(elemW-opts.arrowW)/2;
											 floatNavLeft=posLeft;
											 $navSub.addClass("left"); 
										}
								  }
							 } 
						 }
						 elem.data({"posLeft":floatNavLeft,"arrowLeft":arrowLeft,"floatNavW":navSubW});
					
								
			 }
			 var floatMenuObj=(function(){
				    var $floatMenuBox=$("<div class='floatMenuWrap' id='J_floatMenu' style='display:none;position:absolute;'></div>");
						$floatMenuBox.css({zIndex:opts.zIndex+1,top:opts.pushMenuDownBy})
						var $floatArrow=$("<div class='floatArrow' style='position:absoulte'></div>");
						var $floaContextBox=$("<div class='floatContentBox'></div>");
						$floatMenuBox.css("top",opts.pushMenuDownBy);
						$floatMenuBox.append($floatArrow).append($floaContextBox);
						$navWrap.append($floatMenuBox);
						return {"self":$floatMenuBox,"Arrow":$floatArrow,"contentBox":$floaContextBox};
			 })()
          
		 
		    
			 var init=function(){
				    $navWrap.css("zIndex",opts.zIndex);
				    $child_lis=$this.children("li");
					 $child_lis.last().addClass("last")
					 $.each($child_lis,function(index,elem){
						     var $elem=$(elem);
						     var $navSub=$elem.find("."+opts.subNavClass).eq(0);
							 $navSub.children("li").last().addClass("last")
							 var navSubW=$navSub.width();
							 var posTop=$elem.position().top;
					         var posLeft=$elem.position().left;
							 var elemW=$elem.outerWidth();
							 bindData(navSubW,$elem,elemW,posTop,posLeft,$navSub);
                             if($elem.find("li").length > 0){
							    $elem.bind("mouseenter",function(event){	
								    Index=$child_lis.index(this);
							        var relateTarget=event.relatedTarget;
									
									//if($(relateTarget).closest(floatMenuObj.self).length > 0){return false}	
								    
								    floatMenuObj.self.width($(this).data("floatNavW"));
									$(this).addClass(opts.hoverClass);
									floatMenuObj.contentBox.empty().append($navSub.clone());										    								                                    floatMenuObj.Arrow.css({left:$(this).data("arrowLeft")});
									floatMenuObj.self.stop(true,true).css("left",$(this).data("posLeft")).fadeIn();	
								  }).bind("mouseleave",function(event){
									   var relateTarget=event.relatedTarget;
										 if($(relateTarget).closest(floatMenuObj.self).length > 0){
											  return	
										 }else{
												
												$(this).removeClass(opts.hoverClass);
												floatMenuObj.self.fadeOut()
												
											}
								 })	 
							 
							 }else{
								  $elem.bind("mouseover",function(event){	
								   floatMenuObj.self.stop(true,true).fadeOut();	   
								  
								}) 
							 }
							 
					 })
			 };
			 init();
			 
			 floatMenuObj.self.bind("mouseleave",function(event){
				 	var relateTarget=event.relatedTarget;
					if($(relateTarget).closest($child_lis.eq(Index)).length > 0){
						return;	
					}else{						  
						  $child_lis.eq(Index).removeClass(opts.hoverClass)
						  floatMenuObj.self.stop(true,true).fadeOut()
					
					 /*  var relateTarget=relateTarget.tagName=="LI" ? relateTarget : relateTarget.parentNode;
					   $child_lis.eq(Index).removeClass(opts.hoverClass);
					   Index=$child_lis.index(relateTarget);
					   $navSub=$child_lis.eq(Index).find("."+opts.subNavClass).eq(0);
					   floatMenuObj.contentBox.empty().append($navSub.clone());										    								                       floatMenuObj.Arrow.css({left:$child_lis.eq(Index).data("arrowLeft")});
					   floatMenuObj.self.stop(true,true).css({left:$child_lis.eq(Index).data("posLeft")}).fadeIn();	*/					
					}
			  })
			 
		}
})(jQuery)
//侧栏导航
;(function($){
	 $.fn.sideBarNav=function(options){
	        var defaults={
			     subHoverClass:"onHover",
				 subNavClass:"sidebar-subnav"
			}
			var opts=$.extend(defaults,options);
			var mouseover_timerArr=[],mouseout_timerArr=[];
			var $children_lis=this.children("li");	
			$children_lis.last().addClass("last");
			$.each($children_lis,function(index,elem){
				 $(elem).find("."+opts.subNavClass).children("li").last().addClass("last");
		         if($(elem).find(".subOncurr").length > 0){
			         $(elem).addClass("oncurr");
			     }
				 if($(elem).find("li").length <= 0){
					$(elem).addClass("sidebar-notSub"); 
			     }
			     if($(elem).find("li").length > 0 && !$(elem).hasClass("oncurr")){
					 $(elem).hover(function(){
						  var $this=$(this);
						  $this.addClass(opts.subHoverClass);
						  clearTimeout(mouseout_timerArr[index]);
						  mouseover_timerArr[index] = setTimeout(function() {$this.find('.'+opts.subNavClass).eq(0).slideDown(150);},155);
					   },function(){
						   var $this =$(this);
						   $this.removeClass(opts.subHoverClass);
						   clearTimeout(mouseover_timerArr[index]);
						   mouseout_timerArr[index] = setTimeout(function() {$this.find('.'+opts.subNavClass).eq(0).slideUp(150);},155)
					   })		
				 }
			}) 
	 }   
})(jQuery)
;(function(){
 $.fn.ewm=function(){
	this.hover(function(){
	    $(this).find(".bd").fadeIn()	
     },function(){
		 $(this).find(".bd").fadeOut() 
    })	
};	
})(jQuery)
//广告
;(function($){
		    $.fn.CSadvert=function(options){
				  var opts=$.extend({},$.fn.CSadvert.defaults,options);
				  return this.each(function(){
				        var $this=$(this),
						    isStop=false,
							Index=0,
							$parent2=$this.parent().parent().eq(0),
							$parent1=$this.parent(),
							adWidth,
							adHeight,
							Timer=null,
							$pagination,
							$pagination_lis,
							isIE6=!-[1,]&&!window.XMLHttpRequest,
							$slider_lis=$this.children("li"),
							len=$slider_lis.length,
							$pagination;
							
							
					   adWidth=$parent2.width()
							adHeight=$parent2.height()
					   var paginationInit=function(){
						       var strLis="";
							   $pagination=$('<ul></ul>');
							   $pagination.addClass("advert-numlist").css({"zIndex":opts.zIndex+1,"position":"absolute"})
							   for(var i=0;i<len;i++){
								  strLis+="<li><span>"+(i+1)+"</span></li>"; 
							   }
							   $pagination.append(strLis); 
							   $pagination.appendTo($parent1)
							   $pagination_lis=$pagination.find("li");
							   $pagination_lis.eq(Index).addClass("oncurr");
							   $pagination_lis.each(function(){
								  $(this).bind("mouseover",paginationEvt);
								  $(this).bind("mouseover",thisMouseover).bind("mouseout",thisMouseout);
							   }) 
					   };
					   function paginationEvt(){
						 var prevIndex=Index; 
						 isStop=true;
						 clearTimeout(Timer);
						 Index=$pagination_lis.index(this);
									beginStart()			 
					  }
					   function thisMouseover(event){
						   clearTimeout(Timer);
						   var relateElem = event.relatedTarget;
						   if($(relateElem).closest($parent1).length > 0){				   
	     						return;
							}else{	
								  isStop=true;
							}	     
					  };
				
					  function thisMouseout(event){
						     var relateElem = event.relatedTarget;
							 if($(relateElem).closest($parent1).length > 0){
							        return;
							 }else{
								    isStop=false;
									beginStart()	
						     }
					  };   
					function beginStart(){
								if(Index < 0){
											Index=len-1;
								}else if(Index==len){
										    Index=0
									}	
								scrolling();
					  };
					  function scrolling(){
								
								   $slider_lis.css("zIndex",opts.zIndex-1).stop(true).animate({opacity:0}).eq(Index).css("zIndex",opts.zIndex).stop(true).animate({opacity:1});
								  if(opts.pagination){ 
								  $pagination_lis.eq(Index).addClass("oncurr").siblings().removeClass("oncurr")}
										   if(isIE6){
									            $slider_lis.css("display","none").eq(Index).css("display","block")
									        }
								 if(!isStop){
										 Timer=setTimeout(function(){Index++;beginStart();},3000)
									}
					 };		
					   var Init=function(){ 
					       $parent1.css({position:"relative",width:adWidth,height:adHeight,"overflow":"hidden"});
						   $slider_lis.css({position:"absolute",opacity:0,zIndex:opts.zIndex-1,left:"0",top:"0"}).eq(Index).css("zIndex",opts.Index).animate({"opacity":1}); 
						   if(isIE6){
								$slider_lis.css("display","none").eq(Index).css("display","block")
							} 
							
							$this.bind("mouseover",thisMouseover);
						    $this.bind("mouseout",thisMouseout);
							if(opts.pagination){
								 paginationInit() ;
								 $pagination.bind("mouseover",thisMouseover);
						         $pagination.bind("mouseout",thisMouseout);
								}
							
							 
							beginStart()	
					   }
					   Init();
					   
				  })
		    } 
			$.fn.CSadvert.defaults={
				Index:10,
				pagination:true
		    } 
})(jQuery) 

//无缝滚动js
;(function($){
	      $.fn.CSContinueScroll=function(options){
			  var opts=$.extend({},$.fn.CSContinueScroll.defaults,options);
			  return this.each(function(){
				    var Timer=null,$listElem,$this=$(this),listElemW;
				    $listElem=$this.find("."+opts.listClass).eq(0);
					listElemW=$listElem.width();
					$listElem_clone=$listElem.html();
					$listElem.append($listElem_clone);
					function Marquee(){ 
							if($this.scrollLeft() >= listElemW){
									$this.scrollLeft(0); 
					
							}else{
								$this.scrollLeft($this.scrollLeft()+opts.step);
							}
					} 
					Timer=setInterval(function(){Marquee()},15);
				    $listElem.hover(function(){
						   clearInterval(Timer)
				      },function(){
					        Timer=setInterval(function(){Marquee()},15);
					 })
			  })
			 
		  } 
		  $.fn.CSContinueScroll.defaults={
				  listClass:"hsite-listType1",
				  step:1
		 }
})(jQuery)
$(function(){
  	 $(".h-pro-scrollWrap").CSContinueScroll({
	    listClass:"h-listType2",
		step:1	 
	 })
})
 	 	
