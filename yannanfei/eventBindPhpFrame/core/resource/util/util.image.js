/**
 * Created by happy on 17/11/6.
 * 图片相关的函数处理
 */
define(function(require, exports, module) {

return {
    /*dom 模型
     *<img style="max-width: 200px; max-height: 200px;" name="set_effect_img" src="">
     * <input type="file" name="set_effect_img" >

     * 事件
     *util.bind_image_preview(set_effect_img)
     *最终生成hidden     <input type="hidden" name="set_effect_img">存储16进制文本
     * */
    bind_image_preview:function(img_set_name,value,callback){//绑定图片上传事件
        var set_name=img_set_name;//图片设置
        var img=$("img[name="+set_name+"]");
        var file=$("input[type=file][name="+set_name+"]");
        file.attr('name','');
        file.attr('data-name',set_name);
        file.parent().append('<input type="hidden" name="'+set_name+'" />');
        if(value){ //默认值
            img.attr('src',value);
        }

        if(file.size()>0){ //说明有上传图片的功能
            //绑定事件
            file.on('change',function(){
                var f = this.files[0]; //this就是file的dom
                //判断类型是不是图片
                if(!/image\/\w+/.test(f.type)){
                    alert("请确保文件为图像类型");
                    return false;
                }
                if(Math.floor(f.size/1024/1024)>10){
                    alert('图片大小超过1MB，请上传10MB以内的图片');
                    return false;
                }

                var set_name=$(this).attr('data-name');
                var reader = new FileReader();
                reader.readAsDataURL(f);
                reader.onload = function(e){
                    if(callback){ //如果有回调直接调用回调函数
                        callback(this.result);
                    }
                    else{
                        $("img[name="+set_name+"]").attr('src',this.result);
                        $("input[name="+set_name+"]").val(this.result);
                    }
                }
            });
        }
    },
    //绑定图片改变
    bind_image_resize_preview:function(img_set_name,value,max_width,max_height){
        // 参数，最大高度
        this.bind_image_preview(img_set_name,value,function(base64_img){
            var src=base64_img;
            // 创建一个 Image 对象
            var image = new Image();
// 绑定 load 事件处理器，加载完成后执行

            image.onload = function(){
// 获取 canvas DOM 对象
                var canvas_html='<canvas  style="display: none"></canvas>';
                //创建一个canvas内存对象
                var canvas =$(canvas_html)[0];// document.getElementById("myCanvas");
                var ctx = canvas.getContext("2d");
// 如果高度超标或者宽度超标
                if(max_height&&max_width){//如果宽度和高度都限制了
                    if(image.height>max_height){
                        image.height=max_height;
                    }
                    if(image.width>max_width){
                        image.width=max_width;
                    }
                }
                else if(max_width&&image.width>max_width){//仅仅限制宽度
                    image.height *= max_width / image.width;
                    image.width = max_width;
                }


                if(max_height&&image.height >max_height) {
// 宽度等比例缩放 *=
                    image.width *= max_height / image.height;
                    image.height = max_height;
                }
// 获取 canvas的 2d 环境对象,
// 可以理解Context是管理员，canvas是房子

// canvas清屏
                ctx.clearRect(0, 0, canvas.width, canvas.height);
// 重置canvas宽高
                canvas.width = image.width;
                canvas.height = image.height;
// 将图像绘制到canvas上
                ctx.drawImage(image, 0, 0, image.width, image.height);
// !!! 注意，image 没有加入到 dom之中 //设置图片和hidden
                var new_src=canvas.toDataURL();
                //重新赋值
                $("img[name="+img_set_name+"]").attr('src',new_src);
                $("input[name="+img_set_name+"]").val(new_src);
            };
// 设置src属性，浏览器会自动加载。
// 记住必须先绑定事件，才能设置src属性，否则会出同步问题。
            image.src = src;
        });
    }
}
});
