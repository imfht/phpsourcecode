var exif_obj = false;
//异步加载 /public/static/js/exif.js
jQuery.getScript("/public/static/js/exif.js")
    .done(function() {
        exif_obj = true;
    })
    .fail(function() {
        layer.msg('/public/static/js/exif.js加载失败',{time:800});
	});


		function uploadBtnChange(fileName,textName,pics,callback){
            var scope = this;
			//var pics = [];
            if(window.File && window.FileReader && window.FileList && window.Blob){ 
                //获取上传file
                var filefield = document.getElementById(fileName),
                file = filefield.files[0];
                //获取用于存放压缩后图片base64编码
                var compressValue = document.getElementById(textName);
				var oj = filefield.files;
				for(var i=0;i<oj.length;i++){
					processfile(oj[i],compressValue,pics,callback);
				}
            }else{
                alert("此浏览器不完全支持压缩上传图片");
            }
        }

        function processfile(file,compressValue,pics,callback) {
			var Orientation = 0;
			var alltags = {};
			//获取图片的参数信息
			if(exif_obj==true){
				EXIF.getData(file, function(){
					alltags = EXIF.pretty(this);
					//EXIF.getAllTags(this);
					Orientation = EXIF.getTag(this, 'Orientation');
				});
			}

            var reader = new FileReader();
            reader.onload = function (event) {
                var blob = new Blob([event.target.result]); 
                window.URL = window.URL || window.webkitURL;
                var blobURL = window.URL.createObjectURL(blob); 
                var image = new Image();
                image.src = blobURL;
                image.onload = function() {
                    var resized = resizeUpImages(image);					
					if(resized){
						// alert( alltags );
						$.post(severUrl, {'imgBase64':resized,'Orientation':Orientation,'tags':alltags}).done(function (res) {
							 if(res.code==1){
								 pics.push(res.path);
								 compressValue.value = pics.join(',');
								 if(typeof callback == 'function'){
									callback(res.url,pics);
								 }
							 }else{
								alert(res.info);
							 }
						}).fail(function () {
							alert('操作失败，请跟技术联系');
						});	
						
					}
                }
            };
            reader.readAsArrayBuffer(file);
        }

        function resizeUpImages(img) {
            //压缩的大小
            var max_width = 1920; 
            var max_height = 1080; 

            var canvas = document.createElement('canvas');
            var width = img.width;
            var height = img.height;
            if(width > height) {
                if(width > max_width) {
                    height = Math.round(height *= max_width / width);
                    width = max_width;
                }
            }else{
                if(height > max_height) {
                    width = Math.round(width *= max_height / height);
                    height = max_height;
                }
            }

            canvas.width = width;
            canvas.height = height;

            var ctx = canvas.getContext("2d");
            ctx.drawImage(img, 0, 0, width, height);
            //压缩率
            return canvas.toDataURL("image/jpeg",0.72); 
        }