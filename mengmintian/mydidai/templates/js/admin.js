$(document).ready(function(){
    var side_h3 = $('#sidebar div h3');
    var side_ul = $('#sidebar div ul');

    side_ul.hide();
    var index = null;
    side_h3.toggle(function(){
        index = side_h3.index(this);  
        side_h3.eq(index).css('background','url(../templates/images/sidebar_title_off.gif)');
        side_ul.eq(index).show();
    },function(){
        index = side_h3.index(this);  
        side_h3.eq(index).css('background','url(../templates/images/sidebar_title_on.gif)');
        side_ul.eq(index).hide();
    });
});
