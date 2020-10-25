/**
 * Created by dell on 2016/10/1.
 */
jQuery( document ).ready(function( $ ) {

    $(document).on("click",".recover-btn",function() {
        $.ajax({
            type: "GET",
            url: "/wastebasket/recover/"+$(this).parent().children("input[name='basket-id']").val(),
            success:function(data){
                var html = "";
                $.each(data.wastebasket,function(index,values){
                    html += '<tr>'+
                        '<td>'+index+'</td>'+
                        '<td>'+values.title+'</td>'+
                        '<td><input type="hidden" name="basket-id" value="'+values.id+'"/><a class="btn btn-sm btn-primary recover-btn"><i class="fa fa-eye"></i>还原</a><a><button id="collect-delete-btn" type="button" class="btn btn-danger btn-sm">彻底删除</button></a></td>'+
                        '</tr>'
                });
                $('.tbody').html(html)
            }
        }) ;
    });

    $(document).on('click','.btn-clear',function(){
        $.ajax({
            type:"GET",
            url:"/wastebasket/clear/"+$(this).parent().parent().children("input[name='basket-id']").val(),
            success:function(data){
                var html = "";
                $.each(data.wastebasket,function(index,values){
                    html += '<tr>'+
                    '<td>'+index+'</td>'+
                    '<td>'+values.title+'</td>'+
                    '<td><input type="hidden" name="basket-id" value="'+values.id+'"/><a class="btn btn-sm btn-primary recover-btn"><i class="fa fa-eye"></i>还原</a><a><button id="collect-delete-btn" type="button" class="btn btn-danger btn-sm">彻底删除</button></a></td>'+
                    '</tr>'
                });
                $('.tbody').html(html)
            }
        });
    });
    $.ajax({
        type: "GET",
        url: "/ip/",
        success:function(data){
            if(data.info){
                var d = dialog({
                    title: '提示',
                    content: data.info
                });
                d.show();
                setTimeout(function () {
                    d.close().remove();
                }, 3000);
            }

        }
    }) ;

    if($(window).width() < 768){

        $('.biji_list_form').submit(function(e){
            e.preventDefault();//阻止表单提交
            $.ajax({
                type: "GET",
                url: "/mobile/biji/",
                data:{'biji_id':$('input[name=biji_id]').val()},
                success:function(data){
                   var header =
                        "<form style='display:inline;margin:0 0.5rem;' method = 'GET' action='/biji/"+data.id+"'>"+
                            "<input type='hidden' name='biji_id' value='"+data.id+"'>"+
                            "<button type='submit' id='info' class='btn btn-info btn-sm'>"+
                            "<i class='fa fa-times-circle'></i>"+
                            "笔记信息"+
                            "</button>"+
                        "</form>"+
                        "<button type='button' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#modal-delete'>"+
                        "<i class='fa fa-times-circle'></i>"+
                        "删除笔记"+
                        "</button>"+
                        "<div class='modal fade' id='modal-delete' tabindex='-1'>"+
                            "<div class='modal-dialog'>"+
                                "<div class='modal-content'>"+
                                    "<div class='modal-header'>"+
                                        "<button type='button' class='close' data-dismiss='modal'>"+
                                        "X"+
                                         "</button>"+
                                        "<h4 class='modal-title'>提示</h4>"+
                                    "</div>"+
                                    "<div class='modal-body'>"+
                                        "<p class='lead'>"+
                                            "<i class='fa fa-question-circle fa-lg'></i>"+
                                            "您确定要删除笔记"+data.title+"?"+
                                        "</p>"+
                                    "</div>"+
                                    "<div class='modal-footer'>"+
                                        "<form method='POST' action='/biji/'"+data.id+">"+
                                            "<input type='hidden' name='_token' value='"+"{{csrf_token()}}"+"'>"+
                                            "<input type='hidden' name='_method' value='DELETE'>"+
                                            "<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>"+
                                            "<button type='submit' class='btn btn-danger'>"+
                                                "<i class='fa fa-times-circle'></i>Yes"+
                                            "</button>"+
                                        "</form>"+
                                    "</div>"+
                                "</div>"+
                            "</div>"+
                        "</div>"+
                        "<div class='btn-group' style='margin:0 0.5rem;'>"+
                            "<button type='button' class='btn btn-default'>共享</button>"+
                            "<button style='height: 34px;' type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>"+
                                "<span class='caret'></span>" +
                                "<span class='sr-only'>Toggle Dropdown</span>"+
                            "</button>"+
                            "<ul class='dropdown-menu'>"+
                                "<li><a href='/mail/"+data.id+"'>发送邮件</a></li>"+
                                "<li><a href='/circle/"+data.id+"'>发表至笔友圈</a></li>"+
                            "</ul>"+

                        "</div> <br/><br/>"+

                        "<div style='margin: 1rem 0.5rem;color: #666'>"+data.content+"</div>";
                    $('.biji_list_div').html(header);
                }
            });

        });
    }

});

