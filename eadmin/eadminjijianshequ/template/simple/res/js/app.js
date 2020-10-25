//document.oncontextmenu=function(e){return false;}
//document.onpaste=function(e){return false;}
//document.oncopy=function(e){return false;}
//document.oncut=function(e){return false;}
//document.onselectstart=function(e){return false;}

var document_title = document.title;

$(document).ready(function ()
{
	 $('[data-toggle="tooltip"]').tooltip();
	//图片的渐变
	$(".shibox a").hover(function(){
		$(this).find("img").animate({"opacity":"0.6"},200)
	},function(){
		$(this).find("img").animate({"opacity":"1"},150)
	});
	$(".aw-article-text a").hover(function(){
		$(this).find("img").animate({"opacity":"0.6"},200)
	},function(){
		$(this).find("img").animate({"opacity":"1"},150)
	});
    // fix form bug...
    $("form[action='']").attr('action', window.location.href);



    // 输入框自动增高
    $('.autosize').autosize();

    //响应式导航条效果
    $('.aw-top-nav .navbar-toggle').click(function()
    {
        if ($(this).parents('.aw-top-nav').find('.navbar-collapse').hasClass('active'))
        {
            $(this).parents('.aw-top-nav').find('.navbar-collapse').removeClass('active');
        }
        else
        {
            $(this).parents('.aw-top-nav').find('.navbar-collapse').addClass('active');
        }
    });




  

    if (window.location.hash.indexOf('#!') != -1)
    {
        if ($('a[name=' + window.location.hash.replace('#!', '') + ']').length)
        {
            $.scrollTo($('a[name=' + window.location.hash.replace('#!', '') + ']').offset()['top'] - 20, 600, {queue:true});
        }
    }

    /*用户头像提示box*/
    AWS.show_card_box('.aw-user-name, .aw-user-img', 'user');

    AWS.show_card_box('.topic-tag, .aw-topic-name, .aw-topic-img', 'topic');

    //文章页添加评论, 话题添加 绑定事件
    AWS.Init.init_article_comment_box('.aw-article-content .aw-article-comment');



    //小卡片mouseover
    $(document).on('mouseover', '#aw-card-tips', function ()
    {
        clearTimeout(AWS.G.card_box_hide_timer);

        $(this).show();
    });

    //小卡片mouseout
    $(document).on('mouseout', '#aw-card-tips', function ()
    {
        $(this).hide();
    });

    //用户小卡片关注更新缓存
    $(document).on('click', '.aw-card-tips-user .follow', function ()
    {
        var uid = $(this).parents('.aw-card-tips').find('.name').attr('data-id');

        $.each(AWS.G.cashUserData, function (i, a)
        {
            if (a.match('data-id="' + uid + '"'))
            {
                if (AWS.G.cashUserData.length == 1)
                {
                    AWS.G.cashUserData = [];
                }
                else
                {
                    AWS.G.cashUserData[i] = '';
                }
            }
        });
    });

   

    /*icon tooltips提示*/
    $(document).on('mouseover', '.follow, .voter, .aw-icon-thank-tips, .invite-list-user', function ()
    {
        $(this).tooltip('show');
    });

    //搜索下拉
    AWS.Dropdown.bind_dropdown_list('#aw-search-query', 'search');

    //编辑器@人
    AWS.at_user_lists('#wmd-input, .aw-article-replay-box #comment_editor', 5);

    //ie浏览器下input,textarea兼容
    if (document.all)
    {
        AWS.check_placeholder($('input, textarea'));

        // 每隔1s轮询检测placeholder
        setInterval(function()
        {
            AWS.check_placeholder($('input[data-placeholder!="true"], textarea[data-placeholder!="true"]'));
        }, 1000);
    }
});

$(window).on('hashchange', function() {
    if (window.location.hash.indexOf('#!') != -1)
    {
        if ($('a[name=' + window.location.hash.replace('#!', '') + ']').length)
        {
            $.scrollTo($('a[name=' + window.location.hash.replace('#!', '') + ']').offset()['top'] - 20, 600, {queue:true});
        }
    }
});



$(function() {
//返回顶部
  $(window).resize(function() {
    setGotoTop();
  });
  setGotoTop()
  function setGotoTop() {
    $('#gotoTop').css({
      "right": ($(window).width() - 1000) / 2 - 60
    });
  }
  $('#gotoTop').click(function() {
    $("html, body").animate({
      scrollTop: 0
    }, {
      duration: 600,
      easing: 'easeInExpo'
    });
    var self = this;
    this.className += ' ' + "launch";
    setTimeout(function() {
      self.className = 'show';
    }, 800)
  });
  
  $(window).scroll(function() {
  if($(window).scrollTop()){
		$(".aw-top-menu-wrap").addClass("foxfixednav");
		 //$('#xiaosou').show();
		 // $('.aw-search-box').stop().hide();
		//$('#fudao').stop().hide();
	}else{
		$(".aw-top-menu-wrap").removeClass("foxfixednav");
		//$('#fudao').stop().show();
	} 
  
    if ($(window).scrollTop() < 50) {
    	
    		$('#gotoTop').slideUp(500);
    	
    	
    	
     
    } else {
    	
	
    		$('#gotoTop').slideDown(500);
   		
      
    }
  })
 });
