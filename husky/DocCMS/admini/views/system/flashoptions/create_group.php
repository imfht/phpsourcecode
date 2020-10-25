<script type="text/javascript" src="../inc/js/myfocus/myfocus-2.0.4.full.js"></script><!--引入myFocus库-->
<div class="location">当前位置:<a href="./index.php">首 页</a>→<a href="./index.php?m=system&s=managechannel">操作员后台</a>→<a href="./index.php?m=system&s=flashoptions">广告管理</a>→<a href="./index.php?m=system&s=flashoptions&a=create_group">添加一个新的焦点图组</a></div>
<form name="form1" method="post" action="?m=system&s=flashoptions&a=create_group">
  <table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb">
    <tr class="adtbtitle">
      <td width="892"><h3>添加焦点图片组</h3><a href="javascript:history.back(1)" class="creatbt">返回</a></td>
      <td width="72"><div align="right">
          <input name="submit" type="submit" value=" 保存 " class="savebt" />
        </div></td>
    </tr>
    <tr>
      <td colspan="2" bgcolor="#FFFFFF"><div class="focusmain"> 
        <script type="text/javascript">
			myFocus.set({id:'myFocus',pattern:'mF_fscreen_tb', time:2 ,trigger:0,width:450,height:300});
		</script>
          <div class="focus">
            <div id="myFocus-wrap">
              <div id="myFocus"><!--焦点图盒子-->
                <div class="__loading"><img src="../inc/js/myfocus/pattern/img/loading.gif" alt="请稍候..." /></div>
                <!--载入画面-->
                <div class="__pic"><!--图片列表-->
                  <ul>
                    <!--内容列表-->
                    <li><a target="_blank" href="http://www.doccms.com/"><img src="images/ad_focus/banner1.jpg" thumb="" alt="DOCCMS焦点图模块演示图片之一" text="图片1更详细的描述文字" /></a></li>
                    <li><a target="_blank" href="http://www.doccms.com/"><img src="images/ad_focus/banner2.jpg" thumb="" alt="DOCCMS焦点图模块演示图片之二" text="图片2更详细的描述文字" /></a></li>
                    <li><a target="_blank" href="http://www.doccms.com/"><img src="images/ad_focus/banner3.jpg" thumb="" alt="DOCCMS焦点图模块演示图片之三" text="图片3更详细的描述文字" /></a></li>
                    <li><a target="_blank" href="http://www.doccms.com/"><img src="images/ad_focus/banner4.jpg" thumb="" alt="DOCCMS焦点图模块演示图片之三" text="图片3更详细的描述文字" /></a></li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="clear"></div>
          </div>
          <div class="set">
            <p class="style">
              焦点图名称：
              <input id="title" name="title"  value="" />
              绑定ID：
              <input id="id" name="boxId"  value="myFocus" />
              风格应用选择：
              <select id="pattern" name="pattern" onChange="">
                <option value="mF_fscreen_tb">mF_fscreen_tb</option>
                <option value="mF_YSlider">mF_YSlider</option>
                <option value="mF_luluJQ">mF_luluJQ</option>
                <option value="mF_51xflash">mF_51xflash</option>
                <option value="mF_expo2010">mF_expo2010</option>
                <option value="mF_games_tb">mF_games_tb</option>
                <option value="mF_ladyQ">mF_ladyQ</option>
                <option value="mF_liquid">mF_liquid</option>
                <option value="mF_liuzg">mF_liuzg</option>
                <option value="mF_pithy_tb">mF_pithy_tb</option>
                <option value="mF_qiyi">mF_qiyi</option>
                <option value="mF_quwan">mF_quwan</option>
                <option value="mF_rapoo">mF_rapoo</option>
                <option value="mF_sohusports">mF_sohusports</option>
                <option value="mF_taobao2010">mF_taobao2010</option>
                <option value="mF_taobaomall">mF_taobaomall</option>
                <option value="mF_tbhuabao">mF_tbhuabao</option>
                <option value="mF_pconline">mF_pconline</option>
                <option value="mF_peijianmall">mF_peijianmall</option>
                <option value="mF_classicHC">mF_classicHC</option>
                <option value="mF_classicHB">mF_classicHB</option>
                <option value="mF_slide3D">mF_slide3D</option>
                <option value="mF_kiki">mF_kiki</option>
              </select>
            </p>
            <p class="h4"><span>基本参数设置：</span>(提示：修改参数后点击上面的"应用"以使其生效；另外，不同的风格样式可能会有不同的参数设置)</p>
            <div class="base" id="base">
              <p id="p-time">切换时间间隔:
                <select id="time" name="times">
                  <option value="2">2秒</option>
                  <option value="3">3秒</option>
                  <option value="4">4秒</option>
                  <option value="6">6秒</option>
                </select>
              </p>
              <p id="p-adTrigger">按钮触发切换模式:
                <select id="adTrigger" name="adTrigger">
                  <option value="0" >点击(click)</option>
                  <option value="1" >悬停(mouseover)</option>
                </select>
              </p>
              <div class="clear"></div>
              <p id="p-width">设置宽度(主图区):
                <input id="width" name="width" value="450" />
                (px) </p>
              <p id="p-height">设置高度(主图区):
                <input id="height" name="height" value="296" />
                (px) </p>
              <p id="p-txtHeight">文字层高度(设置0为隐藏):
                <input id="txtHeight" name="txtHeight" class="def" value="default" />
                (px) </p>
            </div>
            <div class="clear"></div>
            <span class="tip"> 提示：Demo用5张图片是为了更好的展示带略缩图风格的滚动，实际中使用你可以添加任意多的图片；<br />
            </span> </div>
        </div></td>
    </tr>
  </table>
</form>
