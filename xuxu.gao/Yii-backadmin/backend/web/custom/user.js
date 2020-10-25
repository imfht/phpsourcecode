

$(document).ready(function(){

    $(".select2").select2();

     $("#data-table-command").bootgrid({
        css: {
            icon: 'md icon',
            iconColumns: 'md-view-module',
            iconDown: 'md-expand-more',
            iconRefresh: 'md-refresh',
            iconUp: 'md-expand-less'
        },
        ajax:true,
        url:"/Admin/user/userlist",
        post:{
            _csrf:$("meta[name=csrf-token]").attr('content')
        },
        cache: false,
        formatters: {
            "commands": function(column, row) {

                var str = "";
                if(row.is_edit) {
                    str += "<a  class='btn btn-xs btn-primary' href='/user/userupdate/" + row.id + "'><i class='md-create'>更新</i></a>";
                }
                if(row.is_delete) {
                    str += " &nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:void(0)' class='btn btn-xs btn-danger' onclick=deteleData(" + row.id + ")><i class='md-clear'>删除</i></a>";
                }
               return str;
            }
        }
    });
});
function deteleData(id){

    swal({
        title: "确定删除该条记录吗?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#2196F3",
        confirmButtonText: "确定",
        cancelButtonText:"取消",
        closeOnConfirm: false },
        function(){
            $.ajax({
                      url:"/Admin/user/userdelete",
                      type:"post",
                      data:{
                          id:id,
                          _csrf:$("meta[name=csrf-token]").attr('content')
                      },
                      dataType:"json",
                      success:function(data){

                          if(data.status == 1){

                              swal({ title: "提示信息 ",text: data.msg,showConfirmButton: false,type: "success",timer:2000});
                              $('#data-table-command').bootgrid('reload');
                          }else{

                              swal({ title: "提示信息 ",text: data.msg,showConfirmButton: false,type: "success",timer:2000});
                          }

                       },
                      error:function (){

                           layer.msg("服务器繁忙，请稍后重试");
                      }
            });
        });
}

