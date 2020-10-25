<div class="col-lg-12 diy-css">
	{$form_error['text']??''}
	{$form_error['img']??''}
	{$form_error['voice']??''}
	{$form_error['video']??''}
	{$form_error['news']??''}
	<div style="margin-top:10px"></div>
	<div id="reply_text" style="display:{$msg_type=='text'?'':'none'};">
		<div class="form-group">
			<label for="text_content" class="control-label">回复内容（文本） ：</label>
			<textarea class="form-control" name="text" id="text_content" rows="3">{$text??''}</textarea>
  		</div>
	</div>
	<div class="form-group" id="reply_img" style="display:{$msg_type=='img'?'':'none'}" >
		<input type="hidden" name="img" id="img" value="{$img['id']??''}"/>
		<label class="control-label">回复内容（图片）：
			<a href="{$public}file/index/fileList?type=1{$ecms_hashur['href']??''}" 
				class="btn btn-success" data-toggle="modal" data-target="#myModal2" >选择图片
			</a>
		</label>
		<div id="img_cover" style="height:110px;padding-top:10px;{$msg_type=='img'?'':'display:none'}">
			<div class="col-xs-6">
				<img src="{$img['img_url']??''}" class="img-responsive" alt="回复图片预览" width="300" height="300" id="img_view"/>
			</div>
			<div class="col-xs-6">
				<span onclick="delete_content('img');" class="btn btn-danger">删除</span>
				<h4 id="img_title">{$img['img_title']??''}</h4>
			</div>
		</div>   
	</div>
	<div class="form-group" id="reply_voice" style="display:{$msg_type=="voice"?'':'none'}" >
		<input type="hidden" name="voice" id="voice" value="{$voice['id']??''}"/>
		<label class="control-label">回复内容（音频）：
			<a href="{$public}file/index/fileList?type=3{$ecms_hashur['href']??''}" 
				class="btn btn-success" data-toggle="modal" data-target="#myModal2">选择音频
			</a>
		</label>
		<div id="voice_cover" style="{$msg_type=="voice"?'':'display:none'}">
			<h4 id="voice_title">{$voice['voice_title']??''}</h4>
			<audio src="{$voice['voice_url']??''}" id="voice_view" controls preload="none">您的浏览器不支持 audio 标签。</audio>
			<h4><span onclick="delete_content('voice');" class="btn btn-danger">删除</span></h4>
		</div>
	</div>
	<div class="form-group" id="reply_video" style="display:{$msg_type=="video"?'':'none'}" >
		<input type="hidden" name="video" id="video" value="{$video['id']??''}"/>
		<label class="control-label">回复内容（视频）：
			<a href="{$public}file/index/fileList?type=4{$ecms_hashur['href']??''}" 
				class="btn btn-success" data-toggle="modal" data-target="#myModal2">选择视频
			</a>
		</label>
		<div id="video_cover" style="{$msg_type=="video"?'':'display:none'}">
			<h4 id="video_title">{$video['video_title']??''}</h4>
			<video  style="max-width: 300px;max-height:300px" src="{$video['video_url']??''}" poster="{$video['thumb']??''}" id="video_view" controls preload="none">您的浏览器不支持 video 标签。</video>
			<h4><span onclick="delete_content('video');" class="btn btn-danger">删除</span></h4>
		</div>
	</div>
	<div id="reply_music" style="display:none"></div>
	<div class="form-group" id="reply_news" style="display:{$msg_type=="news"?'':'none'}">
		<div id="news_group">
			{if !isset($news)}
				<?php $news=[];?>
			{/if}
			<div style="width:550px;" class="text-left">
				<div id="news_0" style="border: 1px black ;word-break: break-all;">
					<!-- 封面图片 -->
					<div class="col-xs-8">
						<div style="width:400px;height:160px;float:left;max-width:100%">
							<?php $news=is_array($news)?$news:array();?>
							<img id="title_img_0" class="img-responsive img-rounded" src="{$news[0]['title_img']??''}"  alt="封面图片" style="width:100%;height:100%;" />
							<!-- 标题 -->
							<div style="clear: both;position:relative;height:40px; bottom:40px;background-color:white;opacity:0.65;margin:auto 1px;padding-left:3px;">
								<span class="text-danger">标题：</span>
								<span id="title_0">{$news[0]['title']??''}</span>
							</div>
						</div>
						<div style="display:none;max-width:100%">摘要：<span id="abstract_0">{$news[0]['abstract']??''}</span></div>
					</div>
					<!-- 操作 -->
					<div class="col-xs-4">
						<input type="hidden" name="news[]" id="news_id_0" value="{$news[0]['id']??''}" />
						<a href="{$public}news/index/newsList?list_num=0{$ecms_hashur['href']??''}" class="btn btn-success" data-toggle="modal" data-target="#myModal2">选择图文</a>
						<input type="button" class="btn btn-danger" value="删除" onclick="deleteOneNews(0)"/>
					</div>
				</div>
				<?php for($i=1;$i<8;$i++){ ?>
					<div id="news_{$i}" style="word-break: break-all;clear:both;display:<?php echo isset($news[$i]['id'])?'':"none";?>;padding-top:10px;">
						<div class="col-xs-8" style="border: 1px black ;">
							<!-- 标题与摘要 -->
							<div class="col-xs-8" style="height:100px;float:left;text-align:left">
								<h5>
									<span class="text-danger">标题：</span>
									<span id="title_<?=$i?>"><?php echo isset($news[$i]['title'])?substr($news[$i]['title'],0,32):'';?></span>
								</h5>
								<h5>
									<span class="text-danger">摘要：</span>
									<span id="abstract_<?=$i?>"><?php echo isset($news[$i]['abstract'])?substr($news[$i]['abstract'],0,32):'';?></span>
								</h5>
							</div>
							<!-- 图片 -->
							<div class="col-xs-4" style="float:left;height:100px" >
								<img class="img-responsive img-thumbnail" id="title_img_{$i}" src="{$news[$i]['title_img']??''}"  alt="封面图片" style="width:100px;height:100px;min-width:100px" />
							</div>
						</div>
						<!-- 操作 -->
						<div class="col-xs-4">
							<input name="news[]" id="news_id_{$i}" type="hidden" value="{$news[$i]['id']??''}"  />
							<a href="{$public}news/index/newsList?list_num={$i}{$ecms_hashur['href']??''}" class="btn btn-success" data-toggle="modal" data-target="#myModal2">选择图文</a>
							
							<input type="button" class="btn btn-danger" value="删除" onclick="deleteOneNews(<?php echo $i;?>);newsHide(<?php echo $i;?>)"/>
						</div>
					</div>
				<?php }?>
				<div class="text-center" style="clear: both;">
					<span style="cursor:pointer;height:50px;width:100%; font-size:5em" id='add_news' onClick="add_news()">+</span>
				</div>
			</div>
			
		</div>
	</div>
</div>


