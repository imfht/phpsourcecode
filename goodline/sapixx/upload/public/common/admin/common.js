//全选
function CheckAll(event){
    var checked = event.checked;
    $(".table tbody input[type = 'checkbox']").each(function(i){
        if(!$(this).prop("disabled")){
            $(this).prop("checked",checked)
        }
    });
}
//快捷排序
function sort(url) {
    $("input[name='sort']").change(function () {
        var param = {
            id : $(this).attr('id'),
            sort : $(this).val()
        }
        $.post(url,param,function (data) {
            if(data.code == 200){
                layer.msg(data.msg,{time: 300}, function () { window.location.reload();});
            }else{
                layer.msg(data.msg);
            }
        })
    });
}
//快捷工具栏弹窗
function openwin(url){
    parent.layer.open({type: 2,maxmin:true,area:['60%','70%'],content: url,cancel:function (index,layero) {
        parent.layer.close(index);
    }, success: function (layero, index) {
        parent.layer.getChildFrame('body',index).addClass(window.name);
    }});
}
/*
** randomWord 产生任意长度随机字母数字组合
** randomFlag-是否任意长度 min-任意长度最小位[固定位数] max-任意长度最大位
** xuanfeng 2014-08-28
*/
function randomWord(randomFlag, min, max){
    var str = "",range = min,arr = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    // 随机产生
    if(randomFlag){
        range = Math.round(Math.random() * (max-min)) + min;
    }
    for(var i=0; i<range; i++){
        pos = Math.round(Math.random() * (arr.length-1));
        str += arr[pos];
    }
    return str;
}
//批量操作
function tools_submit(obj){
    var ids = "";
    $("input:checkbox[name='ids[]']:checked").each(function() {
       ids += $(this).val() + ",";
    });
    parent.layer.confirm(obj.msg,{icon:3},function(index){
        $.post(obj.action,{ids:ids},function(data) {
            if (data.code == "302") {
                $.isEmptyObject(data.data.url) ? window.location.reload(): window.location.replace(data.data.url);
            }else if(data.code == "200"){
                parent.layer.alert(data.msg,{icon:1,closeBtn:0},function(index){
                    $.isEmptyObject(data.data.url) ? window.location.reload(): window.location.replace(data.data.url);
                    parent.layer.close(index);
                });
            } else {
                parent.layer.alert(data.msg,{icon:5});
            }
        },"json");
        parent.layer.close(index);
    });
}
//添加图片
function setImg(show_src) {
    if ($("#imgbox img[src='" + show_src + "']").get(0)) {
        parent.layer.msg("图片已经添加，请不要重复添加！");
    } else {
        $("#imgbox").append('<div class="box-view fn-left"><input type="hidden" name="imgs[]" value="' + show_src + '" /><img src="' + show_src + '"  onclick="selectImg(this)"><div class="opera"><a class="imgbox-left" href="javascript:;"><i class="iconfont icon-arrowleft"></i></a><a class="imgbox-right" href="javascript:;"><i class="iconfont icon-arrowright"></i></a><a class="imgbox-link" href="javascript:;" onclick="linkImg(this)"><i class="iconfont icon-search_icon"></i></a><a class="imgbox-close" href="javascript:;" onclick="delImg(this)"><i class="iconfont icon-close_icon"></i></a></div></div>');
        if ($("#img_index").val() == "") {
            $("#img_index").val(show_src);
        }
        bindEvent();
    }
}
//删除添加的图片
function delImg(id) {
    $(id).parent().parent().remove();
}
//显示图片URL
function linkImg(id) {
    var src = $(id).parent().parent().find('img').attr('src');
    layer.photos({photos:{"status":1,"title": "图片预览","id":8,"start": 0,"data":[{"alt":"layer","pid": 109,"src":src}]},anim:5});
}
//放大图片URL
function bigImg(src) {
    layer.photos({photos:{"status":1,"title": "图片预览","id":8,"start": 0,"data":[{"alt":"layer","pid": 109,"src":src}]},anim:5});
}
//操作左右按钮事件绑定
function bindEvent() {
    $(".imgbox-right").off();
    $(".imgbox-left").off();
    $(".imgbox-right").on("click", function () {
        var current_tr = $(this).parent().parent();
        current_tr.insertAfter(current_tr.next());
    });
    $(".imgbox-left").on("click", function () {
        var current_tr = $(this).parent().parent();
        if (current_tr.prev().html() != null) current_tr.insertBefore(current_tr.prev());
    });
}
//设置默认图片
function selectImg(id) {
    var img = $(id).attr('src');
    $(".box-view").removeClass("current");
    $(id).parent().addClass("current");
    $("#img_index").val(img);
}
//JS图片转换成B64
function imgtob64(imgurl) {
    var img = new Image();
    img.src = imgurl;
    var canvas = document.createElement("canvas");
    canvas.width  = img.width;
    canvas.height = img.height;
    var ctx = canvas.getContext("2d");
    ctx.drawImage(img, 0, 0, img.width, img.height);
    var dataURL = canvas.toDataURL("image/png");
    return dataURL
}