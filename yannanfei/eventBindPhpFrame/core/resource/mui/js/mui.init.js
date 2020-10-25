/**
 * Created by Happy on 2016/10/19 0019.
 */
//mui初始化 不引用mui.js事件初始化都在这里实现，主要是下拉菜单的功能
(function(){


    function get_query(e){
            var t = new RegExp("(^|&)" + e + "=([^&]*)(&|$)");
            var a = window.location.search.substr(1).match(t);
            if (a != null) return a[2];
            return ""
    }

    mui.init({
        swipeBack: true //启用右滑关闭功能
    });

    mui('.mui-scroll-wrapper').scroll();

    mui('#tab_list').on('tap','.mui-tab-item',function(){
           location.href=$(this).attr('href');
    });



    function init_menu_tab(){
        var act=get_query('act');//url中的act参数
        $('#mui_tab_'+act).addClass('mui-active').siblings().removeClass('mui-active');
    }
    init_menu_tab();
    /*
    mui('body').on('shown', '.mui-popover', function(e) {
        //console.log('shown', e.detail.id);//detail为当前popover元素
    });
    mui('body').on('hidden', '.mui-popover', function(e) {
        //console.log('hidden', e.detail.id);//detail为当前popover元素
    });*/

})();