/*
 * @Author: Paco
 * @Date:   2017-02-15
 * +----------------------------------------------------------------------
 * | jqadmin [ jq酷打造的一款懒人后台模板 ]
 * | Copyright (c) 2017 http://jqadmin.jqcool.net All rights reserved.
 * | Licensed ( http://jqadmin.jqcool.net/licenses/ )
 * | Author: Paco <admin@jqcool.net>
 * +----------------------------------------------------------------------
 */

layui.define(['jquery', 'tags', 'layedit', 'laydate' , 'form', 'upload'], function(exports) {
    var $ = layui.jquery,
        layedit = layui.layedit,
        box = "",
        form = layui.form,
        tags = layui.tags;

    form.set({
        "blur": true,
        "form": "#form1",
        "complete":"reg"
    }).init();
    form.reg = function(ret, options, that) {
          //console.log(ret);
         // console.log(options);
         // console.log(that);
        if (ret.status==200){
            layer.msg(data.msg, {icon: 6,time:1000}, function(index){
                layer.close(index);
                window.location.href=data.url;
            });
        }else{
            layer.msg(ret.msg, {icon: 5,time:2000});
        }


    }
    tags.init();

    //上传文件设置
    layui.upload({
        //url: '/static/jqadmin/php/upload.php',
        url: '/kbcms/admin_upload/upload',
        before: function(input) {
            box = $(input).parent('form').parent('div').parent('.layui-input-block');
            if (box.next('div').length > 0) {
                box.next('div').html('<div class="imgbox"><p>上传中...</p></div>');
            } else {
                box.after('<div class="layui-input-block"><div class="imgbox"><p>上传中...</p></div></div>');
            }
        },
        success: function(res) {
            if (res.status == 200) {
                box.next('div').find('div.imgbox').html('<img src="' + res.url + '" alt="..." class="img-thumbnail">');
                box.find('input[type=hidden]').val(res.url);
                form.check(box.find('input[type=hidden]'));
            } else {
                box.next('div').find('p').html('上传失败...')
            }
        }
    });

    //富文本框
    layedit.set({
        uploadImage: {
            url: '/php/upload.php'
        }
    });
    var editIndex = layedit.build('content');


    exports('form', {});
});