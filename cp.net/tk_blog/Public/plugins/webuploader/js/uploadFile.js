/**
 * web图片上传 整理版
 */
$(function(){
    // 初始化Web Uploader
    var uploader = WebUploader.create({

        // 选完文件后，是否自动上传。
        auto: true,

        // swf文件路径
        swf: swfUrl,

        // 文件接收服务端。
        server: sendUrl,

        // 选择文件的按钮。可选。
        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
        pick: '#filePicker',

        // 只允许选择图片文件。
        accept: {
            title: 'Images',
            extensions: 'gif,jpg,jpeg,bmp,png',
            mimeTypes: 'image/*'
        }
    });

    // 文件上传成功 data才是服务器json的返回值
    uploader.on( 'uploadSuccess', function(file,data) {
        if (data.status) {
            //上传成功
            layer.msg(data.msg, {icon: 6,time: 2000});
            //给隐藏域赋值
            $("#hidden-imgs").attr('value',data.url);
            $("#uploader-wamp").find("h5").html(data.name);
            $("#show-imgs").attr('src',data.url);
        } else {
            //上传失败
            layer.msg(data.msg, {icon: 5,time: 2000});
        }
    });

    //删除图片
    $("#uploader-wamp").delegate(".del-img","click",function(){
        var str = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iMTcxIiBoZWlnaHQ9IjE4MCIgdmlld0JveD0iMCAwIDE3MSAxODAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzEwMCV4MTgwCkNyZWF0ZWQgd2l0aCBIb2xkZXIuanMgMi42LjAuCkxlYXJuIG1vcmUgYXQgaHR0cDovL2hvbGRlcmpzLmNvbQooYykgMjAxMi0yMDE1IEl2YW4gTWFsb3BpbnNreSAtIGh0dHA6Ly9pbXNreS5jbwotLT48ZGVmcz48c3R5bGUgdHlwZT0idGV4dC9jc3MiPjwhW0NEQVRBWyNob2xkZXJfMTU1OTEwZTNjNWMgdGV4dCB7IGZpbGw6I0FBQUFBQTtmb250LXdlaWdodDpib2xkO2ZvbnQtZmFtaWx5OkFyaWFsLCBIZWx2ZXRpY2EsIE9wZW4gU2Fucywgc2Fucy1zZXJpZiwgbW9ub3NwYWNlO2ZvbnQtc2l6ZToxMHB0IH0gXV0+PC9zdHlsZT48L2RlZnM+PGcgaWQ9ImhvbGRlcl8xNTU5MTBlM2M1YyI+PHJlY3Qgd2lkdGg9IjE3MSIgaGVpZ2h0PSIxODAiIGZpbGw9IiNFRUVFRUUiLz48Zz48dGV4dCB4PSI2MSIgeT0iOTQuNSI+MTcxeDE4MDwvdGV4dD48L2c+PC9nPjwvc3ZnPg==';
        var url = $("#show-imgs").attr('src');
        if (url) {
            $.post(delIMGURL,{'url' : url},function(data){
                if (data.status) {
                    layer.msg(data.msg, {icon: 6,time: 2000});
                    $("#uploader-wamp").find("h5").html('File Name');
                    $("#show-imgs").attr('src',str);
                } else {
                    layer.msg(data.msg, {icon: 5,time: 2000});
                }
            },"json");
        }
    });
});