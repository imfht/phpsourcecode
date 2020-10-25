layui.define([   'element' ], function(exports){
  var $ = layui.jquery;
  var layer = layui.layer;
  var form = layui.form;
  var element = layui.element;
  var upload = layui.upload;
  
  //上传图片
  if($('.upload-img')[0]){
    layui.use('upload', function(upload){
      var avatarAdd = $('.avatar-add');

      upload.render({
        elem: '.upload-img'
        ,url: upUrl
        ,size: 500
        ,before: function(){
          avatarAdd.find('.loading').show();
        }
        ,done: function(res){
          if(res.status == 1){
            $.post(setUrl, {
              avatar: res.msg
            }, function(res){
              if (res.status.status == 1) {
                 location.reload();
                 layui.layer.msg(res.status.msg, {icon: 1});
              }else {
                 layui.layer.msg(res.status.msg, {icon: 5});
              }
            });
          } else {
            layui.layer.msg(res.msg, {icon: 5});
          }
          avatarAdd.find('.loading').hide();
        }
        ,error: function(){
          avatarAdd.find('.loading').hide();
        }
      });
    });
  }

         
});