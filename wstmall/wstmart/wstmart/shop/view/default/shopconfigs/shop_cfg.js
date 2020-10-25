function save(){
/* 表单验证 */
$('#shopCfg').isValid(function(v){
    if(v){
            var params = WST.getParams('.ipt');
            // 图片路径
            var shopAds = [];
            $('.j-gallery-img').each(function(){
              shopAds.push($(this).attr('v'));
            });
            params.shopAds = shopAds.join(',');
            // 图片轮播广告路径
            var shopAdsUrl = [];
            $('.cfg-img-url').each(function(){
              shopAdsUrl.push($(this).val());
            });
            params.shopAdsUrl = shopAdsUrl.join(',');

            var loading = WST.load({msg:'正在提交数据，请稍后...'});

            $.post(WST.U('shop/shopconfigs/editShopCfg'),params,function(data,textStatus){
              layer.close(loading);
              var json = WST.toJson(data);
              if(json.status=='1'){
                  WST.msg("操作成功",{icon:1});
              }else{
                    WST.msg(json.msg,{icon:2});
              }
            });

      }

    });
}





$(function(){
  //店铺顶部广告图上传
  WST.upload({
      pick:'#shopBannerPicker',
      formData: {dir:'shopconfigs'},
      accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
      callback:function(f){
        var json = WST.toJson(f);
        if(json.status==1){
          $('#uploadMsg').empty().hide();
          var shopbanner = json.savePath+json.thumb; //保存到数据库的路径
          $('#shopBanner').val(shopbanner);
          $('#shopBannerPreview').parent().show();
          $('.del-banner').show();
          $('#shopBannerPreview').attr('src',WST.conf.RESOURCE_PATH+'/'+json.savePath+json.thumb);
        }else{
            WST.msg(json.msg,{icon:2});
        }
    },
    progress:function(rate){
        $('#uploadMsg').show().html('已上传'+rate+"%");
    }
  });
     //店铺移动端顶部广告图上传
  WST.upload({
      pick:'#shopMoveBannerPicker',
      formData: {dir:'shopconfigs'},
      accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
      callback:function(f){
        var json = WST.toJson(f);
        if(json.status==1){
          $('#uploadMsg').empty().hide();
          var shopMoveBanner = json.savePath+json.thumb; //保存到数据库的路径
          $('#shopMoveBanner').val(shopMoveBanner);
          $('#shopMoveBannerPreview').parent().show();
          $('.del-banner').show();
          $('#shopMoveBannerPreview').attr('src',WST.conf.RESOURCE_PATH+'/'+json.savePath+json.thumb);
        }else{
            WST.msg(json.msg,{icon:2});
        }
    },
    progress:function(rate){
        $('#uploadMsg').show().html('已上传'+rate+"%");
    }
  });

  // 店铺街背景图上传
  WST.upload({
      pick:'#shopStreetImgPicker',
      formData: {dir:'shopconfigs'},
      accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
      callback:function(f){
        var json = WST.toJson(f);
        if(json.status==1){
          $('#uploadMsg').empty().hide();
          var shopbanner = json.savePath+json.thumb; //保存到数据库的路径
          $('#shopStreetImg').val(shopbanner);
          $('#shopStreetImgPreview').parent().show();
          $('.del-banner').show();
          $('#shopStreetImgPreview').attr('src',WST.conf.RESOURCE_PATH+'/'+json.savePath+json.thumb);
        }else{
            WST.msg(json.msg,{icon:2});
        }
    },
    progress:function(rate){
        $('#uploadMsg').show().html('已上传'+rate+"%");
    }
  });

/********** 轮播广告图片上传 **********/
var uploader = batchUpload({uploadPicker:'#batchUpload',uploadServer:WST.U('shop/index/uploadPic'),formData:{dir:'shopconfigs'},uploadSuccess:function(file,response){
        var json = WST.toJson(response);
        if(json.status==1){
          $li = $('#'+file.id);
          $li.append('<input type="hidden" class="j-gallery-img" iv="'+json.savePath + json.thumb+'" v="' +json.savePath + json.name+'"/>');
          var delBtn = $('<span class="btn-del">删除</span>');
          $li.append(delBtn);
          $li.append('<input class="cfg-img-url" type="text" value="" style="width:170px;" placeholder="广告路径">' );
          $li.css('height','213px');
          $li.find('.success').remove();
                  delBtn.on('click',function(){
                      delBatchUploadImg($(this),function(){
                        if($('.filelist').find('li').length==0){
                          $("#batchUpload").find('.placeholder').removeClass( 'element-invisible' );
                          $('.filelist').parent().removeClass('filled');
                          $('.filelist').hide();
                          $("#batchUpload").find('.statusBar').addClass( 'element-invisible' );
                        }
                        uploader.removeFile(file);
                        uploader.refresh();
                    });
            });
                  $('.filelist li').css('border','1px solid #eee');
        }else{
          WST.msg(json.msg,{icon:2});
        }
      }});
// 删除广告图片
$('.btn-del').click(function(){
      delBatchUploadImg($(this),function(){
        if($('.filelist').find('li').length==0){
          $("#batchUpload").find('.placeholder').removeClass( 'element-invisible' );
          $('.filelist').parent().removeClass('filled');
          $('.filelist').hide();
          $("#batchUpload").find('.statusBar').addClass( 'element-invisible' );
        }
        $(this).parent().remove();
        uploader.refresh();
      });
    })

function delBatchUploadImg(obj,fn){
  var c = WST.confirm({content:'您确定要删除广告图片吗?',yes:function(){
    $(obj).parent().remove("li");
    layer.close(c);
    fn();
  }});
}


});