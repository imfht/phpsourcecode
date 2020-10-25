var WST_CURR_PAGE = 1;
//图片文件上传
function userComplainInit(){
	 var uploader =WST.upload({
        pick:'#filePicker',
        formData: {dir:'informsImg',isThumb:0},
        fileNumLimit:5,
        accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
        callback:function(f,file){
          var json = WST.toJson(f);
          if(json.status==1){
          var tdiv = $("<div style='width:75px;float:left;margin-right:5px;'>"+
                       "<img class='inform_pic"+"' width='75' height='75' src='"+WST.conf.RESOURCE_PATH+"/"+json.savePath+json.thumb+"' v='"+json.savePath+json.name+"'></div>");
          var btn = $('<div style="position:relative;top:-80px;left:60px;cursor:pointer;" ><img src="'+WST.conf.ROOT+'/wstmart/home/view/default/img/seller_icon_error.png"></div>');
          tdiv.append(btn);
          $('#picBox').append(tdiv);
          btn.on('click','img',function(){
            uploader.removeFile(file);
            $(this).parent().parent().remove();
            uploader.refresh();
          });
          }else{
            WST.msg(json.msg,{icon:2});
          }
      },
      progress:function(rate){
          $('#uploadMsg').show().html('已上传'+rate+"%");
      }
    });
}
function saveInforms(historyURL){
   /* 表单验证 */
  $('#informForm').validator({
              fields: {
                  informContent: {
                    rule:"required",
                    msg:{required:"清输入举报内容"},
                    tip:"请输入举报内容",
                  },
                  informType: {
                    rule:"checked;",
                    msg:{checked:"举报类型不能为空"},
                    tip:"请选择举报类型",
                  }
                  
              },
            valid: function(form){
              var params = WST.getParams('.ipt');
              var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
              var img = [];
                  $('.inform_pic').each(function(){
                    img.push($(this).attr('v'));
                  });
                  params.informAnnex = img.join(',');
                  $.post(WST.U('home/informs/saveInform'),params,function(data,textStatus){
                    layer.close(loading);
                    var json = WST.toJson(data);
                    if(json.status=='1'){
                        WST.msg('您的举报已提交，请留意信息回复', {icon: 6},function(){
                         location.href = WST.U('home/informs/index');
                       });
                    }else{
                          WST.msg(json.msg,{icon:2});
                    }
                  });
        }
  });
}

function toView(id){
  location.href=WST.U('home/Informs/getUserInformDetail','id='+id+'&p='+WST_CURR_PAGE);
}
function informByPage(p){
  $('#list').html('<img src="'+WST.conf.ROOT+'/wstmart/home/view/default/img/loading.gif">正在加载数据...');
  var params = {};
  params = WST.getParams('.s-query');
  params.key = $.trim($('#key').val());
  params.page = p;
  $.post(WST.U('home/informs/queryUserInformPage'),params,function(data,textStatus){
      var json = WST.toJson(data);
      if(json.status==1){
          if(params.page>json.data.last_page && json.data.last_page >0){
              informByPage(json.data.last_page);
              return;
          }
          var gettpl = document.getElementById('tblist').innerHTML;
          laytpl(gettpl).render(json.data.data, function(html){
            $('#list').html(html);
          });
          if(json.data.last_page>1){
            laypage({
               cont: 'pager', 
               pages:json.data.last_page,
               curr: json.data.current_page,
               skin: '#e23e3d',
               groups: 3,
               jump: function(e, first){
                    if(!first){
                        informByPage(e.curr);
                    }
                  } 
            });


          }else{
            $('#pager').empty();
          }
        }  
  });
}