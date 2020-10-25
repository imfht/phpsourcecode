define(['AMapLoader'], function(AMapLoader){
    
    var action = {};
    
    action.isAmapLoaded = function(){
	return typeof AMap.LngLat != "undefined";
    };
    
    
    action.createMap = function(domid){
	
	if(!action.isAmapLoaded()){
	    alert("地图载入错误，请刷新页面重试！");
	    return ;
	}
        
        var map = new AMap.Map(domid,{
            resizeEnable: true,
            //二维地图显示视口
            view: new AMap.View2D({
                center: new AMap.LngLat(120.023768, 30.279796),    //地图中心点
                zoom:3, //地图显示的缩放级别
                resizeEnable: true
            })
        });
        
        //在地图中添加ToolBar插件
        map.plugin(["AMap.ToolBar"],function(){        
            var toolBar = new AMap.ToolBar();
            map.addControl(toolBar);        
        });
        
        //加载比例尺插件
        map.plugin(["AMap.Scale"], function(){
            var scale = new AMap.Scale();
            map.addControl(scale);
        });
        
        return map;
        
    };
    
    return action;
    
});