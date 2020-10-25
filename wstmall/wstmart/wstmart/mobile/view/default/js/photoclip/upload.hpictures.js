 jQuery.noConflict();
//关闭图片上传区
 function closeUploadArea(){
	var data='#upload_close,#upload_button,#upload_modal';
	var data2='.return_users,.useri_info,#useri_info,#footer';
	WST.showHide('',data);
	WST.showHide(1,data2);
     //清空图片上传区的内容
     $('#clipArea').find('img').remove();
     $('#file').val('');
     $('#view').css('background-image','');
     $('#imgData').val('');
 }
 jQuery('#uploadImg').on('change', function() {
	var data='.return_users,.useri_info,#useri_info,#footer';
	var data2='#upload_close,#upload_button,#upload_modal';
	WST.showHide('',data);
	WST.showHide(1,data2);
});
//头像上传
jQuery("#clipArea").photoClip({
    width: 350,
    height: 350,
    file: "#uploadImg",
    view: "#view",
    ok: "#upload_button",
    loadStart: function() {
    	$('#Load').show();
    },
    loadComplete: function() {
    	$('#Load').hide();
    },
    clipFinish: function(dataURL) {
        jQuery('#imgData').val(dataURL);
        var imgData = $('#imgData').val();
        if(!imgData || imgData==''){
        	WST.msg('请先选择图片','info');
            return false;
        }
        // 上传裁剪好的图片
        funUploadFile(dataURL);

    }
});


 /**
 * @param base64Codes
 * 图片的base64编码
 */
funUploadFile=function(base64Codes){
    var self = this;
    var formData = new FormData();
    //convertBase64UrlToBlob函数是将base64编码转换为Blob
    //append函数的第一个参数是后台获取数据的参数名,在php中用$FILES['imageName']接收，
    var imgSuffix = base64Codes.split(";")[0].split('/')[1];
    formData.append("imageName",self.convertBase64UrlToBlob(base64Codes),"image."+imgSuffix);
    //ajax 提交form
    $.ajax({
        // 你后台的接收地址
        url : WST.U('mobile/users/uploadPic',{'dir':'users','isTumb':1,'isLocation':1}), 
        type : "POST",
        data : formData,
        dataType:"json",
        processData : false,         // 告诉jQuery不要去处理发送的数据
        contentType : false,        // 告诉jQuery不要去设置Content-Type请求头
        success:function(data){
            var json = WST.toJson(data);
            if(json.status==1){
            $.post(WST.U('mobile/users/editUserInfo'), {userPhoto:json.savePath+json.name}, function(data){
                if(json.status==1){
                    WST.msg("修改头像成功",'success');
                    jQuery('#imgurl').attr('src', WST.conf.RESOURCE_PATH +'/'+json.savePath+json.name);
                }else{
                    WST.msg('修改头像失败，请重试','warn');
                    return false;
                }
              });
            }else{
                WST.msg(json.msg,'warn');
            }
            closeUploadArea();
            $('#Load').hide();

        }
    });
}

/**
 * 将以base64的图片url数据转换为Blob
 * @param urlData
 * 用url方式表示的base64图片数据
 */
convertBase64UrlToBlob=function(urlData){
    //去掉url的头，并转换为byte
    var bytes=window.atob(urlData.split(',')[1]);        
    //处理异常,将ascii码小于0的转换为大于0
    var ab = new ArrayBuffer(bytes.length);
    var ia = new Uint8Array(ab);
    for (var i = 0; i < bytes.length; i++) {
        ia[i] = bytes.charCodeAt(i);
    }
    // 此处type注意与photoClip初始化中的outputType类型保持一致
    return new Blob( [ab] , {type : 'image/jpeg'});
}