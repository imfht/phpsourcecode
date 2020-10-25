var grid;

$(document).ready(function(){
    $(".select2").select2();
    grid = $("#data-table-command").bootgrid({
        css: {
            icon: 'md icon',
            iconColumns: 'md-view-module',
            iconDown: 'md-expand-more',
            iconRefresh: 'md-refresh',
            iconUp: 'md-expand-less'
        },
        ajax:true,
        url:"/Admin/menu/menulist",
        post:{
            _csrf:$("meta[name=csrf-token]").attr('content')
        },
        cache: false,
        formatters: {
            "commands": function(column, row) {

                var str = "";
                if(row.is_edit) {

                    str += "<a   class='btn btn-xs btn-primary' href='/menu/menuupdate/" + row.id + "'><i class='md-create'>更新</i></a>";
                }
                if(row.is_delete) {

                    str += " &nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:void(0)' class='btn btn-xs btn-danger' onclick=deteleData(" + row.id + ")><i class='md-clear'>删除</i></a>";
                }
                return str;
            },
            "showson" : function(column, row){

                var ss = "";
                    ss+="<a href='javascript:void(0)' class='showSon' data-id='"+row.id+"'><i class='md-add-circle'></i></a>";
                return  ss;
            }
        }
    }).on("loaded.rs.jquery.bootgrid", function() {

        grid.find(".showSon").on("click", function (e) {

            var child = $(this).children('i');
            var icon = child.attr('class');
            var parents = $(this).parent().parent();

            if(icon == 'md-add-circle'){

                child.attr('class','md-remove-circle');
                var id = $(this).data('id');
                $.ajax({
                        url:"/Admin/menu/menuchild",
                        type:"get",
                        dataType:'json',
                        headers:{
                            _csrf:$("meta[name=csrf-token]").attr('content')
                        },
                        data:{
                            id:id
                        },
                        success:function(data){

                            var str = getSonStr(data);
                            $(str).insertAfter(parents);
                        },
                        error:function(error){

                            layer.msg("服务器繁忙，请稍后重试");
                        }
                });
            }
            if(icon == 'md-remove-circle'){

                child.attr('class','md-add-circle');
                $(parents).next().remove();
            }
        });
    });
});

function getSonStr(data){

    var str =  "<td colspan='8'>";
    str += "<div class='box-body'>";
    str += "<table  class='table table-striped table-vmiddle'>";
    str += "<thead>";
    str += "<tr>";
    str += "<th ></th>";
    str += "<th >ID</th>";
    str += "<th >菜单名称</th>";
    str += "<th >地址</th>";
    str += "<th >权限</th>";
    str += "<th >创建时间</th>";
    str += "<th >更新时间</th>";
    str += "<th >操作</th>";
    str += "</tr>";
    str += "</thead>";
    str += "<tbody>";

    for(var i = 0;i<data.length;i++){

        str += "<tr>";
        str += "<td></td>";
        str += "<td>"+data[i].id+"</td>";
        str += "<td>"+data[i].name+"</td>";
        str += "<td>"+data[i].url+"</td>";
        str += "<td>"+data[i].slug+"</td>";
        str += "<td>"+data[i].created_at+"</td>";
        str += "<td>"+data[i].updated_at+"</td>";
        str += "<td>";
        if(data[i].is_edit) {
            str += "<a class='btn btn-xs btn-primary' href='/menu/menuupdate/" + data[i].id + "'><i class='md-create'>更新</i></a>";
        }
        if(data[i].is_delete) {
            str += "&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:void(0)' class='btn btn-xs btn-danger' onclick='deteleData(" + data[i].id + ")'><i class='md-clear'>删除</i></a>";
        }
        str += "</td>";
        str += "</tr>";
    }
    str += "</tbody>";
    str += "</table>";
    str += "</div></td>";
    return str;
}

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
                url:"/Admin/menu/menudelete",
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