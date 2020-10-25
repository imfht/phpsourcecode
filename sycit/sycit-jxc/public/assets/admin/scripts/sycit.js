/**
 * 三叶草IT QQ-316262448
 * www.sycit.cn, hyzwd@outlook.com
 * Created by Peter on 2017/8/28.
 */
$(function () {
    //单页面的返回顶部
    //隐藏或显示“回到顶部”链接
    $(window).on('scroll',function(){
        ($(this).scrollTop() > 300) ? $('#syc-view-top').addClass('syc-is-visible'): $('#syc-view-top').removeClass('syc-is-visible');
        if($(this).scrollTop() > 1200) {
            $('#syc-view-top').addClass('syc-is-visible');
        }
    });
    //平滑滚动到顶部
    $('#syc-view-top').on('click', function() {
        <!--此处加入finish防止多次点击回顶部或者回底部多次触发动画的bug,也可以使用stop()来替换finish()-->
        $('body,html').finish().animate({scrollTop:0}, 700);
        return false;
    });
});

//列表中 搜索后高亮显示位置
function listDropdownSearch(obj) {
    // 判断 obj 是否为 函数
    var obj = obj instanceof Object ? obj : null;
    if (obj == null) {
        return false;
    }
    if (obj.i !== '' && obj.s !== '' && obj.k !== '') {
        var list = $("#"+obj.i);
        var text = list.find('#'+obj.s+'_'+obj.k+' span').text();
        var span = '<span class="icon-yes"></span>';
        list.find('#'+obj.s+'_'+obj.k).prepend(span);
        list.find(".title").empty().text("("+text+")");
        //console.log(obj)
    }
};


//
function sumAjax(href, options, type, async, callback) {
    var type = type ? type : 'POST';
    var async = async ? async : true;
    $.ajax({
        url: href,
        type: type, //GET
        async: async,    //或false,是否异步
        data: options,
        timeout:5000,    //超时时间
        dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text

        success:function(data){
            var $item=new Array();
            for (var i in data) {
                var str = data[i];
                $item.push(str);
            }
            //return data;
            console.log(data)
        }
    });
}