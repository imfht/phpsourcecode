<script type="text/javascript" src="../inc/js/myfocus/myfocus-2.0.4.min.js"></script><!--引入myFocus库-->
<span id="test"></span>
<div class="location">当前位置: <a href="./index.php">首 页</a> → <a href="./index.php?m=system&s=managechannel">操作员后台</a> → <a href="./index.php?m=system&s=flashoptions">焦点图调用管理</a> → <a href="./index.php?m=system&s=flashoptions&a=edit_group&group_id=<?php echo $request['group_id'] ?>">修改焦点图信息</a></div>
<form name="form1" method="post" action="?m=system&s=flashoptions&a=edit_group&group_id=<?php echo $request['group_id'] ?>">
  <table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb">
    <tr class="adtbtitle">
      <td width="892"><h3>修改焦点图组</h3><a href="./index.php?m=system&s=flashoptions" class="creatbt">返回</a></td>
      <td align="right"><span id="msg" style="color:#FF0000"></span></td>
    </tr>
    <tr>
      <td colspan="2" bgcolor="#FFFFFF"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td colspan="2" bgcolor="#FFFFFF"><div class="focusmain">
                <div class="focus">
                  <div id="myFocus-wrap">
                    <div id="myFocus" class="<?php echo $edit_group_item->pattern?>"><!--焦点图盒子-->
                      <div class="__loading"><img src="../inc/js/myfocus/pattern/img/loading.gif" alt="请稍候..." /></div>
                      <!--载入画面-->
                      <div class="__pic">
                        <ul>
                          <!--内容列表-->
						  <?php
                            if(isset($data)){
                                foreach($data['results'] as $k=>$v){
                          ?>
                          <li><a href="<?php echo $v['url']; ?>" target="_blank"><img src="<?php echo $v['picpath']; ?>" thumb="" alt="<?php echo $v['title']; ?>" text="<?php echo $v['summary']; ?>" /></a></li>
                          <?php 
                                }   
                            }else{
                          ?>
                          <li><a target="_blank" href="http://www.doccms.com/"><img src="images/ad_focus/banner1.jpg" thumb="" alt="DOCCMS焦点图模块演示图片之一" text="图片1更详细的描述文字" /></a></li>
                                  <li><a target="_blank" href="http://www.doccms.com/"><img src="images/ad_focus/banner2.jpg" thumb="" alt="DOCCMS焦点图模块演示图片之二" text="图片2更详细的描述文字" /></a></li>
                                  <li><a target="_blank" href="http://www.doccms.com/"><img src="images/ad_focus/banner3.jpg" thumb="" alt="DOCCMS焦点图模块演示图片之三" text="图片3更详细的描述文字" /></a></li>
                                  <li><a target="_blank" href="http://www.doccms.com/"><img src="images/ad_focus/banner4.jpg" thumb="" alt="DOCCMS焦点图模块演示图片之三" text="图片3更详细的描述文字" /></a></li>
                          <?php
                            }
                            ?>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <div class="clear"></div>
                </div>
                <div class="clear"></div>
                <?php 
			$file = rec_listFiles('../inc/js/myfocus/pattern/','css');
			for($i=0;$i<count($file);$i++)
			{				
				$files[extend_1($file[$i])] = extend_1($file[$i]);
			}
			$timesAry = array("2" => "2秒",
							  "3" => "3秒",
							  "4" => "4秒",
							  "6" => "6秒");
			$clickAry = array("click" => "点击(click)",
							  "mouseover" => "悬停(mouseover)");	
			?>
                <div class="set">
                  <p class="style">
                    焦点图名称
                    <input id="title" name="title"  value="<?php echo $edit_group_item->title?>" />
                    绑定ID：
                    <input id="id" name="boxId"  value="<?php echo $edit_group_item->boxId?>" />
                    风格应用选择： <?php echo select($files,'pattern',$edit_group_item->pattern);?>
                    <input name="submit" type="submit" class="button" value="应 用" />
                  </p>
                  <p class="h4"><span>基本参数设置：</span></p>
                  <div class="base" id="base">
                    <p id="p-time">切换时间间隔: <?php echo select($timesAry,'times',$edit_group_item->times);?> </p>
                    <p id="p-adTrigger">按钮触发切换模式: <?php echo select($clickAry,'adTrigger',$edit_group_item->adTrigger);?> </p>
                    <div class="clear"></div>
                    <p id="p-width">设置宽度(主图区):
                      <input id="width" name="width" value="<?php echo $edit_group_item->width?>" />
                      (px) </p>
                    <p id="p-height">设置高度(主图区):
                      <input id="height" name="height" value="<?php echo $edit_group_item->height?>" />
                      (px) </p>
                    <p id="p-txtHeight">文字层高度(设置0为隐藏):
                      <input id="txtHeight" name="txtHeight" class="def" value="<?php echo $edit_group_item->txtHeight?>" />
                      (px) </p>
                  </div>
                  <div class="clear"></div>
                  <div class="clear"></div>
                  <span class="tip"> 提示：Demo用5张图片是为了更好的展示带略缩图风格的滚动，实际中使用你可以添加任意多的图片；<br />
                  </span> </div>
              </div>
              <script type="text/javascript">
myFocus.set({id:'myFocus',pattern:'<?php echo $edit_group_item->pattern?>', time:<?php echo $edit_group_item->times?>,trigger:'<?php echo $edit_group_item->adTrigger?>',width:<?php echo $edit_group_item->width?>,height:<?php echo $edit_group_item->height?>,txtHeight:'<?php echo $edit_group_item->txtHeight?>',rootpath:'<?php echo ROOTPATH?>'});
</script></td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2"><?php echo $flash_group->render(); ?></td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>
