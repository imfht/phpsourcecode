<object id="FlashID" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="<?php echo $width?>" height="<?php echo $height?>">
  <param name="movie" value="<?php echo $tag['path.skin'].$url?>" />
  <param name="quality" value="high" />
  <param name="wmode" value="opaque" />
  <param name="swfversion" value="8.0.35.0" />
  <!-- 此 param 标签提示使用 Flash Player 6.0 r65 和更高版本的用户下载最新版本的 Flash Player。如果您不想让用户看到该提示，请将其删除。 -->
  <param name="expressinstall" value="<?php echo $tag['path.skin']?>Scripts/expressInstall.swf" />
  <!-- 下一个对象标签用于非 IE 浏览器。所以使用 IECC 将其从 IE 隐藏。 -->
  <!--[if !IE]>-->
  <object type="application/x-shockwave-flash" data="<?php echo $tag['path.skin'].$url?>" width="<?php echo $width?>" height="<?php echo $height?>">
    <!--<![endif]-->
    <param name="quality" value="high" />
    <param name="wmode" value="opaque" />
    <param name="swfversion" value="8.0.35.0" />
    <param name="expressinstall" value="<?php echo $tag['path.skin']?>Scripts/expressInstall.swf" />
    <!-- 浏览器将以下替代内容显示给使用 Flash Player 6.0 和更低版本的用户。 -->
    <div>
      <h4>此页面上的内容需要较新版本的 Adobe Flash Player。</h4>
      <p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="获取 Adobe Flash Player" width="112" height="33" /></a></p>
    </div>
    <!--[if !IE]>-->
  </object>
  <!--<![endif]-->
</object>