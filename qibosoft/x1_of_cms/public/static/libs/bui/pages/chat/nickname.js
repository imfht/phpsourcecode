loader.define(function() {
	var pageview = {};
	var id,uid='';

	pageview.init = function () {
		bui.input({
            id: ".user-input",
            callback: function (e) {
                // 清空数据
                this.empty();
            }
        });

		router.$("#postBtn").click(function(){
			$.get("/index.php/qun/wxapp.member/edit_nickname.html?id="+id+"&uid="+uid+"&nickname="+router.$("#nickname").val(),function(res){
				if(res.code==0){
					bui.alert("修改成功");
				}else{
					bui.alert(res.msg);
				}
			});
		});
	}

	var getParams = bui.getPageParams();
    getParams.done(function(result){		
		if(result.uid!=undefined){
			uid = result.uid;
		}
		if(result.id!=undefined){
			id = result.id;
			$.get("/index.php/qun/wxapp.member/get_byuid.html?id="+id+"&uid="+uid,function(res){
				if(res.code==0){
					router.$("#nickname").val(res.data.nickname);
				}
			});
		}
    })

	pageview.init();
})