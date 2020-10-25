/*COPYRIGHT(C)UCWEB 2012.*/
/*独立js样式表（九游Iphone-webapp）.*/
/*2012.04.11*/
function setCookie(cookieName, cookieValue, days, path, domain, secure) {
    var expires = new Date();
    expires.setDate(expires.getDate() + days);
    document.cookie = escape(cookieName) + '=' + escape(cookieValue)
        + (expires ? '; expires=' + expires.toGMTString() : '')
        + (path ? '; path=' + path : '/')
        + (domain ? '; domain=' + domain : '')
        + (secure ? '; secure' : '');
}
/*格式化URL传参值*/
function urlFormat(url,key,value){
    if(url==undefined || url.trim()=="")
        return "javascript:void(0);";
    if(key==undefined || value==undefined)
        return url;
    url=url.replace(/http:\/\/?/i,'');
    url="http://"+url;
    var npr=key.concat("=",value);
    if(url.indexOf('?')<0)
        return url.concat("?",npr);
    if(url.indexOf(key+"=")<0)
        return url.concat("&",npr);
    var regx = eval("/" + key + "=[^&]+/ig");
    return url.replace(regx,npr);
}
$(function(){
        //越狱一键安装不跳转
        $(".no_jump").click(function(e){
	    e.stopPropagation();
        });
	//顶部广告条出现隐藏效果
	$("#js_tb_slideUp").delay(3000).slideUp(3000);
	$("#js_tb_slideDown").delay(3000).slideDown(3000);
	//关闭效果
	$(".js_tb_close").click(function(){
		$(".ui-topBanner-info").fadeOut(600);
	});
	//首页滑动焦点自动生成文字说明
	$(".js-hisAutoInfo").each(function(){
		$(this).after(function(){
			return '<p class="ms-pr"><span class="ms-pa info">' + $(this).attr('alt') + '</span></p>';
		});
	});
	//搜索块切换
	$(".js_searchTog").toggle(function(){
		$(".js_searchTogarea").show();
	},function(){
		$(".js_searchTogarea").hide();
	});
	//网游专区板块切换
	var $ngtag_item = $(".js_ngtab_tag").children();
	$ngtag_item.click(function(){
		var ngtabIndex = $(this).parent().children().index(this);
		var $ngtabArea = $(this).parent().parent().next();
		$ngtabArea.children().eq(ngtabIndex).removeClass("ms-none").addClass("am_fadeout").siblings().addClass("ms-none");
		return false;
	});
	//展示更多精简
	$(".js_disinfoTag_a").click(function(){
		$(this).hide();
		$(".js_disInfo_i").hide();
		$(".js_disInfo_a").show();
		$(".js_disinfoTag_i").show();
		return false;
	});
	$(".js_disinfoTag_i").click(function(){
		$(this).hide();
		$(".js_disInfo_a").hide();
		$(".js_disInfo_i").show();
		$(".js_disinfoTag_a").show();
		return false;
	});
	//幻灯片播放
       
	$(".js_pptClose").click(function(){
		$(".js_autoIMG").hide();
	});
	/*var pptCon = $(".js_autoPPT").html();
	$(".js_autoimgList").html(pptCon);
	$(".js_autoPPT li").each(function(){
		var PPTindex = $(".js_autoPPT li").index(this);
		var oldPic=$(".js_autoimgList li").eq(PPTindex).find("img");
		var newPic=new Image();
		newPic.src=oldPic.attr("src");
		var oliW=newPic.width;
		var oliH=newPic.height;
		if (oliW/oliH>1){
			$(".js_autoimgList li").eq(PPTindex).find("img").addClass("js_hImg")
		}else{
			$(".js_autoimgList li").eq(PPTindex).find("img").addClass("js_vImg")
		};
		$(this).click(function(){
			$(".js_autoIMG").show();
			$(".js_autoimgList li").eq(PPTindex).show().siblings().hide();
		});
	});	
	$(".js_pptClose").click(function(){
		$(".js_autoIMG").hide();
	});*/

	
	/*控制字数
	$(".js_length").each(function(){
		$(this).text($(this).text().substr(0, 30)+'......');
	});*/
	//幻灯片自动生成播放
	/*var $aImg = $(".js_autoPPT").children(".img");
	$aImg.each(function(){
		var pImgsrc = $(this).attr("src");
		$(this).click(function(){
			$(".js_autoIMG").show();
			$(".ui-pptImg-wrap").append("<img src='"+pImgsrc+"' class='img'>");
		});
		/*var oldPic=$(this);
		var newPic=new Image();
		newPic.src=oldPic.attr("src");
		var oliW=newPic.width;
		var oliH=newPic.height;
		if (oliW/oliH>1){
			$(this).removeClass(".js_vImg").addClass("js_hImg")
		}else{
			$(this).removeClass(".js_hImg").addClass("js_vImg")
		});
	});*/
	/*$(".js_autoPPT li").each(function(){
		var pptIndex = $(".js_autoPPT li").index(this);
		$(this).click(function(){
			$(".js_autoIMG").show();
			$(".js_autoimgList li").eq(pptIndex).show().siblings().hide();
			$(".js_autoWH .img").each(function(){
				var p_wid = $(this).width();
				var p_hei = $(this).height();
				if (p_wid/p_hei>1){
					$(this).removeClass(".js_vImg").addClass("js_hImg")
				}else{
					$(this).removeClass(".js_hImg").addClass("js_vImg")
				};
			});
		});
	});
	$(".js_pptClose").click(function(){
		$(".js_autoIMG").hide();
	});*/
	/*$(".js_autoWidth").each(function(){
		var $aImg = $(".js_autoWidth").children(".img");
		var p_wid = $aImg.width();
		var p_hei = $aImg.height();
		if (p_wid/p_hei>1){
			$aImg.addClass("js_hImg")
		}else{
			$aImg.addClass("js_vImg")
		};
	});
	$(".js_autoPPT ").each(function(){
		var pImgsrc = $(this).children().attr("src");
		$(this).click(function(){
			$(".js_pptImg").show();
			$(".ui-pptImg-wrap").append("<img src='"+pImgsrc+"' class='img'>");
		});
	});
	$(".js_pptClose").each(function(){
		$(this).click(function(){
			$(".js_pptImg").detach();;
		});
	});*/
	//自动长度
	$(".js_auto_w").each(function(){
		var spLen = $(this).find("li").length;
		var spWit = spLen*95+20;
		$(this).width(spWit);
	});
	/*评分评论切换*/
		$(".g_com").click(function(){
			var parent = $(this).parent().parent();
			$(parent).hide();
			$(parent).siblings().show();
			return false;
		});
		/*星级评分*/
		$(".starArea li").each(function(){
			$(this).mouseover(function(){
				var index = $(".starArea li").index(this);
				var index2 = index+1;
				$(".starArea div").width(40*index2);
				return false;
			}).click(function(){
				var index = $(".starArea li").index(this);
				var index2 = index+1;
				$(".starArea div").width(40*index2);
				switch (index)
				{case 0:
					 $(".starInfo").html("您打了1分啊！")
					 break
				 case 1:
					 $(".starInfo").html("您打了2分哦！")
					 break
				 case 2:
					 $(".starInfo").html("您打了3分呢！")
					 break
				 case 3:
					 $(".starInfo").html("您打了4分啦！")
					 break
				 case 4:
					 $(".starInfo").html("您打了5分哇！")
					 break	
				};
				$("#score").val(index2);
				$(".starArea li").unbind("mouseout",FUN);
				return false;
			});
		});
		$(".starArea li").bind("mouseout",FUN = function(){
			var index = $(".starArea li").index(this);
			var index2 = index+1;
			$(".starArea div").width(0);
			return false;
		}); 
    var cookie_time=365;
    //越狱标签
    var gourl_ary=[["yy","iph_yyxs_yes"],["bb","iph_yyxs_no"]];
    $(".js_wk").toggle(function(){
        $(this).next(".wk_box").show();
    },function(){
        $(this).next(".wk_box").hide();
    });
    $(".js_wkIco").each(function(){
        $(this).click(function(){
            $(this).addClass("on").siblings().removeClass("on");
            var cidx=$(".js_wkIco").index(this);
            setCookie("wk",gourl_ary[cidx][0], cookie_time,"/",".9game.cn");
            setCookie("i_last_choose",cidx, cookie_time,"/",".9game.cn");
            window.location=urlFormat(urlFormat(window.location.href,"wk",gourl_ary[cidx][0]),"text",gourl_ary[cidx][1]);
        });
    });
    //越狱标签
});	
/*通用js代码*/
$(function(){
	 $(".ui-nav ul li a").click(function(){
   var href=$(this).attr("href");
   window.location.href=href;
   return false;
  })
	//关闭效果
	$(".js_close").click(function(){
		$(this).fadeOut(600);
	});
	//图片圆角效果
	/*
	$(".js-roundImg").each(function(){
		$(this).wrap(function(){
			return '<span class="roundImg" style="background:url(' + $(this).attr('src') + ') no-repeat center center; width: ' + $(this).width() + 'px; height: ' + $(this).height() + 'px; background-size:'+ $(this).width() +'px;"></span>';
		});
		$(this).css("opacity","0");
	});
	*/
	//页内板块切换
	var $tabtogItem = $(".js_tab_tog").children();
	$tabtogItem.click(function(){
		$(this).addClass("on").siblings().removeClass("on");
		return false;
	});
	//导航板块切换
	var $navtagItem = $(".js_nav_tag").children();
	$navtagItem.click(function(){
		$(this).children().addClass("on");
		$(this).siblings().children().removeClass("on");
	});
         
         $(".circle").each(function(){
             this.src="http://image.game.uc.cn/2012/8/4/9000021.gif";
        });
    //分类的显示与隐藏
    $(".at_aro").click(function(){
        if($(".ico_group").get(0).style.display=="none"){
            $(".ico_group").show();
            $(this).addClass("at_aro_d");
        }else{
            $(".ico_group").hide();
            $(this).removeClass("at_aro_d");
        }
    });
});
	//评论下拉窗口
	function com_toggle(){
	var comArea = document.getElementById("g_comAera");
	if(comArea.style.display == "block"){
		comArea.style.display = "none";
	}
	else{
		comArea.style.display = "block";
	}
}	
//点击游戏列表出现操作按钮
function listinfo_click(that,t_href){
    window.location=t_href;
    return;
    if($(that).next().attr("class").indexOf("drop_down")<0 || $(that).next().find("a").length == 0){
        window.location=t_href;
        return;
    }
    if($(that).next().height()==0){
        $(that).parent().find(".drop_down").height(0);
        $(that).parent().find('.ui-arrow').hide();
        $(that).next().height(45);
        $(that).find('.ui-arrow').show();
    }else{
        $(that).next().height(0);
        $(that).find('.ui-arrow').hide();
    }
}