Namespace = new Object();
Namespace.register = function(fullNS)
{
    var nsArray = fullNS.split('.');
    var sEval = "";
    var sNS = "";
    var count = nsArray.length;
    for (var i = 0; i < count; i++)
    {
        if (i != 0) sNS += ".";
        sNS += nsArray[i];
        if (i<count-1) {
            sEval += "if (typeof(" + sNS + ") == 'undefined') " + sNS + " = new Object();";
        }else{
            sEval += "delete " + sNS + ";" + sNS + " = new Object();";
        };
    }
    if (sEval != "") eval(sEval);
};
Namespace.register("EBCMS.DATA");
Namespace.register("EBCMS.CORE");
Namespace.register("EBCMS.MYFUN");
Namespace.register("EBCMS.MSG");
Namespace.register("EBCMS.FN");
Namespace.register("EBCMS.TEMP");
$(function() {
    EBCMS.CORE = {
        myfun:function(name, fun, cover){
            if (typeof name == 'string' && typeof fun == 'function') {
                if (cover == true || typeof EBCMS.MYFUN[name] != 'function') {
                    EBCMS.MYFUN[name] = fun;
                }
            }
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            var x = XMLHttpRequest.responseText.replace('<script>','<pre style="display:none;">').replace('</script>','</pre>');
            x = '<frame name="error"><div style="max-height:600px;max-width:900px;overflow:auto;">'+(x||'系统反馈异常，请稍后再试！')+'</div></frame>';
            art.dialog({
                title:'系统发生错误！',
                lock: true,
                fixed: true,
                drag: false,
                background: '#600',
                opacity: 0.87,
                content: x
            });
        },
        ajax:function(p){
            if (1 == EBCMS.TEMP.submit_token) {
                EBCMS.MSG.alert('提交得太快啦！');
                return;
            };
            EBCMS.TEMP.submit_token = 1;
            var x = artDialog.tips('处理中...',20);
            $.ajax({
                url: p.url,
                type: p.method||'POST',
                dataType: 'JSON',
                data: p.data,
                success:function(res){
                    EBCMS.TEMP.submit_token = 0;
                    if (!res.code) {
                        x.close();
                        EBCMS.MSG.alert(res.msg);
                    }else{
                        artDialog.tips(res.msg||'处理完毕...',0.3);
                        setTimeout(function() {
                            history.go(0);
                        }, 300);
                    }
                },
                error:function(XMLHttpRequest, textStatus, errorThrown){
                    EBCMS.TEMP.submit_token = 0;
                    x.close();
                    EBCMS.CORE.error(XMLHttpRequest, textStatus, errorThrown);
                }
            });
        },
        submit:function(p){
            if (1 == EBCMS.TEMP.submit_token) {
                EBCMS.MSG.alert('提交得太快啦！');
                return;
            }
            EBCMS.TEMP.submit_token = 1;
            var x = artDialog.tips('处理中...',20);
            var data;
            if (p.form) {
                data = $('#'+p.form).serialize();
            }else if(p.queryParams){
                data = p.queryParams;
            };
            $.ajax({
                url: p.url,
                type: 'POST',
                dataType: 'JSON',
                data: data,
                success:function(res){
                    EBCMS.TEMP.submit_token = 0;
                    if (!res.code) {
                        x.close();
                        EBCMS.MSG.alert(res.msg);
                    }else if (typeof p.success == 'function') {
                        x.close();
                        p.success(res);
                    }else{
                        artDialog.tips(res.msg||'处理完毕...',1);
                        setTimeout(function() {
                            history.go(-1);
                        }, 1000);
                    }
                },
                error:function(XMLHttpRequest, textStatus, errorThrown){
                    EBCMS.TEMP.submit_token = 0;
                    x.close();
                    if (p.error) {
                        p.error(XMLHttpRequest, textStatus, errorThrown);
                    }else{
                        EBCMS.CORE.error(XMLHttpRequest, textStatus, errorThrown);
                    }
                }
            });
        },
    };
    EBCMS.FN = {
        str_repeat:function (n, str) {
            var result='';
            for (var i = 0; i < n; i++) {
                result+=str;
            };
            return result;
        },
        in_array:function(search,arr){ 
            // 遍历是否在数组中 
            var count = arr.length;
            for(var i=0,k = count;i<k;i++){ 
                if(search==arr[i]){ 
                    return true; 
                } 
            }
            // 如果不在数组中就会返回false 
            return false; 
        },
        here_doc:function(func){
            return func.toString().split(/\n/).slice(1, -1).join('\n');
        },
        htmlspecialchars:function(str){
            if (!str) {return};
            str = str.replace(/&/g, '&amp;');
            str = str.replace(/</g, '&lt;');
            str = str.replace(/>/g, '&gt;');
            str = str.replace(/"/g, '&quot;');
            str = str.replace(/'/g, '&#039;');
            return str;
        },
        htmlspecialchars_decode:function(str){
            if (!str) {return};
            str = str.replace(/&lt;/g, '<');
            str = str.replace(/&gt;/g, '>');
            str = str.replace(/&quot;/g, '"');
            str = str.replace(/&#039;/g, "'");
            str = str.replace(/&amp;/g, '&');
            return str;
        },
        random_str:function(len,chars){
            var chars = chars||'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            chars = chars.split("");
            var res = "";
            for(var i = 0; i < len ; i ++) {
                var id = Math.ceil(Math.random()*(chars.length-1));
                res += chars[id];
            }
            return res;
        },
        random_num:function(num,from,to){
            var arr=[];
            for(var i=from;i<=to;i++)
                arr.push(i);
            arr.sort(function(){
                return 0.5-Math.random();
            });
            arr.length=num;
            return arr;
        },
        // 获取选中项id
        getCheckedId:function(selecter){
            var ids = [];
            $(selecter).each(function(){ 
                ids.push($(this).attr('value'));
            });
            return ids.join(',');
        },
        // 反选
        inverse:function(selecter) {
            $(selecter).each(function(){ 
                $(this).click();
            });
        },
        loadCSS:function(url){
            var cssLink = document.createElement("link");
            cssLink.rel = "stylesheet";
            cssLink.rev = "stylesheet";
            cssLink.type = "text/css";
            cssLink.media = "screen";
            cssLink.href = url;
            document.getElementsByTagName("head")[0].appendChild(cssLink);
        }
    };
    EBCMS.MSG = {
        notice:function(info){
            art.dialog.notice({
                title: '操作提示',
                width: 220,
                content: info,
                icon: 'face-smile',
                time: 2
            });
        },
        show:function(content,title){
            art.dialog({
                title:title||'内容展示',
                lock: false,
                fixed: false,
                drag: true,
                content: content,
            });
        },
        tips:function(info,time){
            time = time||1;
            artDialog.tips(info,time);
        },
        alert:function(info){
            art.dialog.alert(info);
        },
        confirm:function(info,yes,no){
            art.dialog.confirm(info, function () {
                if (typeof yes == 'function') {
                    yes();
                };
            }, function () {
                if (typeof no == 'function') {
                    no();
                };
            });
            return false;
        }
    };
});