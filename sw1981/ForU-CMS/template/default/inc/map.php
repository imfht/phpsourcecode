<!--引用百度地图API-->
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=<?php echo MAP_KEY;?>"></script>
<div id="Baidu-map" class="gmap map_960"></div>
<script type="text/javascript">
  //创建和初始化地图函数：
  function initMap(){
  createMap();//创建地图
  setMapEvent();//设置地图事件
  // addMapControl();//向地图添加控件
  addMapOverlay();//向地图添加覆盖物
  }
  function createMap(){
  map = new BMap.Map("Baidu-map");
  map.centerAndZoom(new BMap.Point(<?php echo MAP_LNG;?>,<?php echo MAP_LAT;?>),<?php echo MAP_SCALE;?>);
  }
  function setMapEvent(){
  // map.enableScrollWheelZoom();
  // map.enableKeyboard();
  map.enableDragging();
  // map.enableDoubleClickZoom()
  }
  function addClickHandler(target,window){
  target.addEventListener("click",function(){
    target.openInfoWindow(window);
  });
  }
  function addMapOverlay(){
  var markers = [
    {content:"<?php echo MARK_CONTENT;?>",title:"<?php echo MARK_TITLE;?>",imageOffset: {width:-46,height:-21},position:{lat:<?php echo MAP_LAT;?>,lng:<?php echo MAP_LNG;?>}}
  ];
  for(var index = 0; index < markers.length; index++ ){
    var point = new BMap.Point(markers[index].position.lng,markers[index].position.lat);
    var marker = new BMap.Marker(point,{icon:new BMap.Icon("http://api.map.baidu.com/lbsapi/createmap/images/icon.png",new BMap.Size(20,25),{
    imageOffset: new BMap.Size(markers[index].imageOffset.width,markers[index].imageOffset.height)
    })});
    var label = new BMap.Label(markers[index].title,{offset: new BMap.Size(25,5)});
    var opts = {
    width: <?php echo LABEL_WIDTH;?>,
    title: markers[index].title,
    enableMessage: false
    };
    var infoWindow = new BMap.InfoWindow(markers[index].content,opts);
    // marker.setLabel(label);
    // addClickHandler(marker,infoWindow);
    // map.openInfoWindow(infoWindow, map.getCenter());
    map.addOverlay(marker);
  };
  }
  //向地图添加控件
  function addMapControl(){
  var scaleControl = new BMap.ScaleControl({anchor:BMAP_ANCHOR_BOTTOM_LEFT});
  scaleControl.setUnit(BMAP_UNIT_IMPERIAL);
  map.addControl(scaleControl);
  var navControl = new BMap.NavigationControl({anchor:BMAP_ANCHOR_TOP_LEFT,type:BMAP_NAVIGATION_CONTROL_LARGE});
  map.addControl(navControl);
  }
  var map;
  initMap();
</script>
