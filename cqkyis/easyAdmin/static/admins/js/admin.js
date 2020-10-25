/**
 * Created by Administrator on 2016/3/19.
 */
/**
 * 添加一个模型
 * @param content
 */
function modal(content) {
    var modalHtml = '<div class="modal fade "></div>';
    if ($(".modal").length == 0) {
        $("body").append(modalHtml);
    }
    $(".modal").html(content);
    $('.modal').modal('show',{backdrop: 'static'});
}



$(document).on("click", "a[rel='add']", function (e) {

    e.preventDefault();
    var obj = $(this);

    $.get(obj.attr('href'), function (data) {
        var msg=jQuery.parseJSON(data);
              // alert(msg);
        modal( msg);
        // if (data) {
        //
        //     if (!data.match("^\{(.+:.+,*){1,}\}$"))
        //     {
        //         modal( data);
        //     }
        //     else
        //     {
        //         //通过这种方法可将字符串转换为对象
        //         var msg=jQuery.parseJSON(data);
        //         parent.error(msg.info);
        //     }



        // }else{
        //     removeModal();
        // }
    }, 'html');


});
$(document).on("click", "a[rel='newadd']", function (e) {
    var obj = $(this);
    var htmlurl = obj.attr('action');
    var title = obj.attr('title');
    
    layer.open({
        type: 2,
        area: ['700px', '450px'],
        // fixed: false, //不固定
        // maxmin: true,
        content: htmlurl,
        title:title,
    });
});


$(document).on("click", "a[rel='ajx']", function (e) {
    e.preventDefault();
    var obj = $(this);
    $.get(obj.attr('href'), function (data) {
        if (data) {
            ajx( data);
        }else{
            hidde();}
    }, 'html');


});
function ajx(content){
    var opts = {
        "closeButton": true,
        "debug": false,
        "positionClass": "toast-top-right",
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"

    };

    toastr.error(''+content+'', "亲，再看看", opts);
    hidde();
}
function removeModal() {
    $(".modal").remove();
}


function error(msg){


    layer.msg(''+msg+'');
   

}

function success(msg,back){
    // swal({
    //     title: ret.msg,
    //     text: ret.msg,
    //     type:'success',
    //     button: "确定"
    // },function () {
    //     location.href =ret.data;
    // });
    layer.msg(''+msg+'');
    eval(back);
}


function successpc(msg,back){

if(back){
    alert(msg);
    setTimeout(location.href = back, 5000);


}else {
    history.back(-1);
}

}


function jumpUrl(url) {

    if (url) {

        location.href = url;
    } else {
        history.back(-1);
    }
}


$(document).on("click", "a[rel='del']", function (e) {
    e.preventDefault();
    removeModal();
    // var modalHtml = '<div class="modal fade">' +
    //     '<div class="modal-dialog" >' +
    //     '<div style="height: 40px; background-color: #F2F2F2;">' +
    //     '<div class="modal-header">' +
    //     '<button type="button" style="color: #333" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
    //     '<h4 class="modal-title" style="color: #333">系统提示</h4>' +
    //     '</div></div><div class="modal-content">' +
    //     '<form role="form" class="form-horizontal" method="post" action="'+$(this).attr('href')+'" target="frame">' +
    // '<div class="modal-body">' +
    //     '<i class="fa fa-question" style="color: #333; font-size: 30px;"></i><b style="color: #ff4891; font-size: 14px;">&nbsp;&nbsp;确定删除此条信息！</b></div><div class="modal-footer">' +
    //     '<button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>' +
    //     '<button type="submit" class="btn btn-gray">确定</button></div></form></div></div></div>';
    // if ($(".modal").length == 0) {
    //     $("body").append(modalHtml);
    //     $('.modal').modal('show',{backdrop: 'static'});
    // }

    var urls=$(this).attr('href');
    swal({
        title: "你确定删除吗?",
        text: "删除过后不能恢复!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "是的，删除",
        cancelButtonText: "取消",
        closeOnConfirm: false
    }, function(){

        $.ajax({
            type : "GET",
            url : urls,
            success : function(ret) {

                if(ret.code==1){
                    swal({
                        title: ret.msg,
                        text: ret.msg,
                        type:'success',
                        button: "确定"
                    },function () {
                        location.href =ret.data;
                    });

                }else{
                    swal(ret.msg, ret.msg,"error")
                }

            }
        });

    });


});



$(document).on("click", "a[rel='delseller']", function (e) {
    e.preventDefault();
    removeModal();
    var modalHtml = '<div class="modal fade">' +
        '<div class="modal-dialog" style="border: #ff6264 solid 2px; ">' +
        '<div style="height: 50px; background-color: #ff6264">' +
        '<div class="modal-header">' +
        '<button type="button" style="color: #ffffff" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
        '<h4 class="modal-title" style="color: #ffffff">系统提示</h4>' +
        '</div></div><div class="modal-content">' +
        '<form role="form" class="form-horizontal" method="post" action="'+$(this).attr('href')+'" target="x-frame">' +
        '<div class="modal-body">' +
        '<i class="fa fa-question" style="color: #ff2a2d; font-size: 30px;"></i><b style="color: #ff2a2d; font-size: 14px;">&nbsp;&nbsp;确定删除商家会员，删除商家会员将删除商家下的所有信息，包括视频,商品等！</b></div><div class="modal-footer">' +
        '<button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>' +
        '<button type="submit" class="btn btn-pink">确定</button></div></form></div></div></div>';
    if ($(".modal").length == 0) {
        $("body").append(modalHtml);
        $('.modal').modal('show',{backdrop: 'static'});
    }



});


$(document).on("click", "a[rel='delvip']", function (e) {
    e.preventDefault();
    removeModal();
    var modalHtml = '<div class="modal fade">' +
        '<div class="modal-dialog" style="border: #ff6264 solid 2px; ">' +
        '<div style="height: 50px; background-color: #ff6264">' +
        '<div class="modal-header">' +
        '<button type="button" style="color: #ffffff" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
        '<h4 class="modal-title" style="color: #ffffff">系统提示</h4>' +
        '</div></div><div class="modal-content">' +
        '<form role="form" class="form-horizontal" method="post" action="'+$(this).attr('href')+'" target="x-frame">' +
        '<div class="modal-body">' +
        '<i class="fa fa-question" style="color: #ff2a2d; font-size: 30px;"></i><b style="color: #ff2a2d; font-size: 14px;">&nbsp;&nbsp;确定删除会员，删除会员将删除会员的订单，会员钱包，会员积分，会员积分记录等所有信息！！</b></div><div class="modal-footer">' +
        '<button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>' +
        '<button type="submit" class="btn btn-pink">确定</button></div></form></div></div></div>';
    if ($(".modal").length == 0) {
        $("body").append(modalHtml);
        $('.modal').modal('show',{backdrop: 'static'});
    }



});


function closed(){
    $(".modal").remove();
    $(".modal-backdrop").remove();
}


function selectCallBack(id, name, v1, v2) {

    $("#" + id).val(v1);
    $("#" + name).val(v2);
    $(".close").trigger("click");
    $(".modal").remove();
    $(".modal-backdrop").remove();
    $("body").removeClass('modal-open');
    selectcate();
}


/**
 *全选
 */
//全选




$(document).on("click", ".checkAll", function (e) {
   var child = $(this).attr('rel');
     $(".child_" + child).prop('checked', $(this).prop("checked"));
});


