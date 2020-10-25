layui.use(['layer', 'form','layedit'], function(){
  var layer = layui.layer,
  layedit = layui.layedit,
  form = layui.form;
  $ = layui.jquery;
  var layeditIndex;
  layedit.set({
    uploadImage: {
      url: '/plugins/upload/uploadimg.php?fp=upimg' //接口url
      ,type: 'post' //默认post
    }
  });
  //验证输入
  form.verify({
  verifytext: function(value, item){ //value：表单的值、item：表单的DOM对象
    if(!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)){
      return '不能有特殊字符';
    }
    if(/(^\_)|(\__)|(\_+$)/.test(value)){
      return '首尾不能出现下划线\'_\'';
    }
    if(/^\d+\d+\d$/.test(value)){
      return '不能全为数字';
    }
  }
  ,cd2t30: [
    /^[\S]{2,30}$/
    ,'长度2-30字符'
  ]
  ,cd2t10: [
    /^[\S]{2,10}$/
    ,'长度2-10字符'
  ]

});      
  layeditIndex = layedit.build('content'); //建立编辑器 
    //邮箱开关
    form.on('checkbox(closed)', function(data){
      if(data.elem.checked == true){
        $('#email').css('display','inline-block'); 
         $("#emailinput").attr("lay-verify", "required|email");
      }else{
        $('#email').css('display','none'); 
       $("#emailinput").attr("lay-verify", "");
      };
    });

//end function
$('#verify').pointsVerify({
    mode : 'fixed',
    defaultNum : 4,
    checkNum : 2,
    vSpace : 5,
    imgUrl : '/src/images/',
    imgName : ['1.jpg', '2.jpg','3.jpg'],
     imgSize : {
      width: '400px',
      height: '200px',
    },
    barSize : {
      width: '400px',
      height : '40px',
    },
    ready : function() {
      $('#btn').click(function(){
        layedit.sync(layeditIndex);//同步
        form.on("submit(addmsg)",function(data){
          top.layer.msg('请完成验证后提交！', function(){});
        })
      })
    },
    success : function() {
      $('#btn').attr('lay-filter','verifypass');
      $('#btn').click(function(){
        layedit.sync(layeditIndex);//同步
        form.on("submit(verifypass)",function(data){
        var formData = new FormData(addmsg) ;//
        var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        $.ajax({            
          url:"add_do.php",
          type : 'POST', 
          data : formData, 
             processData : false, 
             contentType : false,
             success: function(data){
                   if(data.trim()=="OK")
                   {
                    setTimeout(function(){
                      top.layer.msg("提交成功，敬请期待！");
                      top.layer.close(index);
                      layer.closeAll("iframe");
                //刷新父页面
                parent.location.reload();
              },2000);
                    return false;
                }
                else
                {
                  setTimeout(function(){
                      top.layer.close(index);
                      top.layer.msg("数据提交失败，请重新提交，错误信息：<red>"+data.trim()+"</red>");
              },2000);
                  return false; 
              }
          }
          });
        //end ajax
        })
      })
    },
    error : function() {
     top.layer.msg('验证码不匹配！请重新验证', function(){});
    }  
  });



  
})
//end ui