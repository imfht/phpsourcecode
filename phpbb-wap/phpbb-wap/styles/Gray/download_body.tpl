			<div id="main">
				<div class="title">文件信息</div>
				{ERROR_BOX}
				<div class="module">
					<div class="module bm-gray">文件名：{FILENAME}</div>
					<div class="module bm-gray">大小：{FILESIZE} {SIZELANG}</div>
					<div class="module bm-gray">下载：{COUNT} 次</div>
					<div class="module bm-gray">描述：{COMMENT}</div>
<!-- BEGIN information -->
					<div class="module bm-gray">您下载该附件需要扣取 {information.DOWNLOAD_CUT_POINTS} {information.POINT_NAME}，您目前的账户有 {information.USER_POINTS} {information.POINT_NAME}，在1小时内重复下载不扣取 {information.POINT_NAME}</div>
<!-- END information -->
					<div class="module bm-gray">{IMG_BACKPOST}<a href="{U_BACKPOST}">返回帖子</a></div>
					<div class="module">{IMG_DOWNLOAD}<a href="{U_DOWNLOAD}" target="_blank">点击下载</a></div>
				</div>
				<div class="title">下载说明</div>
				<div class="module">
					<p class="bm-gray">1、如果迅雷下载时文件名为 <u>download.php</u> 请放心下载，下载完成后会自动恢复文件名。</p>
					<p class="bm-gray">2、当你使用的浏览器下载为 <u>download.php</u> 下载完成后请把 <u>download.php</u> 改成 <u>{FILENAME}</u> 即可。</p>
					<p>3、如果下载地址出错或者你对本站有什么好的建议你可以联系本站的管理员人员。</p>
				</div>
			</div>