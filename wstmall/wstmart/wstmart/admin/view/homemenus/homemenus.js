var grid;
$(function(){
  $('#headTip').WSTTips({width:90,height:35,callback:function(v){
     var diff = v?113:53;
  }});
  grid = $("#maingrid").WSTGridTree({
    url:WST.U('admin/homemenus/pageQuery'),
    pageSize:10000,
    pageSizeOptions:10000,
    height:'99%',
        width:'100%',
        minColToggle:6,
        delayLoad :true,
        rownumbers:true,
        columns: [
          { display: '菜单名称', name: 'menuName', id:"menuId", width:190,isSort: false},
          { display: '菜单Url', name: 'menuUrl', isSort: false},
          { display: '菜单类型', name: 'menuType',  width:80,isSort: false,render:function(rowdata, rowindex, value){
            if(rowdata['menuType']==0){
              return '用户菜单';
            }else if(rowdata['menuType']==1){
              return '商家菜单';
            }else if(rowdata['menuType']==2){
              return '门店菜单';
            }else if(rowdata['menuType']==3){
              return '供货商菜单';
            }
          }},
          { display: '是否显示', name: 'isShow', width:80,isSort: false,render :function(item, rowindex, value){
            return '<input type="checkbox" '+((item.isShow==1)?"checked":"")+' class="ipt" lay-skin="switch" lay-filter="isShow" data="'+item.menuId+'" lay-text="显示|隐藏">';
          }},
          { display: '排序号', name: 'menuSort', width:60,isSort: false,render:function(rowdata,rowindex,value){
             return '<span class="classSort" style="cursor:pointer;color:blue;" ondblclick="changeSort(this,'+rowdata["menuId"]+');">'+rowdata['menuSort']+'</span>';
          }},
          { display: '操作', name: 'op',width:290,isSort: false,render: function (rowdata, rowindex, value){
              var h = "";
              if(WST.GRANT.QTCD_01)h += "<a  class='btn btn-blue' href='javascript:toEdit(0," + rowdata['menuId'] + ","+rowdata['menuType']+")'><i class='fa fa-plus'></i>添加子菜单</a> ";
              if(WST.GRANT.QTCD_02)h += "<a  class='btn btn-blue' href='javascript:getForEdit("+rowdata["parentId"]+"," + rowdata['menuId'] + ")' href='"+WST.U('admin/homemenus/toEdit','menuId='+rowdata['menuId'])+"'><i class='fa fa-pencil'></i>修改</a> ";
              if(WST.GRANT.QTCD_03)h += "<a  class='btn btn-red' href='javascript:toDel("+rowdata["parentId"]+"," + rowdata['menuId'] + ")'><i class='fa fa-trash-o'></i>删除</a> ";
              return h;
          }}
        ],
        callback:function(){
            $('.classSort').poshytip({content:'双击修改排序号',showTimeout:0,hideTimeout:1,alignX: 'center',
              offsetY: 10,timeOnScreen:1000,allowTipHover: false});
            layui.form.render();
         }
    });
    layui.form.on('switch(isShow)', function(data){
          var id = $(this).attr("data");
          if(this.checked){
              toggleIsShow(id, 1);
          }else{
              toggleIsShow(id, 0);
          }
    });
    $('body').css('overflow-y','auto');
})


var oldSort;
function changeSort(t,id){
  if(!WST.GRANT.QTCD_02)return;
  $(t).attr('ondblclick'," ");
var html = "<input type='text' id='sort-"+id+"' style='width:30px;padding:2px' onblur='doneChange(this,"+id+")' value='"+$(t).html()+"' />";
 $(t).html(html);
 $('#sort-'+id).focus();
 $('#sort-'+id).select();
}
function doneChange(t,id){
  var sort = ($(t).val()=='')?0:$(t).val();
  if(sort==oldSort){
    $(t).parent().attr('ondblclick','changeSort(this,'+id+')');
    $(t).parent().html(parseInt(sort));
    return;
  }
  $.post(WST.U('admin/homemenus/changeSort'),{id:id,menuSort:sort},function(data){
    var json = WST.toAdminJson(data);
    if(json.status==1){
        $(t).parent().attr('ondblclick','changeSort(this,'+id+')');
        $(t).parent().html(parseInt(sort));
    }
  });
}




function toDel(pid,menuId){
  var box = WST.confirm({content:"删除该菜单会将下边的子菜单也一并删除，您确定要删除吗?",yes:function(){
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/homemenus/del'),{menuId:menuId},function(data,textStatus){
      layer.close(loading);
      var json = WST.toAdminJson(data);
      if(json.status=='1'){
        WST.msg("操作成功",{icon:1});
        layer.close(box);
        grid.reload(pid);
      }else{
        WST.msg(json.msg,{icon:2});
      }
    });
  }});
}



