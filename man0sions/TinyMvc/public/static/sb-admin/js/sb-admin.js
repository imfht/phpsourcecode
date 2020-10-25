$(function() {

    $('#side-menu').metisMenu();
    $("input,select,.textarea").addClass("form-control");

});

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
$(function() {
    $(window).bind("load resize", function() {
        console.log($(this).width())
        if ($(this).width() < 768) {
            $('div.sidebar-collapse').addClass('collapse')
        } else {
            $('div.sidebar-collapse').removeClass('collapse')
        }
    })


})

/*
* jquery ajax form validation
*
* */


$(function(){
    //
    //$("*[type=submit]").click(function(){
    //    var submit = $(this);
    //
    //    var form = $(this).parents("form");
    //    var formid = form.attr("id");
    //
    //    var child = form.find(".form-control");
    //    var data = [];
    //    child.each(function(i){
    //
    //        var obj = child.eq(i);
    //
    //        var name = obj.attr("name");
    //        var value = obj.val();
    //        if(name)
    //            data.push( name+'='+value );
    //    });
    //    data = data.join("&");
    //    $.ajax({
    //        url:location.href,
    //        method:form.attr('method'),
    //        data:data+'&ajax=1',
    //        success:function(res){
    //            //如果没有错误，直接跳转到gourl
    //            if(res.err==0 && res.gourl)
    //            {
    //
    //               location.href = res.gourl;
    //               return;
    //            }
    //
    //
    //            form.find(".form-group").removeClass("has-error");
    //
    //            for(var i=0;i<res.length;i++)
    //            {
    //               var obj = form.find("*[name="+res[i].field+"]");
    //               obj.parent(".form-group").addClass("has-error");
    //               obj.next(".help-block").remove();
    //               obj.after('<p class="help-block">'+res[i].message+'</p>');
    //            }
    //
    //
    //        }
    //
    //    });
    //
    //    return false;
    //});
})