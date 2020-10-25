<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ setting.site_title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes" /> 
    <meta name="apple-touch-fullscreen" content="yes" /> 
    <meta name="keywords" content="{{ setting.site_keywords }}">
    <meta name="description" content="{{ setting.site_description }}">
    <link rel="shortcut icon" href="/favicon.ico" />
    {{ stylesheet_link("css/pure-min.css") }}
    {{ stylesheet_link("css/waterNav.css") }}
    {{ stylesheet_link("css/index.css") }}
    {{ stylesheet_link("css/idangerous.swiper.css") }}
    {{ javascript_include("js/modernizr.js") }}
    <!--[if !IE]><!--> 
    <!--<![endif]-->
     
</head>
<body>
	<div id="header">
        {% include "common/header.volt"%}
    </div>
    <div id="content_farther">
        {{content()}}
    </div>
    <div id="footer" >
        {% include "common/footer.volt"%}
    </div>
    {{ javascript_include("js/jquery-1.10.1.min.js") }}
    {{ javascript_include("js/idangerous.swiper.js") }}
    {{ javascript_include("js/jquery.qrcode.min.js") }} 
    {{ javascript_include("js/json.js") }}
    {{ javascript_include("js/index.js") }}
    {{ javascript_include("js/waterNav.js") }}
    {{ javascript_include("js/fastclick.js") }}
</body>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=Knf9h80sArbh3FzW1WBqDuDs"></script>
<script type="text/javascript">
    //创建和初始化地图函数：
    function initMap(){
      createMap();//创建地图
      setMapEvent();//设置地图事件
      addMapControl();//向地图添加控件
      addMapOverlay();//向地图添加覆盖物
    }
    function createMap(){ 
      map = new BMap.Map("map"); 
      map.centerAndZoom(new BMap.Point(119.608682,39.941679),14);
    }
    function setMapEvent(){
      map.enableScrollWheelZoom();
      map.enableKeyboard();
      map.enableDragging();
      map.enableDoubleClickZoom()
    }
    function addClickHandler(target,window){
      target.addEventListener("click",function(){
        target.openInfoWindow(window);
      });
    }
    function addMapOverlay(){
      var markers = [
        {content:"",title:"天道棋院",imageOffset: {width:0,height:3},position:{lat:39.943664,lng:119.618069}}
      ];
      for(var index = 0; index < markers.length; index++ ){
        var point = new BMap.Point(markers[index].position.lng,markers[index].position.lat);
        var marker = new BMap.Marker(point,{icon:new BMap.Icon("http://api.map.baidu.com/lbsapi/createmap/images/icon.png",new BMap.Size(20,25),{
          imageOffset: new BMap.Size(markers[index].imageOffset.width,markers[index].imageOffset.height)
        })});
        var label = new BMap.Label(markers[index].title,{offset: new BMap.Size(25,5)});
        var opts = {
          width: 200,
          title: markers[index].title,
          enableMessage: false
        };
        var infoWindow = new BMap.InfoWindow(markers[index].content,opts);
        marker.setLabel(label);
        addClickHandler(marker,infoWindow);
        map.addOverlay(marker);
      };
    }
    //向地图添加控件
    function addMapControl(){
      var scaleControl = new BMap.ScaleControl({anchor:BMAP_ANCHOR_BOTTOM_LEFT});
      scaleControl.setUnit(BMAP_UNIT_IMPERIAL);
      map.addControl(scaleControl);
      var navControl = new BMap.NavigationControl({anchor:BMAP_ANCHOR_TOP_LEFT,type:3});
      map.addControl(navControl);
      var overviewControl = new BMap.OverviewMapControl({anchor:BMAP_ANCHOR_BOTTOM_RIGHT,isOpen:false});
      map.addControl(overviewControl);
    }
    var map;
      initMap();
</script>

</html>

