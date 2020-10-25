var api = {
    del:function(url,layer,trObj){
        $.ajax({
            url:url,
            success:function(data){
                if(data.code ===1){
                    layer.msg(data.msg,{icon:1});
                    trObj.del();
                }else{
                    layer.msg(data.msg,{icon:5});
                }
            }
        });
    },
    update:function(url,data,alert){
        $.ajax({
            url:url,
            data:data,
            type:'post',
            success:function(data){
                var icon = data.code ===1 ? 1 : 5;
                if(alert){
                    layer.msg(data.msg,{icon:icon});
                }
            }
        });
    },
    post:function(url,data){
        $.ajax({
            url:url,
            data:data,
            type:'post',
            success:function(data){
                var icon = data.code ===1 ? 1 : 5;
                layer.msg(data.msg,{icon:icon});
                if(data.code === 1){
                    setTimeout(function(){
                        location.href = data.url;
                    },data.wait * 1000)
                }
            }
        });
    }
}

function openPage(url, appId, appname, refresh){
    window.parent.openPage(url, appId, appname, refresh);
}
