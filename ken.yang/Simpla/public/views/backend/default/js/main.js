/**
 * 点击删除确认
 */
$(".delete_field_sure").click(function () {
    var field_name = $(this).attr('name');
    $(".delete_field").attr('name', field_name);
});
//删除字段
$(".delete_field").click(function () {
    var field_name = $(this).attr("name");
    $.ajax({
        url: '/admin/node/type/field/delete',
        type: 'POST',
        dataType: "html",
        data: {'field_name': field_name},
        success: function (data) {
            $("#modal_pop").removeClass("in").toggle();
            $(".modal-backdrop").remove();
            location.reload();
        }
    });
});


/**
 * 富文本输入框
 * @param {type} param
 */
//$('#summernote').summernote({
//    height: 200, //set editable area's height
//    codemirror: {// codemirror options
//        theme: 'monokai'
//    }
//});
tinymce.init({
    plugins: "image,code,media,preview,textcolor,link,searchreplace,table,advlist,fullscreen,insertdatetime,charmap", //图片,源代码,视频，预览，字体颜色，连接，搜索替换，表，字数统计
    autosave_interval: "20s", //自动保存
    language_url: '/themes/backend/default/plugs/tinymce/langs/zh_CN.js', //语言
    selector: "#TinyMCE"//代码中使用的是为TinyMCE的ID
});