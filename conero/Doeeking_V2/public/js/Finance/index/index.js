$(function(){
    //
    $('.app_nav').click(function(){
        var dataid = Cro.dataid($(this));
        if(Cro.empty(dataid)) return;
        var win = '<iframe src="'+dataid+'"></iframe>'; 
        $('#app_win').html(win);
    });
});
var Cro = new Conero();