$(document).ready(function(){

    //文章分类导航
    var left_top_li = $('#left_top_ul>li');
    $('.ul_content>li').hide();
    $('.ul_content>li').eq(0).show();
    left_top_li.mousemove(function(){    
        $(this).addClass('select').siblings().removeClass();
        var num = $(this).index();   
        $('.ul_content>li').eq(num).show().siblings().hide();
    });

    //网络资源导航
    var left_buttom_li = $('#left_buttom_ul>li');
    $('#buttom_content>li').hide();
    $('#buttom_content>li').eq(0).show();
    left_buttom_li.mousemove(function(){
        $(this).addClass('select').siblings().removeClass();
        var num = $(this).index();
        $('#buttom_content>li').eq(num).show().siblings().hide();
    });

    //登录窗口 
    var login = $('#login');
    var pop_login = $('#pop_login');
    var pop_close = $('#pop_login h3 span');
    var mask = $('#mask');

    var window_width = $(window).width();
    var window_height = $(window).height();
    var pop_width = pop_login.outerWidth(true);
    var pop_height = pop_login.outerHeight(true);

    var scroll_top = $(window).scrollTop();
    var scroll_left = $(window).scrollLeft();


    login.click(function(){
		pop_login.css({'left':window_width/2 - pop_width/2 + scroll_left + 'px','top':window_height/2 - pop_height/2+ scroll_top +  'px'}).fadeIn();
        $(window).bind("resize",function(){
            window_width = $(window).width();
            window_height = $(window).height();
            var scroll_top = $(window).scrollTop();
            var scroll_left = $(window).scrollLeft();
            
			pop_login.css({'left':window_width/2 - pop_width/2 + scroll_left + 'px','top':window_height/2 - pop_height/2+ scroll_top +  'px'}).fadeIn();
		
         });

        $(window).bind("scroll",function(){
            window_width = $(window).width();
            window_height = $(window).height();
            var scroll_top = $(window).scrollTop();
            var scroll_left = $(window).scrollLeft();
            pop_login.css({'left':window_width/2 - pop_width/2 + scroll_left + 'px','top':window_height/2 - pop_height/2+ scroll_top +  'px'}).fadeIn();
        });

    });

    login.click(function(){
        mask.fadeIn().width($(document).width()).height($(document).height());
    });

    pop_close.click(function(){
        pop_login.fadeOut();
        mask.fadeOut();
        $(window).unbind();
    });

    //热点标签随机颜色
    var tag_hot = $('#tag_hot ul li a');
    var tag_str = '15fa20e82cd65d104607c06ab6290700e34a';
    
    for(var i=0;i<tag_hot.size();i++){
        tag_rand = Math.ceil(Math.random()*10);
        tag_hot.eq(i).css({color:'#'+tag_str.substr(tag_rand+3,3),fontSize:14+'px'});
    }

    //文章正文ajax获取文章的评论
    var ajax_comment_list = $('#ajax_comment_list');
    var article_comment_list = $('#article_comment_list');
    var aid = $('#article_id').val();
    var click_num = 1;
    ajax_comment_list.click(function(event) {
        $.get('./comment.php?action=ajax_lists&s=' + click_num + '&aid=' + aid,function(data){
            alert(data);
        });
        click_num += 1;
    });

});
