	<div id="layout">
		<div id="header">
			<div class="headerNav">
				<div style='margin:10px; font-size:18px; color:#fff; font-weight:bold;'>whoneed_cms管理后台</div>
				  <ul class="nav">
                    <li><a style='font-size:18px; color:#fff; font-weight:bold;'>欢迎您，<?php echo Yii::app()->user->getState('user_name'); ?></a></li>
					<li><a href='/admin/site/changePass' target="navTab" style='font-size:18px; color:#fff; font-weight:bold;'>修改密码</a></li>
					<li><a href='/admin/site/logout' style='font-size:18px; color:#fff; font-weight:bold;'>退出</a></li>
				  </ul>
			</div>
			<!-- navMenu -->
		</div>
		<div id="leftside">
			<div id="sidebar_s">
				<div class="collapse">
					<div class="toggleCollapse"><div></div></div>
				</div>
			</div>
			<div id="sidebar">
				<div class="toggleCollapse"><h2>主菜单</h2><div>收缩</div></div>

				<div class="accordion" fillSpace="sidebar">
					<!-- form -->
					<?php
						// 查看权限数据
						if($arrAuth){
							if($arrDataList){
								foreach($arrDataList as $data){

									// 验证一级栏目权限
									if(array_search($data['id'], $arrAuth) === false){ 
										continue;
									}
								?>
								<div class="accordionHeader">
									<h2><span>Folder</span><?php echo $data['column_name']; ?></h2>
								</div>
								<div class="accordionContent">
									<ul class="tree treeFolder">
									<?php 
										if($data['child']){
											foreach($data['child'] as $data_child){

												// 验证二级栏目权限
												if(array_search($data_child['id'], $arrAuth) === false){ 
													continue;
												}

												$strUrl = '';	// 链接地址
												
												if($data_child['column_url']){	// 直接指定URL
													$strUrl = $data_child['column_url'];
												}else if($data_child['model_id']){	// 读取对应模型中的地址
													$strUrl = CF::getSystemModel($data_child['model_id'])->model_url;
												}

												// 替换相应的id
												if($strUrl){
													$strUrl = str_replace('{$id}', $data_child['id'], $strUrl);
												}else{
													$strUrl = '#';
												}

											?>
                                                <li><a <?php if($strUrl !== '#'){ ?>href="<?php echo $strUrl; ?>" target="navTab" rel="column_list_<?php echo $data_child['id']; ?>" <?php } ?>><?php echo $data_child['column_name']; ?></a>
													<?php
														if($data_child['child']){
															echo '<ul>';
															foreach($data_child['child'] as $data2_child){

																// 验证三级栏目权限
																if(array_search($data2_child['id'], $arrAuth) === false){ 
																	continue;
																}

																$strUrl = '';	// 链接地址
																
																if($data2_child['column_url']){	// 直接指定URL
																	$strUrl = $data2_child['column_url'];
																}else if($data2_child['model_id']){	// 读取对应模型中的地址
																	$strUrl = CF::getSystemModel($data2_child['model_id'])->model_url;
																}

																// 替换相应的id
																if($strUrl){
																	$strUrl = str_replace('{$id}', $data2_child['id'], $strUrl);
																}
													?>
																<li><a href="<?php echo $strUrl; ?>" target="navTab" rel="column_list_<?php echo $data_child['id'].'_'.$data_child['child'];?>"><?php echo $data2_child['column_name']; ?></a></li>
													<?php
															}
															echo '</ul>';
														}
													?>
												</li>
											<?php
											}
										}
									?>
									</ul>
								</div>					
								<?php
								}
							}
						}else{
							echo "<div style='font-size:14px; color:red; height:100px; padding-top:50px; text-align:center;'>无权限,请联系管理员!</div>";
						}
					?>
					<!-- form end -->
				</div>
			</div>
		</div>
		<div id="container">
			<div id="navTab" class="tabsPage">
				<div class="tabsPageHeader">
					<div class="tabsPageHeaderContent"><!-- 显示左右控制时添加 class="tabsPageHeaderMargin" -->
						<ul class="navTab-tab">
							<li tabid="main" class="main"><a href="javascript:;"><span><span class="home_icon">我的主页</span></span></a></li>
						</ul>
					</div>
					<div class="tabsLeft">left</div><!-- 禁用只需要添加一个样式 class="tabsLeft tabsLeftDisabled" -->
					<div class="tabsRight">right</div><!-- 禁用只需要添加一个样式 class="tabsRight tabsRightDisabled" -->
					<div class="tabsMore">more</div>
				</div>
				<ul class="tabsMoreList">
					<li><a href="javascript:;">我的主页</a></li>
				</ul>
				<div class="navTab-panel tabsPageContent layoutBox">
					<div class="page unitBox">
						<div class="accountInfo">
							<p><span>Whoneed 智能管理系统</span></p>
							<p>官网：<a href="http://www.whoneed.com" target="_blank">http://www.whoneed.com</a></p>
						</div>
						<div class="pageFormContent" layoutH="80" style="margin-right:230px">							
							<p>服务器操作系统：<?php echo PHP_OS ;?></p>
							<p>Web服务器： <?php echo $_SERVER["SERVER_SOFTWARE"]?></p>
							<p>PHP 版本：<?php echo PHP_VERSION;?></p>
							<p>PHP运行方式：<?php echo php_sapi_name();?></p>
							<p>安全模式：<?php echo (boolean) ini_get('safe_mode') ?  '是':'否'?></p>
							<p>上传附件限制：<?php echo ini_get('upload_max_filesize');?> </p>
							<p>执行时间限制：<?php echo ini_get('max_execution_time').'秒'?></p>
							<p>服务器时间：<?php echo date("Y年n月j日 H:i:s")?></p>
							<p>北京时间：<?php echo gmdate("Y年n月j日 H:i:s",time()+8*3600)?></p>
							<p>服务器域名：<?php echo $_SERVER['SERVER_NAME'];?></p>
							<p>IP：<?php echo gethostbyname($_SERVER['SERVER_NAME']);?></p>
							<p>剩余空间：<? echo round((@disk_free_space(".")/(1024*1024)),2).'M'?></p>
						</div>

					</div>
					
				</div>
			</div>
		</div>

	</div>

	<div id="footer">Copyright &copy; 2012 <a href="http://www.whoneed.com" target="dialog">whoneed</a></div>


