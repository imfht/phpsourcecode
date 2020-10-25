<style type="text/css">
#allmap {width: 100%;height: 450px;overflow: hidden;margin:0;}
#l-map{height:<?php echo $data['height']?$data['height']:'450px';?>;width:<?php echo $data['width']?$data['width']:'100%';?>;float:left;border-right:2px solid #bcbcbc;}
</style>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=1.3"></script>
<div id="l-map"></div>
<script type="text/javascript">
var map = new BMap.Map("l-map");
var point = new BMap.Point(<?php echo empty($data['lng'])?'116.404':$data['lng'] ?>,<?php echo empty($data['lat'])?'39.915':$data['lat'] ?>);
map.centerAndZoom(point, 12);
var marker = new BMap.Marker(point);  // 创建标注
map.addOverlay(marker);              // 将标注添加到地图中
map.addControl(new BMap.NavigationControl());  //添加默认缩放平移控件
map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_TOP_RIGHT, type: BMAP_NAVIGATION_CONTROL_SMALL}));  //右上角，仅包含平移和缩放按钮
map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_BOTTOM_LEFT, type: BMAP_NAVIGATION_CONTROL_PAN}));  //左下角，仅包含平移按钮
map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_BOTTOM_RIGHT, type: BMAP_NAVIGATION_CONTROL_ZOOM}));  //右下角，仅包含缩放按钮
map.enableScrollWheelZoom();    //启用滚轮放大缩小，默认禁用
map.centerAndZoom(point, 15);
var opts = {
  width : 250,     // 信息窗口宽度
  height: 100,     // 信息窗口高度
  title : '<b>公司名称：</b><?php echo $data['title']?><br><b>联系电话：</b><?php echo $data['phone']?><br><b>公司地址：</b><?php echo $data['address']?>'  // 信息窗口标题
}
var infoWindow = new BMap.InfoWindow('', opts);  // 创建信息窗口对象
map.openInfoWindow(infoWindow,point); //开启信息窗口
marker.addEventListener("click", function(){          
   this.openInfoWindow(infoWindow);
   //图片加载完毕重绘infowindow
});
</script>
<?php echo stripslashes($data['content']); ?>