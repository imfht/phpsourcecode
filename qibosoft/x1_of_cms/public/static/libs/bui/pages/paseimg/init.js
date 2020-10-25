mod_class.paseimg = {
    init: function(res) {		
        if (in_pc == true) {
			$("#btn_paseimg").click(function(){
				layer.alert("帮助提示：<br>在任何界面，同时按住“Ctrl、Alt、A”这三个键截图，点击“完成”后，回到聊天输入框Ctr+V粘贴即可实现截图上传",{title:false,btn:'我知道了'});
			});
            $(function paseImg() {
                var imgReader = function(item) {
                    var blob = item.getAsFile(),
                        reader = new FileReader();
                    reader.onloadend = function(e) {
                        $.ajax({
                            url: '/index.php/index/attachment/upload/dir/msgpic/from/base64/module/bbs.html',
                            type: 'POST',
                            data: {
                                imgBase64: e.target.result
                            },
                            success: function(res) {
                                layer.msg(res.info);
                                if (res.code == 1) {
                                    var url = res.path;
                                    if (url.indexOf('://') == -1 && url.indexOf('/public/') == -1) {
                                        url = (typeof(web_url) != 'undefined' ? web_url : '') + '/public/' + url;
                                    }
                                    var old = $('#input_box').val();
                                    $('#input_box').val(old + "<img src='" + url + "' class='big' />");
                                }
                            }
                        })
                    };
                    reader.readAsDataURL(blob);
                };
                document.getElementById("input_box").addEventListener("paste", function(e) {
                    var clipboardData = e.clipboardData,
                        i = 0,
                        items,
                        item,
                        types;
                    if (clipboardData) {
                        items = clipboardData.items;
                        if (!items) {
                            return;
                        }
                        item = items[0];
                        types = clipboardData.types || [];
                        for (; i < types.length; i++) {
                            if (types[i] === 'Files') {
                                item = items[i];
                                break;
                            }
                        }
                        if (item && item.kind === 'file' && item.type.match(/^image\//i)) {
                            imgReader(item);
                        }
                    }
                });
            });
        }else{
			console.log('QQ截图只能在PC端使用!');
		}
    },
}
 