var pageW = WST.pageWidth();
//弹框
function dataShow(n){
	jQuery('#cover').attr("onclick","javascript:dataHide('"+n+"');").show();
    jQuery('#'+n).animate({"left": 0}, 500, function(){
        echo.init();
    });
    
}
function dataHide(n){
	jQuery('#'+n).animate({'left': pageW+'px'  }, 500);
	jQuery('#cover').hide();
}

function goToSendPage(){
    var id = $('#id').val();
    location.href = WST.U('mobile/orderservices/sendPage',{id:id});
}