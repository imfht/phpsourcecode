<!-- BEGIN attach --> 
				<hr />
	<!-- BEGIN denyrow -->
					<div>{postrow.attach.denyrow.L_DENIED}</div>
	<!-- END denyrow --> 
	<!-- BEGIN cat_images -->
				<div class="module">
					<div>{postrow.attach.cat_images.COMMENT}</div>
					<div class="attachImg"><a href="{postrow.attach.cat_images.IMG_SRC}"><img src="{postrow.attach.cat_images.IMG_SRC}" alt="." border="0" /></a></div>
				</div>
	<!-- END cat_images --> 
	<!-- BEGIN cat_thumb_images -->
				<div class="module">
					<div>{postrow.attach.cat_thumb_images.S_UPLOAD_IMAGE}<a href="{postrow.attach.cat_thumb_images.IMG_SRC}">{postrow.attach.cat_thumb_images.DOWNLOAD_NAME}</a></div>
					<div>(大小：({postrow.attach.cat_thumb_images.FILESIZE} {postrow.attach.cat_thumb_images.SIZE_VAR})</div>
					<div>查看：{postrow.attach.cat_thumb_images.DOWNLOAD_COUNT} 次</div>
					<div>描述：{postrow.attach.cat_thumb_images.COMMENT}</div>
				</div>
	<!-- END cat_thumb_images -->
	<!-- BEGIN cat_stream -->
				<div class="module">
					<div>描述：{postrow.attach.cat_stream.COMMENT}</div>
					<div>大小：{postrow.attach.cat_stream.FILESIZE} {postrow.attach.cat_stream.SIZE_VAR}</div>
					<div>播放：{postrow.attach.cat_stream.DOWNLOAD_COUNT} 次</div>
					<div>
						<object id="wmp" classid="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,0,0,0" standby="Loading Microsoft Windows Media Player components..." type="application/x-oleobject"> 
							<param name="FileName" value="{postrow.attach.cat_stream.U_DOWNLOAD_LINK}"> 
							<param name="ShowControls" value="1"> 
							<param name="ShowDisplay" value="0"> 
							<param name="ShowStatusBar" value="1"> 
							<param name="AutoSize" value="1"> 
							<param name="AutoStart" value="0"> 
							<param name="Visible" value="1"> 
							<param name="AnimationStart" value="0"> 
							<param name="Loop" value="0"> 
							<embed type="application/x-mplayer2" pluginspage="http://www.microsoft.com/windows95/downloads/contents/wurecommended/s_wufeatured/mediaplayer/default.asp" src="{postrow.attach.cat_stream.U_DOWNLOAD_LINK}" name="MediaPlayer2" showcontrols="1" showdisplay="0" showstatusbar="1" autosize="1" autostart="0" visible="1" animationatstart="0" loop="0"></embed> 
						</object>
					<div>
				<div>
	<!-- END cat_stream -->
	<!-- BEGIN cat_swf -->
				<div class="module">
					<div>描述：{postrow.attach.cat_swf.COMMENT}</div>
					<div>大小：{postrow.attach.cat_swf.FILESIZE} {postrow.attach.cat_swf.SIZE_VAR}</div>
					<div>播放：{postrow.attach.cat_swf.DOWNLOAD_COUNT} 次</div>
					<div>
						<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0" width="{postrow.attach.cat_swf.WIDTH}" height="{postrow.attach.cat_swf.HEIGHT}"> 
							<param name=movie value="{postrow.attach.cat_swf.U_DOWNLOAD_LINK}"> 
							<param name=loop value=true> 
							<param name=quality value=high> 
							<param name=scale value=noborder> 
							<param name=wmode value=transparent> 
							<param name=bgcolor value=#000000> 
							<embed src="{postrow.attach.cat_swf.U_DOWNLOAD_LINK}" loop="true" quality="high" scale="noborder" wmode="transparent" bgcolor="#000000"  width="{postrow.attach.cat_swf.WIDTH}" height="{postrow.attach.cat_swf.HEIGHT}" type="application/x-shockwave-flash" pluginspace="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash"></embed> 
						</object>
					<div>
				<div>
	<!-- END cat_swf -->
	<!-- BEGIN attachrow -->
				<div class="module">
					<div><a href="{postrow.attach.attachrow.U_DOWNLOAD_LINK}">{postrow.attach.attachrow.S_UPLOAD_IMAGE}{postrow.attach.attachrow.DOWNLOAD_NAME}</a></div>
					<div>大小：{postrow.attach.attachrow.FILESIZE} {postrow.attach.attachrow.SIZE_VAR}</div>
					<div>下载：{postrow.attach.attachrow.DOWNLOAD_COUNT} 次</div>
					<div>描述：{postrow.attach.attachrow.COMMENT}</div>
				</div>
	<!-- END attachrow -->
	<!-- BEGIN attachinfo -->
				<div class="module">
					<div>{postrow.attach.attachinfo.S_UPLOAD_IMAGE}{postrow.attach.attachinfo.DOWNLOAD_NAME}</div>
				</div>
	<!-- END attachinfo -->
	<!-- BEGIN attachnote -->
				<div class="module">
					<p class="red">提示：请登录后获取权限您才能下载这些附件！</p>
				</div>
	<!-- END attachnote -->
	
<!-- END attach -->	