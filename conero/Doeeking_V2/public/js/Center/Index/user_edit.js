$(function(){
    $('.set_city').click(function(){
        var text = $(this).text();
        var cityEl = $('#user_edit').find('input[name="city"]');
        cityEl.val(cityEl.val()+' '+text);
    });
});