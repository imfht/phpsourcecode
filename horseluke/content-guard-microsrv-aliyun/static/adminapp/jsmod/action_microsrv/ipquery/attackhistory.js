define(['jquery', 'webjstool', 'util/mappoiAttacher', 'util/mapinit'], function($, webjstool, mappoiAttacher, mapinit){
    
    var action = {};
    
    action.run = function(){
        if(typeof window.actionOpenMapFlag !== "undefined"){
            action.runMapInit();
        }
    };
    
    action.runMapInit = function(){
        var cfg = window.actionOpenMapFlag;
        if($('#' + cfg.mapId).length < 1){
        return ;
        }
        
        $('#' + cfg.mapId).html("");
        
        
        if(mapinit.isAmapLoaded()){
            action.runReallyRunMap();
        }else{
            setTimeout(action.runReallyRunMap, 2500);
        }
        
    };
    
    var map;
    action.runReallyRunMap = function(){
        if(typeof AMap.LngLat == "undefined"){
            action.addTipMsg("地图载入失败，请刷新页面重试。");
            return ;
        }
        
        map = mapinit.createMap(window.actionOpenMapFlag.mapId);
        
        var citys = [];
        var countrys = [];
        $(window.actionOpenMapFlag.attackdstGeoStat.city_list).each(function(idx, e){
            citys.push(e.location);
        });
        
        $(window.actionOpenMapFlag.attackdstGeoStat.country_list).each(function(idx, e){
            countrys.push(e.location);
        });
        
        if(citys.length > 0){
            action.privateAddCityInMap(citys);
        }
        
        if(countrys.length > 0){
            action.privateAddCountryInMap(countrys);
        }
        
    };
    
    action.privateAddCityInMap = function(citys){
        $.ajax({
           url: webjstool.cfg.get("url") + '?r=geo/ajax/searchCity',
            method: 'POST',
            data: {
                'citys': citys.join(','),
            },
            success: function(rst){
                if(rst.code != 0){
                    alert(rst.err);
                    return ;
                }
                
                action.addTipMsg("注意：此处标记的城市只是一个示意，并非说攻击源真的是来自地图上的这个点。");
                
                var result = rst.rst;
                if(result.miss.length > 0){
                    action.addTipMsg("以下城市无法在地图显示：" + result.miss.join("，") + "。");
                }
                
                for(var i=0;i<result.citys.length;i++){
                    $(window.actionOpenMapFlag.attackdstGeoStat.city_list).each(function(idx, e){
                        if(e.location == result.citys[i].city){
                            mappoiAttacher.attach(map, result.citys[i].lat, result.citys[i].lng, e.location + "，被攻击计数:" + e.count);
                        }
                    });
                }
                
            },
            error: function(result){
        	action.addTipMsg("城市搜索出错。");
            }
        });
    };
    

    action.privateAddCountryInMap = function(countrys){
        $.ajax({
            url: webjstool.cfg.get("url") + '?r=geo/ajax/searchcountry',
             method: 'POST',
             data: {
                 'countrys': countrys.join(','),
             },
             success: function(rst){
                 if(rst.code != 0){
                     alert(rst.err);
                     return ;
                 }
                 
                 action.addTipMsg("注意：此处标记的国家只是一个示意，并非说攻击源真的是来自这个国家的这个城市。");
                 
                 var result = rst.rst;
                 if(result.miss.length > 0){
                     action.addTipMsg("以下国家无法在地图显示：" + result.miss.join("，") + "。");
                 }
                 
                 for(var i=0;i<result.countrys.length;i++){
                     $(window.actionOpenMapFlag.attackdstGeoStat.country_list).each(function(idx, e){
                         if(e.location == result.countrys[i].country_zh_cn){
                             if(typeof result.countrys[i].latitude != 'undefined'){
                        	 mappoiAttacher.attach(map, result.countrys[i].latitude, result.countrys[i].longitude, e.location + "，被攻击计数:" + e.count);
                             }else{
                        	 action.addTipMsg(e.location + "没有坐标，无法显示");
                             }
                         }
                     });
                 }
                 
             },
             error: function(result){
        	 action.addTipMsg("国家搜索出错。");
             }
         });
    };
    
    action.addTipMsg = function(msg){
	var myDate = new Date();
	
	var dom = $("<div></div>");
	dom.text(myDate.toLocaleString() + ": " + msg);
	$("#tipsBlock").append(dom);
	$("#tipsBlock").show();
	
    };
    
    return action;
    
});