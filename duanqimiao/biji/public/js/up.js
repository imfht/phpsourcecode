/**
 * Created by Administrator on 2016/11/3 0003.
 */
jQuery( document ).ready(function( $ ) {
    //绑定页面滚动事件
    $(window).bind('scroll',function(){
        var len=$(this).scrollTop()
        if(len>=100){
            //显示回到顶部按钮
            $('#up').show();
        }else{
            //影藏回到顶部按钮
            $('#up').hide();
        }
    });
    //绑定回到顶部按钮的点击事件
    $('#up').click(function(){
        //动画效果，平滑滚动回到顶部
        $('body').animate({ scrollTop: 0 });
    });
});

