define(['AMapLoader'], function(AMapLoader){

    var action = {};
    
    action.attach = function(map, lat, lng, infoTip){
	
        var marker = new AMap.Marker({
	    position: new AMap.LngLat(lng, lat)
	});
        
        // 设置鼠标划过点标记显示的文字提示
        if(infoTip){
            marker.setTitle(infoTip);

            // 设置label标签
            marker.setLabel({//label的父div默认蓝框白底右下角显示，样式className为：amap-marker-label
                //offset:new AMap.Pixel(50,50),//修改父div相对于maker的位置
                content: infoTip
            });
        }
        
        marker.setMap(map);
    };
    
    return action;
    
});