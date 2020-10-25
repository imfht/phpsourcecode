loader.define(function(require,exports,module) {
    
	var pageview = {},      // 页面的模块, 包含( init,bind )
			uiPullrefresh,      // 消息,电话公用的下拉刷新控件
			scroll_get=true,  // 我的设备折叠菜单
			showtype='qun',		//显示圈子还是用户
			mid=0,				//圈子模型筛选
			uiAccordionFriend;  // 我的好友折叠菜单
    
	var map_x = 0;
	var map_y = 0;
	var page = 1;

	store.compile(".bui-bar");	//重新加载全局变量数据

    /**
     * [init 页面初始化]
     * @return {[type]} [description]
     */
    pageview.init = function () {
        
        // 页面动态加载,需要重新初始化
        bui.init({
            id: "#tab-contact"
        })
        var mainHeight = $(window).height() - $("#tab-contact-header").height()- $("#tabDynamicNav").height();



        // 初始化好友折叠菜单
       // uiAccordionFriend = bui.accordion({
        //    id:"qun_zi"
        //});

		//showFirst显示第一个
        //uiAccordionFriend.showFirst();

		loader.import(["/public/static/libs/bui/js/map.js"],function(){
			get_gps_location(function(x,y){
				map_x = x;
				map_y = y;
				$.get("/member.php/member/wxapp.user/edit_map.html?point="+x+","+y);
				$.get("/index.php/index/wxapp.map/get_address.html?xy="+x+","+y,function(res){
					if(res.code==0){
						router.$(".show_address .span1").html( "当前位置:"+res.data.address );
					}
				});
				page = 1;
				$("#near_qunzi").html('');
				showMoreList(x,y);				
			});
		});   
		
		weixin_share({
				title:'这是分享标题',
				about:'这是分享介绍',
				picurl:'https://x1.php168.com/public/static/qibo/logo.png',
				url:window.location.href,
			});
    }

	
	var that = router.$("#contactScroll");
	that.parent().scroll(function () {
			var h = that.height()-that.parent().height()-that.parent().scrollTop();
			if( h<300 && scroll_get==true){
				scroll_get = false;
				console.log(h);
				layer.msg('内容加截中,请稍候',{time:1500});
				showMoreList(map_x,map_y);
			}
	});

	
	//var j = 0;

	//根据当前坐标位置去数据库按位置远近排序读取
	function showMoreList(longitude,latitude){
		var url;
		if(showtype == 'user'){
			url = "/index.php/index/wxapp.member/get_near.html?rows=30";
		}else{
			url = "/index.php/qun/wxapp.near/index.html?rows=30&mid="+mid;
		}
		$.get(url+"&point=" + longitude + ',' + latitude + "&page=" + page + '&' + Math.random(),function(res){
			var d ='';
		   if(res.code==0){
			   if(res.data.length==0){			   
				   layer.msg("已经显示完了！",{time:500});
			   }else{
					page++;
				    res.data.forEach(function(rs){
						//j++;
						var toid;
						if(showtype == 'user'){
							toid = rs.uid;
						}else{
							toid = -rs.id;
						}
						d += `
							<li class="bui-btn bui-box">
                                    <a href="${rs.url}" class="iframe" target="_blank"><img class="ring ring-pc" src="${rs.picurl}" onerror="this.src='/public/static/images/nopic.png'"/></a>
                                <div class="span1 a" href="/public/static/libs/bui/pages/chat/chat.html?uid=${toid}">
                                    <h3 class="item-title">
                                        ${rs.title}
                                    </h3>
                                    <p class="item-text bui-text-hide" data-map="${rs.map_x},${rs.map_y}">${rs.content}</p>
                                </div>
                            </li>	
						`;
				   });
				   $("#near_qunzi").append(d);
				   $("#list_content .show_address .time").html(res.paginate.total);
				   
				   show_distance(longitude,latitude);
				   layer.closeAll();
				   scroll_get = true;
			   }			  
		   }
		});
	}


	


	//计算距离当前位置的公里数
	function show_distance(map_lat,map_lon){
		$('#near_qunzi').children('li').each(function(){
			var this_map=$(this).find('.item-text').data('map');
			var thismap=this_map.split(",");
			var this_lon = thismap[0];
			var this_lat = thismap[1];
			var show_word='距离';
			if(this_lon!=0 &&this_lat!=0){
				var show_map_str = GPS.distance(map_lat,map_lon,this_lon,this_lat);
				console.log(map_lat+"="+map_lon+"="+this_lon+"="+this_lat);
				var kilometres = Math.floor(show_map_str/1000);  
				var metres=Math.floor(show_map_str%1000);				
				if(kilometres>0){
					show_word+='<font style="color:red;">'+kilometres+'</font>公里';
				}
				show_word += isNaN(metres)?'未知':'<font style="color:red;">'+metres+'</font>米';
			}else{
				show_word = '未标定位';
			}
			
			$(this).find('.item-text').html(show_word);
		});	
	}


	

    // 下拉刷新以后执行数据请求
    function getData () {

        bui.ajax({
            url : "/public/static/libs/bui/userlist.json",
            data: {
                pageindex:1,
                pagesize:4
            }
        }).done(function(res) {

            //还原刷新前状态
            uiPullrefresh.reverse();

        }).fail(function (res) {
            //请求失败变成点击刷新
            uiPullrefresh.fail();
        })
    }

	var getParams = bui.getPageParams();
    getParams.done(function(result){
		layer.msg('数据加载中,请稍候...',{time:5000});
		console.log(result);
		if(result.type!=undefined && result.type=='user'){
			showtype = 'user';
		}else if(result.mid!=undefined){
			showtype = 'qun';
			mid = result.mid;
		}
    })

    // 控件初始化
    pageview.init();
    
    // 输出模块
    module.exports = pageview;
})