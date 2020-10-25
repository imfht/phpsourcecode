<style type="text/css">
.c-box{ float:left; width:100%;}
.col2-lbox { width:20%; }
.col2-rbox { width:78.9%; }
#allmap {width: 90%;height: 450px;overflow: hidden;margin:0;}
#l-map{height:450px;width:90%;float:left;border-right:2px solid #bcbcbc;}
</style>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=1.3"></script>

<div class="c-box">
  <div class="col2-lbox">
    <table border='0'  cellspacing='0' cellpadding='0' style='width:100%;'  class='tableBorder' >
      <tr>
        <td class='tableHeading' > 操作菜单 </td>
      </tr>
      <tr>
        <td class='tableCellTwo' ><span class='defaultBold'><a href="javascript:;" style="text-decoration:none;">商家地图</a></span> </td>
      </tr>
    </table>
    <table border='0'  cellspacing='0' cellpadding='0' style='width:100%;'  class='tableBorder' >
      <tr>
        <td class='tableHeading' > 公司位置 </td>
      </tr>
      <tr>
        <td class='tableCellTwo' ><span class='defaultBold'>
          <div id="r-result">
              搜索:<input type="text" id="suggestId" size="20" value="百度" style="width:150px;" /></div><div id="searchResultPanel" style=" display:none">
          </div>
          </span></td>
      </tr>
    </table>
  </div>
  <div class="col2-rbox">
    <div class='box' >
      <form name="form1" method="post" action="./index.php?a=edit&p=<?php echo $request['p'] ?>">
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="4">
          <tr>
            <td width="60">公司名称</td>
            <td width="90%"><input type="text" name="title" id="title" class="txt" style="width:60%" value="<?php echo $mapshow->title ?>" />
              <input name="submit" type="submit" value=" 保 存 " class="savebt" style="float:right;"/></td>
          </tr>
          <tr>
            <td width="60">联系电话</td>
            <td width="90%"><input type="text" name="phone" id="phone" class="txt" style="width:60%" value="<?php echo $mapshow->phone ?>" /></td>
          </tr>
          <tr>
            <td width="60">公司地址</td>
            <td width="90%"><input type="text" name="address" id="address" class="txt" style="width:60%" value="<?php echo $mapshow->address ?>" /></td>
          </tr>
          <tr>
            <td colspan="2" style="padding:0 11px 0 0;"><div id="l-map"></div>
            <script type="text/javascript" language="javascript">
			var map = new BMap.Map("l-map");
			var point = new BMap.Point(<?php echo empty($mapshow->lng)?'116.404':$mapshow->lng ?>,<?php echo empty($mapshow->lat)?'39.915':$mapshow->lat ?>);
			map.centerAndZoom(point, 15);
			
			map.addControl(new BMap.NavigationControl());  //添加默认缩放平移控件
			map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_TOP_RIGHT, type: BMAP_NAVIGATION_CONTROL_SMALL}));  //右上角，仅包含平移和缩放按钮
			map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_BOTTOM_LEFT, type: BMAP_NAVIGATION_CONTROL_PAN}));  //左下角，仅包含平移按钮
			map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_BOTTOM_RIGHT, type: BMAP_NAVIGATION_CONTROL_ZOOM}));  //右下角，仅包含缩放按钮
				
			map.enableScrollWheelZoom();    //启用滚轮放大缩小，默认禁用
 		    map.enableContinuousZoom();    //启用地图惯性拖拽，默认禁用
			
		    var marker = new BMap.Marker(point);  // 创建标注
			map.addOverlay(marker);              // 将标注添加到地图中
			marker.enableDragging();    //可拖拽
   
			marker.addEventListener("dragend",function(e){
				map.setCenter(new BMap.Point(e.point.lng,e.point.lat));   //设置地图中心点。
				$('#lngs').val(e.point.lng); 
				$('#lats').val(e.point.lat);   
			});
			
			var ac = new BMap.Autocomplete(    //建立一个自动完成的对象
				{"input" : "suggestId"
				,"location" : map
			});
			function G(id) {
				return document.getElementById(id);
			}
			ac.addEventListener("onhighlight", function(e) {  //鼠标放在下拉列表上的事件
			var str = "";
				var _value = e.fromitem.value;
				var value = "";
				if (e.fromitem.index > -1) {
					value = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
				}    
				str = "FromItem<br />index = " + e.fromitem.index + "<br />value = " + value;
				
				value = "";
				if (e.toitem.index > -1) {
					_value = e.toitem.value;
					value = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
				}    
				str += "<br />ToItem<br />index = " + e.toitem.index + "<br />value = " + value;
				G("searchResultPanel").innerHTML = str;
			});
			
			var myValue;
			ac.addEventListener("onconfirm", function(e) {    //鼠标点击下拉列表后的事件
			var _value = e.item.value;
				myValue = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
				G("searchResultPanel").innerHTML ="onconfirm<br />index = " + e.item.index + "<br />myValue = " + myValue;
				
				setPlace();
			});
			function setPlace(){
				map.clearOverlays();    //清除地图上所有覆盖物
				function myFun(){
					var pp = local.getResults().getPoi(0).point;    //获取第一个智能搜索的结果
					map.centerAndZoom(pp, 18);
					var marker = new BMap.Marker(pp);  // 创建标注
					map.addOverlay(marker);    //添加标注
					marker.enableDragging();    //可拖拽
					
					marker.addEventListener("dragend",function(e){
					map.setCenter(new BMap.Point(e.point.lng,e.point.lat));   //设置地图中心点。
					$('#lngs').val(e.point.lng); 
					$('#lats').val(e.point.lat);   
				});
				}
				var local = new BMap.LocalSearch(map, { //智能搜索
				  onSearchComplete: myFun
				});
				local.search(myValue);
			}

			</script>
              <table>
                <tr>
                  <td> 坐标纬度:</td>
                  <td><input type="text" id="lats" name="lat" value="<?php echo $mapshow->lat ?>" class="t_input"  /></td>
                  <td> 坐标经度:</td>
                  <td><input type="text" id="lngs" name="lng" value="<?php echo $mapshow->lng ?>" class="t_input"  /></td>
                </tr>
                <tr>
                  <td> 地图宽度:</td>
                  <td><input type="text" id="width" name="width" value="<?php echo $mapshow->width ?>" class="t_input"  />
                    (px)</td>
                  <td> 地图高度:</td>
                  <td><input type="text" id="height" name="height" value="<?php echo $mapshow->height ?>" class="t_input"  />
                    (px)</td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td colspan="2"><a href="javascript:showHide('field_pane_on_2')"><img src="images/expand.gif" border="0"> 填写关键词&摘要 </a><a href="http://www.doccms.com/seo/#guanjianzi" target="_blank"><img src="./images/help.gif" alt="不知道怎么写关键字？" border="0" /></a>
              <div id="field_pane_on_2" style="display: none; padding:0; margin:0;">
                <table width="100%" border="0" align="center" cellpadding="0">
                  <tr>
                    <td> 页面关键词：</td>
                    <td width="90%"><textarea style='width:60%;' name='keywords' id='keywords' cols='90' rows='3'><?php echo $mapshow->keywords ?></textarea></td>
                  </tr>
                  <tr>
                    <td> 页面摘要：</td>
                    <td width="90%"><textarea style='width:60%;' name='description' id='description' cols='90' rows='3'><?php echo $mapshow->description ?></textarea></td>
                  </tr>
                </table>
              </div></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo ewebeditor(EDITORSTYLE,'content',$mapshow->content); ?></td>
          </tr>
          <tr>
            <td colspan="2"><img src="./images/light.gif" alt="内容编辑小建议" border="0" /> 内容优化小建议：段落小标题请用h2,h3标签。文章主题关键词请使用strong,em标签加强语气！ <a href="http://www.doccms.com/seo/#neirong" target="_blank"><img src="./images/help.gif" alt="内容优化小常识" border="0" /></a></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