function edit(pid,menuId){
  //获取所有参数
  var params = WST.getParams('.ipt');
    params.menuId = menuId;
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/homemenus/'+((menuId==0)?"add":"edit")),params,function(data,textStatus){
      layer.close(loading);
      var json = WST.toAdminJson(data);
      if(json.status=='1'){

          WST.msg("操作成功",{icon:1});
          loadGrid();
      }else{
            WST.msg(json.msg,{icon:2});
      }
    });
}
function toggleIsShow(menuId, isShow){
  if(!WST.GRANT.QTCD_02)return;
  $.post(WST.U('admin/homemenus/setToggle'), {'menuId':menuId, 'isShow':isShow}, function(data, textStatus){
    var json = WST.toAdminJson(data);
    if(json.status=='1'){
      WST.msg("操作成功",{icon:1});
      grid.reload(menuId);
    }else{
      WST.msg(json.msg,{icon:2});
    }
  })
}

function getForEdit(pid,menuId){
  $('#menuForm')[0].reset();
  var loading = WST.msg('正在获取数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/homemenus/get'),{menuId:menuId},function(data,textStatus){
          layer.close(loading);
          var json = WST.toAdminJson(data);
          if(json.menuId){
              WST.setValues(json);
              toEdit(json.menuId,pid);
          }else{
              WST.msg(json.msg,{icon:2});
          }
   });
}

function toEdit(menuId,parentId,tId){
  var title = "编辑";
  if(menuId==0){
    $('#menuForm')[0].reset();
    title = "新增";
    WST.setValue('isShow',1);
  }
  if(parentId>0){
	  $('#menuTypes').hide();
  }else{
	  $('#menuTypes').show();
  }
  if(tId==1){$('#menuType').val(1);}
  layui.form.render();
  var box = WST.open({title:title,type:1,content:$('#menuBox'),area: ['650px', '580px'],btn:['确定','取消'],
    end:function(){$('#menuBox').hide();},yes:function(){
    $('#menuForm').submit();
  }});
  $('#menuForm').validator({
        fields: {
          'menuName': {rule:"required;",msg:{required:'请输入菜单名称'}},
          'menuUrl': {rule:"required;",msg:{required:'请输入菜单Url'}},
          'menuSort': {rule:"required;integer",msg:{required:'请输入排序号',number:"请输入数字"}}
        },
        valid: function(form){
          var params = WST.getParams('.ipt');
            params.menuId = menuId;
            params.parentId = parentId;
            params.isShow = params.isShow?params.isShow:0;
          var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
         $.post(WST.U('admin/homemenus/'+((menuId==0)?"add":"edit")),params,function(data,textStatus){
           layer.close(loading);
           var json = WST.toAdminJson(data);
           if(json.status=='1'){
                WST.msg("操作成功",{icon:1});
                $('#menuBox').hide();
                $('#menuForm')[0].reset();
                layer.close(box);
                grid.reload(params.parentId);
           }else{
             WST.msg(json.msg,{icon:2});
            }
          });

      }

  });
}
function loadGrid(){
    $("#maingrid").WSTGridTree({
    url:WST.U('admin/homemenus/pageQuery',{menuType:$('#s_menuType').val()}),
    pageSize:10000,
    pageSizeOptions:10000,
    height:'99%',
        width:'100%',
        minColToggle:6,
        delayLoad :true,
        rownumbers:true,
        columns: [
          { display: '菜单名称', name: 'menuName', id:"menuId", width:190,isSort: false},
          { display: '菜单Url', name: 'menuUrl', isSort: false},
          { display: '菜单类型', name: 'menuType',  width:80,isSort: false,render:function(rowdata, rowindex, value){
             return (rowdata['menuType']==0)?'用户菜单':'商家菜单';
          }},
          { display: '是否显示', name: 'isShow', width:80,isSort: false,render :function(item, rowindex, value){
            return '<input type="checkbox" '+((item.isShow==1)?"checked":"")+' class="ipt" lay-skin="switch" lay-filter="isShow" data="'+item.menuId+'" lay-text="显示|隐藏">';
          }},
          { display: '排序号', name: 'menuSort', width:60,isSort: false,render:function(rowdata,rowindex,value){
             return '<span style="cursor:pointer;color:blue;" ondblclick="changeSort(this,'+rowdata["menuId"]+');">'+rowdata['menuSort']+'</span>';
          }},
          { display: '操作', name: 'op',width:290,isSort: false,render: function (rowdata, rowindex, value){
              var h = "";
              if(WST.GRANT.QTCD_01)h += "<a  class='btn btn-blue' href='javascript:toEdit(0," + rowdata['menuId'] + ","+rowdata['menuType']+")'><i class='fa fa-plus'></i>添加子菜单</a> ";
              if(WST.GRANT.QTCD_02)h += "<a  class='btn btn-blue' href='javascript:getForEdit("+rowdata["parentId"]+"," + rowdata['menuId'] + ")' href='"+WST.U('admin/homemenus/toEdit','menuId='+rowdata['menuId'])+"'><i class='fa fa-pencil'></i>修改</a> ";
              if(WST.GRANT.QTCD_03)h += "<a  class='btn btn-red' href='javascript:toDel("+rowdata["parentId"]+"," + rowdata['menuId'] + ")'><i class='fa fa-trash-o'></i>删除</a> ";
              return h;
          }}
        ],
        callback:function(){
          layui.form.render();
        }
    });
    layui.form.on('switch(isShow)', function(data){
          var id = $(this).attr("data");
          if(this.checked){
              toggleIsShow(id, 1);
          }else{
              toggleIsShow(id, 0);
          }
    });
}