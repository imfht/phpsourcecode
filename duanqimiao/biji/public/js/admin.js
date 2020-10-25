/**
 * Created by dell on 2016/12/12.
 */
jQuery( document ).ready(function( $ ) {

    $(document).on('click','#handle', function () {
        var that = $(this);
        var d = dialog({
            title: '举报处理',
            content: "选择以下一种你要处理的方式 ",
            okValue: '解除笔记的分享状态',
            ok: function () {
                this.title('提交中…');
                $.ajax({
                    type: "GET",
                    url: "/admin/bijiManage/"+$(that).parent().children("input[name=bijiId]").val()+"/edit",
                    data: {
                        'type': 'remove'
                    },
                    success: function (data) {
                        var d = dialog({
                            title: '提示',
                            content: data.info,
                            width: 220
                        });
                        d.show();

                        window.location.reload();
                        return true;
                    }
                });
            },
            cancelValue: '直接拉黑该用户',
            cancel: function () {
                this.title('提交中…');
                $.ajax({
                    type: "GET",
                    url: "/admin/bijiManage/"+$(that).parent().children("input[name=bijiId]").val()+"/edit",
                    data: {
                        'type': 'delete'
                    },
                    success: function (data) {
                        var d = dialog({
                            title: '提示',
                            content: data.info,
                            width: 220
                        });
                        d.show();

                        window.location.reload();
                        return true;
                    }
                });
            }
        });
        d.show();
    });

    $(document).on('click','#ignore', function () {
        $.ajax({
            type: "GET",
            url: "/admin/bijiManage/"+$(this).parent().children("input[name=bijiId]").val()+"/edit",
            data: {
                'type': 'ignore'
            },
            success: function (data) {
                var d = dialog({
                    title: '提示',
                    content: data.info,
                    width: 220
                });
                d.show();

                window.location.reload();
                return true;
            }
        });
    });

    /*查看被举报笔记详情*/
    $(document).on('click','#detail', function () {
        $.ajax({
            type: "GET",
            url: "/admin/bijiManage/" + $(this).parent().children("input[name='bijiId']").val(),
            success: function (data) {
                var d = dialog({
                    title: '笔记详情',
                    content: data.content
                });
                d.show();
            }
        });
    });

   /* 修改用户信息*/
   $(document).on('click','#edit',function(){
       $.ajax({
           type: "GET",
           url: "/admin/userManage/"+$(this).parent().children("input[name='userId']").val()+"/edit",
           success: function (data) {
               $.each(data.user,function(index,values) {
                   var html = "<div>用户名：<input type='text' name='name' style='margin-right: 1em;' value='"+values.name+"'>" +
                       "电子邮箱：<input type='email' name='email' style='margin-right: 1em;' value='"+values.email+"'/></div>";
                   var d = dialog({
                       title: '提示',
                       content: html,
                       okValue: '修改',
                       ok: function () {
                           this.title('提交中…');
                           $.ajax({
                               type: "GET",
                               url: "/admin/userManage/"+values.id,
                               data: {
                                   'userName': $('input[name=name]').val(),
                                   'email':$('input[name=email]').val()
                               },
                               success: function (data) {
                                   var d = dialog({
                                       title: '提示',
                                       content: data.info,
                                       width: 220
                                   });
                                   d.show();

                                   window.location.reload();
                                   return true;
                               }
                           });
                       },
                       cancelValue: '取消',
                       cancel: function () {}
                   });
                   d.show();
               });


           }
       });
   });
    /*删除用户*/
    $(document).on('click','#delete',function() {
        var that = $(this);
        var d = dialog({
            title: '提示',
            content:'您确定要删除该用户吗？',
            okValue: '确定',
            ok: function () {
                this.title('提交中…');
                $.ajax({
                    type: "DELETE",
                    url: "/admin/userManage/" + $(that).parent().children("input[name=userId]").val(),
                    data: {
                        '_token': $('input[name=_token]').val()
                    },
                    success: function (data) {
                        var d = dialog({
                            title: '提示',
                            content: data.info,
                            width: 320
                        });
                        d.show();

                        window.location.reload();
                        return true;
                    }
                });
            },
            cancelValue: '取消',
            cancel: function () {}
        });
        d.show();
    });
});