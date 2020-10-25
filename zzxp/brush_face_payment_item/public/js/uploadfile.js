;(function ($, undefined) {
 $.fn.uploadfile = function (setting) {
 var defaultSetting = {
 //url : "{{ url('testoss')}}",
 num : '',  /*用来区分input框中value（阿里云图片存放路径）*/
 width : 600,
 height : 50,
 canDrag : true,
 canMultiple : false,
 success : function (fileName) { //单个文件上传成功的回调函数
 },
 error : function (fileName) { //单个文件上传失败的回调函数
 },
 complete : function () { //上传完成的回调函数
 }
 };
 
 //判断浏览器是否支持FileReader
 if(!window.FileReader){
 alert('您的浏览器不支持FileReader，请更换浏览器。');
 return;
 }
 
 setting = $.extend(true, {}, defaultSetting, setting);
 setting.width < 450 && (setting.width = 450);
 
 $(this).each(function (i, item) {
 var demoHtml = '';
 //是否可以拖拽图片上传，构造dom结构
 if(setting.canDrag){
 setting.height < 200 && (setting.height = '');
 demoHtml += '<div class="file_sel">';
 demoHtml +=  '<div class="file_input">';
 demoHtml +=  '<div class="sel_file_img">';
 demoHtml +=  '</div>';
 demoHtml +=  '<div class="sel_file_btn">';
 demoHtml +=  '<input type="file"/>';
 demoHtml +=  '</div>';
 demoHtml +=  '</div>';

 demoHtml += '</div>';
 demoHtml += '<div class="file_info_handle">';
 demoHtml +=  '<div class="file_info">';
 demoHtml +=  '当前选择了<span class="file_count">0</span>个文件，共<span class="file_size">0</span>KB。';
 demoHtml +=  '<button class="uploadfile">开始上传</button>';
 demoHtml +=  '</div>';
 demoHtml += '</div>';
 demoHtml += '<div class="file_show">';
 demoHtml += '</div>';
 }else{
 setting.height < 50 && (setting.height = 50);
 $(item).addClass('noDrag');
 demoHtml += '<div class="file_info_handle">';
 demoHtml += '<div class="file_info">';
 demoHtml += '当前选择了<span class="file_count">0</span>个文件，共<span class="file_size">0</span>KB。';
 demoHtml += '<input type="file"/>';
 demoHtml += '<button class="continue_sel">继续选择</button>';
 demoHtml += '<button class="uploadfile">开始上传</button>';
 demoHtml += '</div>';
 demoHtml += '</div>';
 demoHtml += '<div class="file_show">';
 demoHtml += '<div class="sel_file_btn">';
 demoHtml += '<input type="file"/>';
 demoHtml += '<div class="sel_btn"></div>';
 demoHtml += '</div>';
 demoHtml += '</div>';
 }
 $(item).css({
 width : setting.width,
 height : setting.height,
 display : 'block'
 });
 $(item).html(demoHtml);
 
 //获取DOM节点
 var fileArr = [],
 fileSize = 0,
 _this = $(item),
 fileDrag = $('.file_sel .file_drag', _this),
 selFileIpt = $('input[type=file]', _this),
 selFileBtn = selFileIpt.next();
 fileCount = $('.file_info_handle .file_info .file_count', _this),
 fileSz = $('.file_info_handle .file_info .file_size', _this),
 beginUpload = $('.file_info_handle .file_info .uploadfile', _this),
 fileShow = $('.file_show', _this),
 noDragSelFile = $('.file_show .sel_file_btn', _this);
  
 //显示拖拽上传部分
 setting.canDrag || fileShow.show();
 
 //是否可以多选
 setting.canMultiple && selFileIpt.attr('multiple', 'multiple');
 
 //绑定事件
 selFileIpt.on('change', selFile);
 
 //让按钮去触发input的click事件
 selFileBtn.on('click', function () { 
 $(this).prev().click();
 })
 
 fileDrag.on({
 dragover : dragOver, 
 drop : selFile
 })
 
 beginUpload.on('click', upLoadFile);
 
  
 
 // 选择文件
 function selFile (e) {
 e = e || window.event;
 //阻止浏览器的默认行为
 if(e.preventDefault){ 
  e.preventDefault(); 
 }else{
  e.returnValue = false;
 }
 var files = this.files || event.dataTransfer.files,
 src = 'img/',
 imgSrc;
 Array.prototype.forEach.call(files, function (item, i) {
 
  //防止重复选择相同的文件
  var notExist = fileArr.some(function (existFile) {
  return existFile.name === item.name;
  })
  if(notExist && fileArr.length != 0){
  return !notExist;
  }
 
  fileArr.push(item);
  var fr = new FileReader();
  fr.readAsDataURL(item);
  fr.onload = function () {
 
  //判断展示的文件类型
  if(item.type.indexOf("image") > -1){
  imgSrc = fr.result;
  }else if(item.name.indexOf("rar") > -1){
  imgSrc = src + 'rar.png';
  }else if(item.name.indexOf("zip") > -1){
  imgSrc = src + 'zip.png';
  }else if(item.type.indexOf("text") > -1){
  imgSrc = src + '';
  }else{
  imgSrc = src + 'file.png';
  }
 
  //展示选择的文件
  var imgDom = $('<span class="img_box"><span class="up_load_success" title="上传成功"></span><span class="img_handle"><span class="file_name" title="'+ item.name +'">'+ item.name +'</span><span class="icon-bin"></span></span><img src="'+ imgSrc +'"/></span>');
  if(setting.canDrag){
  fileShow.css('display') === 'none' && fileShow.show();
  fileShow.append(imgDom);
  }else{
  fileShow.css('display') === 'none' && fileShow.show();
  noDragSelFile.before(imgDom);
  }
  }
 })
 
 //选择的文件的信息
 fileCount.html(fileArr.length);
 fileSz.html(getFileInfo());
 
 //防止在删除了上次选择的文件后，再次选择相同的文件无效的问题。
 this.value =''; 
 }
 
 //拖拽
 function dragOver (e) {
 var event = e || window.event;
 event.preventDefault();
 }
 
 //上传文件
 function upLoadFile () {
 if(!fileArr.length){
  alert('请选择文件');
  return;
 }
 fileArr.forEach(function (item, i) {

  var upLoadSuccess = $('.img_box').eq(i).children('.up_load_success');
   
  //防止重复上传
  if(upLoadSuccess.css('display') === 'block') return false;

  var formData = new FormData();
  formData.append('file', item);
  /*console.log(item);
  alert('xxx');
  alert(setting.url);*/
  $.ajax({
  url: setting.url,
  type: 'POST',
  cache: false,
  data: formData,
  processData: false,
  contentType: false,
   //data反回的阿里云图片储存绝对路径
   'success':function(data){
    //$('.file_name').html('<span style="color:green">'+ '上传成功'+'</span>');
//alert(data.replace('buketech.oss-cn-shanghai.aliyuncs.com','static.buketech.com'));exit();
    //单文件上传时
    if(setting.canMultiple==false){
       //若是input框则：
       $('#upload_file_control'+setting.num).attr("value",data.replace('buketech.oss-cn-shanghai.aliyuncs.com','static.buketech.com'));
    }

    //alert(setting.tt);

    //多文件上传时(在视图页面设置了tt:'ipt'，即input上传/增加图片时)
    if(setting.canMultiple==true && setting.tt=='ipt'){
     //原来的存储的绝对路径内容
     var ori = $('#upload_file_control').val();
     //若是input则：
     $('#upload_file_control'+setting.num).attr("value",ori+data.replace('buketech.oss-cn-shanghai.aliyuncs.com','static.buketech.com')+',');
    }

    //多文件上传时(文件储存绝对路径上传用textarea时，且没有设置tt:'ipt'或者没有tt时)
    if(setting.canMultiple==true && (setting.tt!='ipt' || typeof(setting.tt) == "undefined")){
      //若是textarea则：
      $('#upload_file_control'+setting.num).append(data.replace('buketech.oss-cn-shanghai.aliyuncs.com','static.buketech.com')+',');
    }



   }
  }).done(function(res) {
  //上传成功图标
  upLoadSuccess.show();

  //单个文件上传成功执行回调
  setting.success(item.name);

  //全部文件上传完成执行回调函数
  (i === (fileArr.length - 1)) && setting.complete();
  }).fail(function(res) {
  //单个文件上传失败执行回调
  setting.error(item.name);
 
  (i === (fileArr.length - 1)) && setting.complete();
  });
 })
 }
 
 //计算文件信息
 function getFileInfo () {
 //每次重新计算大小，防止单位不同造成错误
 fileSize = 0;
 fileArr.forEach(function (item, i) {
  fileSize += item.size;
 })
 fileSize = (fileSize / 1024).toFixed(2);
 return fileSize;
 }
 
 fileShow.on('click', '.icon-bin' , function () {
 //删除节点
 var index = $(this).parents('.img_box').index();
 $(this).parents('.img_box').remove();
 
 //删除上传文件
 fileArr.splice(index, 1);
 
 //修改文件信息
 fileCount.html(fileArr.length);
 fileSz.html(getFileInfo());
 
 //隐藏文件显示区域
 !setting.canDrag || fileArr.length || fileShow.hide();
 })
 })
 }
})(jQuery)