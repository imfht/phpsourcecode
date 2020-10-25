$(function(){
    center.menuAbjust();
});
var Cro = new Conero();
var center = Cro.extends(function(th){
    // 左菜单自适应
    this.menuAbjust = function(){
        var search = location.search;
        var arr = search.split('&');
        var sidebar = $('.nav-sidebar');
        sidebar.find('li.active').removeClass('active');
        var href = arr[0]? arr[0]:'?user.html';
        sidebar.find('a[href="'+href+'"]').parent('li').addClass('active');
        //th.log('a[href="'+arr[0]+'"]');
    }
});
