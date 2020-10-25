/**
 * Created by spatra on 15-4-19.
 */

requirejs(['basic'], function(basic){

  /**
   * 实现左侧导航栏的自动高亮当前栏目
   */
  function sliderBarAutoHighLight(){
    var currentUrl = window.location.href;
    var parties = currentUrl.split('/');
    var currentModule = parties[ parties.length - 1];

    var lis = document.getElementsByClassName('slider-bar-item');
    console.log( lis.hasOwnProperty('length'));
    for(var i = 0, length = lis.length; i < length; ++i ){
      var currentLi = lis[i];

      if( currentLi.nodeName !== 'A' ) continue;

      console.log(currentLi.href);
      if( currentLi.href.indexOf(currentModule) !== -1 ){
        currentLi.className += ' selected';
        break;
      }
    }

  }

  basic.whenReady(sliderBarAutoHighLight);

  /**
   * 实现点击按钮上传图片
   */
  function imageUpload(){
    var uploadInput = document.getElementById('img-upload-input');
    var headImage = document.getElementById('head-image');
    if( !uploadInput || !headImage) return;

    basic.addEvent(uploadInput, 'change', function(event){
      var file = this.files[0];
      var objUrl = webkitURL.createObjectURL(file);

      var img = new Image();
      img.onload = function(){
        var width = img.width, height = img.height;

        var imgCanvas = document.createElement('canvas');
        imgCanvas.width = width;
        imgCanvas.height = height;
        var ctx = imgCanvas.getContext('2d');

        ctx.drawImage(img, 0, 0, width, height);

        headImage.src = imgCanvas.toDataURL('image/jpeg');
      };

      img.src = objUrl;
    });
  }

  basic.whenReady(imageUpload);

});