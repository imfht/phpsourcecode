#####功能函数，seajs默认引用

* 添加图片绑定事件
dom:   dom 模型
            *<img style="max-width: 200px; max-height: 200px;" name="set_effect_img" src="">
            * <input type="file" name="" id="input44" data-name="set_effect_img">
             <input type="hidden" name="set_effect_img">
      js事件：
      util.bind_image_preview(set_effect_img);

* 设置form表单默认值 移到util.set_default_value()函数中，可以设置input  select  textarea等的值