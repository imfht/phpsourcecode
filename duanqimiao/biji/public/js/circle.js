/**
 * Created by dell on 2016/8/24.
 */
jQuery( document ).ready(function( $ ) {

    $(document).on('click','.report', function () {
        var html = "<div>举报原因：<div style='margin-top: 0.5em;'><input class='form-control' name='cause'/></div></div>";
        var d = dialog({
            title: '提示',
            width: 320,
            content:html,
            okValue: '提交',
            ok: function () {
                this.title('提交中…');
                $.ajax({
                    type: "post",
                    url: "/admin/bijiManage" ,
                    data: {
                        '_token':$('input[name=_token]').val(),
                        'biji_id': $('input[name=biji_id]').val(),
                        'biji_title': $('input[name=biji_title]').val(),
                        'reporter_name': $('input[name=reporter_name]').val(),
                        'cause': $('input[name=cause]').val(),
                        'reported_id': $('input[name=reported_id]').val(),
                        'reported_name': $('input[name=reported_name]').val()
                    },
                    success: function (data) {
                        var d = dialog({
                            title: '提示',
                            content: data.info,
                            width: 220
                        });
                        d.show();
                        setTimeout(function () {
                            d.close().remove();
                        }, 3000);
                        return true;
                    }
                });
            },
            cancelValue: '取消',
            cancel: function () {}
        });
        d.show();
    });

    $(document).on('click','#collect-delete-btn',function(){
        $.ajax({
            type:"POST",
            url:"collect/"+$(this).parent().parent().children("input[name='bijis_id']").val(),
            data:{
                '_token':$('input[name=_token]').val()
            },
            success:function(data) {
                window.location.reload();
            }
        });
    });

    $(document).on("click","#share-delete-btn",function() {
        var that = $(this);
        var d = dialog({
            title: '提示',
            content: '您确定不再分享该笔记？',
            okValue: '确定',
            ok: function () {
                this.title('提交中…');
                $.ajax({
                    type: "GET",
                    url: "/share/" + $(that).parent().parent().children("input[name='share_id']").val(),
                    data: {
                        '_token': $('input[name=_token]').val()
                    },
                    success: function (data) {
                        var html = "";
                        $.each(data.share,function(index,values){
                            html += '<tr>' +
                            ' <td style="display: none;">'+
                            '<input type="hidden" name="share_id" value='+values.id+'/>'+
                            '</td>'+
                            '<td>'+
                            values.title+
                            ' </td>'+
                            ' <td>'+
                            '<a href="circle/'+values.id+'/edit" class="btn btn-sm btn-primary">'+
                            '<i class="fa fa-eye"></i>查 看'+
                            '</a>'+
                            '<a><button id="share-delete-btn" type="button" class="btn btn-danger btn-sm">删除</button></a>'+
                            '</td>'+
                            '</tr>';
                        });
                        $('.tbody').html(html);
                        return true;
                    }
                });
            },
            cancelValue: '取消',
            cancel: function () {}
        });
        d.show();

    });

    $('#search_btn').click(function(){
        $.ajax({
            type: "GET",
            url: "/search/",
            data: {
                'search':$('input[name=search]').val()
            },
            success:function(data){
                var html = "";
                var href = "";
                //第一个each:index为bijis values为Object Object
                $.each(data,function(index,values){
                    //第二个each:index为所有匹配到的笔记个数 value为Object
                    $.each(values,function(index,value){
                        href = value.id+" /edit";
                        html += "<tr><td class='title'>"+value.title+"</td><td><a class='btn btn-sm btn-primary' href='"+href+"'><i class='fa fa-eye'></i>查 看</a></td></tr>";

                    });
                });
                $('.tbody').html(html);
            }
        });
    });

    $(document).on("click",".comment-submit",function() {
        /*获取评论内容*/
        var content = $.trim($(this).parent().prev().children("textarea").val());
        $(this).parent().prev().children("textarea").val("");//获取内容后清空输入框
        if (content == "") {
            var d = dialog({
                title: '提示',
                content: '评论内容不能为空！',
                width: 220
            });
            d.show();
            setTimeout(function () {
                d.close().remove();
            }, 3000);
        } else {
            $.ajax({
                type: "GET",
                url: "/comment/create",
                dataType: "json",
                data: {
                    'parent_id': $(this).attr("parent_id"), /*上级评论id*/
                    'biji_id': $('input[name=biji_id]').val(),
                    'content': content
                },
                success: function (data) {
                    $(".comment-reply").next().remove();//删除已存在的所有回复div
                    //显示新增评论
                    var newli = "";
                    if(data.comments.parent_id == "0"){
                        //发表的是一级评论时，添加到一级ul列表中
                        newli = "<li comment_id='"+data.comments.id+"'>" +
                        "<div>" +
                        "<div class='cm'>" +
                        "<div class='cm-header'>" +
                        "<span>"+data.comments.user_name+"</span>" +
                        "<span>"+data.comments.created_at+"</span>" +
                        "</div>" +
                        "<div class='cm-content'>" +
                        "<p>"+data.comments.comments+"</p>" +
                        "</div>" +
                        "<div class='cm-footer'>" +
                        "<a class='comment-reply' comment_id='"+data.comments.id+"'  href='javascript:void(0);'>回复</a>" +
                        "</div>" +
                        "</div>" +
                        "</div>" +
                        "<ul class='children'></ul>" +
                        "</li>";
                        $(".comment-ul").prepend(newli);
                    }else{
                        //否则添加到对应的孩子ul列表中
                        newli = "<li comment_id='"+data.comments.id+"'>" +
                        "<div >" +

                        "<div class='children-cm'>" +
                        "<div  class='cm-header'>" +
                        "<span>"+data.comments.user_name+"</span>" +
                        "<span>"+data.comments.created_at+"</span>" +
                        "</div>" +
                        "<div class='cm-content'>" +
                        "<p>"+data.comments.comments+"</p>" +
                        "</div>" +
                        "<div class='cm-footer'>" +
                        "</div>" +
                        "</div>" +
                        "</div>" +
                        "<ul class='children'></ul>" +
                        "</li>";
                        $("li[comment_id='"+data.comments.parent_id+"']").children("ul").prepend(newli);
                    }
                    window.location.reload()
                }
            });
        }
    });
    //点击"回复"按钮显示或隐藏回复输入框
    $("body").delegate(".comment-reply","click",function(){
        //添加回复div
        $(".comment-reply").next().remove();//删除已存在的所有回复div
        //添加当前回复div
        var parent_id = $(this).attr("comment_id");//要回复的评论id
        var divhtml = "";
        divhtml = "<div class='div-reply-txt' replyid='2'>" +
        "<div>" +
        "<textarea class='div-reply-txt form-control' replyid='2'></textarea>" +
        "</div>" +
        "<div style='margin-top:5px;text-align:right;'>" +
        "<a class='comment-submit' parent_id='"+parent_id+"'>" +
        "<button class='btn btn-primary'>提交回复</button>" +
        "</a>"+
        "</div>" +
        "</div>";
        $(this).after(divhtml);
    });

    $('.collect').click(function(){
        $.ajax({
            type:'GET',
            url:'/collect/'+$('input[name=user_id]').val(),
            data:{
                'biji_id': $('input[name=biji_id]').val()
            },
            success: function (data) {
                $('.collect').html("已收藏");
                if(data.collect){
                    var d = dialog({
                        title: '提示',
                        content: '收藏笔记成功，请到《我的收藏》查看！',
                        width: 220
                    });
                    d.show();
                    setTimeout(function () {
                        d.close().remove();
                    }, 3000);
                }else{
                    var d = dialog({
                        title: '提示',
                        content: '不能重复收藏！',
                        width: 220
                    });
                    d.show();
                    setTimeout(function () {
                        d.close().remove();
                    }, 3000);
                }
            }
        }) ;
    });

    $('.good').click(function(){
        $.ajax({
            type:'GET',
            url:'/good',
            data:{
                'biji_id':$('input[name=biji_id]').val()
            },
            success:function(data){
                $('.good').html("已点赞");
                if(data.good){
                }else{
                    var d = dialog({
                        title: '提示',
                        content: '不能重复点赞！',
                        width: 220
                    });
                    d.show();
                    setTimeout(function () {
                        d.close().remove();
                    }, 3000);
                }
            }

        }) ;
    });
});
