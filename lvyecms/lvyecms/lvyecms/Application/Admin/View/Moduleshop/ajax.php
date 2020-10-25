<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
  <div class="table_list">
    <table width="100%">
      <colgroup>
      <col width="90">
      <col>
      <col width="160">
      </colgroup>
      <thead>
        <tr>
          <td align="center">应用图标</td>
          <td>应用介绍</td>
          <td align="center">操作</td>
        </tr>
      </thead>
      <volist name="data" id="vo">
      <tr>
        <td>
            <div class="app_icon">
            <if condition=" $vo['icon'] ">
            <img src="{$vo.icon}" alt="{$vo.modulename}" width="80" height="80">
            <else/>
            <img src="{$config_siteurl}statics/images/modul.png" alt="{$vo.modulename}" width="80" height="80">
            </if>
            </div>
        </td>
        <td valign="top">
            <h3 class="mb5 f12"><if condition=" $vo['address'] "><a target="_blank" href="{$vo.address}">{$vo.modulename}</a><else />{$vo.modulename}</if></h3>
            <div class="mb5"> <span class="mr15">版本：<b>{$vo.version}</b></span> <span>开发者：<if condition=" $vo['author'] "><a target="_blank" href="{$vo.authorsite}">{$vo.author}</a><else />匿名开发者</if></span> <span>适配 LvyeCMS 最低版本：<if condition=" $vo['adaptation'] ">{$vo.adaptation}<else /><font color="#FF0000">没有标注，可能存在兼容风险</font></if></span> </div>
            <div class="gray"><if condition=" $vo['introduce'] ">{$vo.introduce}<else />没有任何介绍</if></div>
            <div> <span class="mr20"><a href="{$vo.authorsite}" target="_blank">{$vo.authorsite}</a></span> </div>
        </td>
        <td align="center">
          <?php
		  $op = array();
		  if(!isModuleInstall($vo['module'])){
			  $op[] = '<a href="'.U('install',array('sign'=>$vo['sign'])).'" class="btn btn_submit mr5 Js_install">安装</a>';
		  }else{
			 //有安装，检测升级
			 if($vo['upgrade']){
				 $op[] = '<a href="'.U('upgrade',array('sign'=>$vo['sign'])).'" class="btn btn_submit mr5 Js_upgrade" id="upgrade_tips_'.$vo['sign'].'">升级到最新'.$vo['newVersion'].'</a>';
			 }
		  }
		  echo implode('  ',$op);
		  if($vo['price']){
			  echo "<br /><font color=\"#FF0000\">价格：".$vo['price']." 元</font>";
		  }
		  ?>
        </td>
      </tr>
      </volist>
    </table>
  </div>
  <div class="p10">
        <div class="pages">{$Page}</div>
   </div>