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

layui.define(['jquery', 'tags', 'layedit', 'jqform', 'upload'], function(exports) {
    var $ = layui.jquery,
        layedit = layui.layedit,
        box = "",
        form = layui.jqform,
        tags = layui.tags;

    form.set({
        "blur": true,
        "form": "#form1"
    }).init();



    //自定义
    form.verify({
        username: function(value) {
            if (!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)) {
                return '文章标题不能有特殊字符';
            }
            if (/(^\_)|(\__)|(\_+$)/.test(value)) {
                return '文章标题首尾不能出现下划线\'_\'';
            }
            if (/^\d+\d+$/.test(value)) {
                return '文章标题不能全为数字';
            }
        },
        pass: [
            /^[\S]{6,12}$/, '密码必须6到12位，且不能出现空格'
        ],
        content: function(value) {
            layedit.sync(editIndex);
            return;
        }
    });
    tags.init();

    //上传文件设置
    layui.upload({
        url: '/php/upload.php',
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


    exports('myform', {});
});