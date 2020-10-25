<?php 
/*
 * This file is part of the emlog for bootstrap Project. See CREDITS and LICENSE files
 *
 * emlog for bootstrap Project URL:https://git.oschina.net/ysfl/emlog-for-bootstrap
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
/**
 * 侧边栏组件、页面模块
 */
if(!defined('EMLOG_ROOT')) {exit('error!');} 
?>
<?php
//widget：blogger
function widget_blogger($title){
	global $CACHE;
	$user_cache = $CACHE->readCache('user');
	$name = $user_cache[1]['mail'] != '' ? "<a href=\"mailto:".$user_cache[1]['mail']."\">".$user_cache[1]['name']."</a>" : $user_cache[1]['name'];?>
	<li>
	<h3><span><?php echo $title; ?></span></h3>
	<ul id="bloggerinfo">
	<div id="bloggerinfoimg">
	<?php if (!empty($user_cache[1]['photo']['src'])): ?>
	<img src="<?php echo BLOG_URL.$user_cache[1]['photo']['src']; ?>" width="<?php echo $user_cache[1]['photo']['width']; ?>" height="<?php echo $user_cache[1]['photo']['height']; ?>" alt="blogger" />
	<?php endif;?>
	</div>
	<p><b><?php echo $name; ?></b>
	<?php echo $user_cache[1]['des']; ?></p>
	</ul>
	</li>
<?php }?>
<?php
//widget：日历
function widget_calendar($title){ ?>
	<li>
	<h3><span><?php echo $title; ?></span></h3>
	<div id="calendar">
	</div>
	<script>sendinfo('<?php echo Calendar::url(); ?>','calendar');</script>
	</li>
<?php }?>
<?php
//widget：标签
function widget_tag($title){
	global $CACHE;
	$tag_cache = $CACHE->readCache('tags');?>
	<li>
	<h3><span><?php echo $title; ?></span></h3>
	<ul id="blogtags">
	<?php foreach($tag_cache as $value): ?>
		<span style="font-size:<?php echo $value['fontsize']; ?>pt; line-height:30px;">
		<a href="<?php echo Url::tag($value['tagurl']); ?>" title="<?php echo $value['usenum']; ?> 篇文章"><?php echo $value['tagname']; ?></a></span>
	<?php endforeach; ?>
	</ul>
	</li>
<?php }?>
<?php
//widget：分类
function widget_sort($title){
	global $CACHE;
	$sort_cache = $CACHE->readCache('sort'); ?>
	<li>
	<h3><span><?php echo $title; ?></span></h3>
	<ul id="blogsort">
	<?php
	foreach($sort_cache as $value):
		if ($value['pid'] != 0) continue;
	?>
	<li>
	<a href="<?php echo Url::sort($value['sid']); ?>"><?php echo $value['sortname']; ?>(<?php echo $value['lognum'] ?>)</a>
	<?php if (!empty($value['children'])): ?>
		<ul>
		<?php
		$children = $value['children'];
		foreach ($children as $key):
			$value = $sort_cache[$key];
		?>
		<li>
			<a href="<?php echo Url::sort($value['sid']); ?>"><?php echo $value['sortname']; ?>(<?php echo $value['lognum'] ?>)</a>
		</li>
		<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	</li>
	<?php endforeach; ?>
	</ul>
	</li>
<?php }?>
<?php
//widget：最新微语
function widget_twitter($title){
	global $CACHE; 
	$newtws_cache = $CACHE->readCache('newtw');
	$istwitter = Option::get('istwitter');
	?>
	 <div class="panel panel-primary">
  <div class="panel-heading sidebar-wy-heading">
    <h3 class="panel-title">微语</h3>
    <a style="color: rgb(255,255,255)" href="<?php echo BLOG_URL . 't/'; ?>">更多&raquo;</a>
  </div>
  <div class="panel-body">
	<li>
	<ul id="twitter">
	<?php foreach($newtws_cache as $value): ?>
	<?php $img = empty($value['img']) ? "" : '<a title="查看图片" class="t_img" href="'.BLOG_URL.str_replace('thum-', '', $value['img']).'" target="_blank">&nbsp;</a>';?>
	<li><?php echo $value['t']; ?><?php echo $img;?><p><?php echo smartDate($value['date']); ?></p></li>
	<?php endforeach; ?>
    <?php if ($istwitter == 'y') :?>  
	<?php endif;?>
	</ul>
	</li>
	</div>
</div>
<?php }?>
<?php
//widget：最新评论
function widget_newcomm($title){
	global $CACHE; 
	$com_cache = $CACHE->readCache('comment');
	?>
	<div class="panel panel-primary">
  		<div class="panel-heading">
    		<h3 class="panel-title">最新评论</h3>
  		</div>
  		<div class="panel-body">
			<li>
				<ul id="newcomment">
					<?php foreach($com_cache as $value):$url = Url::comment($value['gid'], $value['page'], $value['cid']);?>
					<li><span class="newcomment-name label label-primary"><?php echo $value['name']; ?></span>
					说：<a href="<?php echo $url; ?>"><?php echo $value['content']; ?></a></li>
					<?php endforeach; ?>
				</ul>
			</li>
		</div>
</div>
<?php }?>
<?php
//widget：最新文章
function widget_newlog($title){
	global $CACHE; 
	$newLogs_cache = $CACHE->readCache('newlog');
	?>
	<li>
	<h3><span><?php echo $title; ?></span></h3>
	<ul id="newlog">
	<?php foreach($newLogs_cache as $value): ?>
	<li><a href="<?php echo Url::log($value['gid']); ?>"><?php echo $value['title']; ?></a></li>
	<?php endforeach; ?>
	</ul>
	</li>
<?php }?>
<?php
//widget：热门文章
function widget_hotlog($title){
	$index_hotlognum = Option::get('index_hotlognum');
	$Log_Model = new Log_Model();
	$randLogs = $Log_Model->getHotLog($index_hotlognum);?>
	<li>
	<h3><span><?php echo $title; ?></span></h3>
	<ul id="hotlog">
	<?php foreach($randLogs as $value): ?>
	<li><a href="<?php echo Url::log($value['gid']); ?>"><?php echo $value['title']; ?></a></li>
	<?php endforeach; ?>
	</ul>
	</li>
<?php }?>
<?php
//widget：随机文章
function widget_random_log($title){
	$index_randlognum = Option::get('index_randlognum');
	$Log_Model = new Log_Model();
	$randLogs = $Log_Model->getRandLog($index_randlognum);?>
	<li>
	<h3><span><?php echo $title; ?></span></h3>
	<ul id="randlog">
	<?php foreach($randLogs as $value): ?>
	<li><a href="<?php echo Url::log($value['gid']); ?>"><?php echo $value['title']; ?></a></li>
	<?php endforeach; ?>
	</ul>
	</li>
<?php }?>
<?php
//widget：搜索
function widget_search($title){ ?>
	<li>
		<ul class="search">
			<form class="form-inline" name="keyform" method="get" action="<?php echo BLOG_URL; ?>index.php">
				<div class="form-group has-success has-feedback">
  					<input name="keyword" type="text" class="form-control" placeholder="按回车搜索">
  					<span class="glyphicon glyphicon-search form-control-feedback"></span>
				</div>
			</form>
		</ul>
	</li>

<?php } ?>
<?php
//widget：归档
function widget_archive($title){
	global $CACHE; 
	$record_cache = $CACHE->readCache('record');
	?>
	<div class="panel panel-primary">
  		<div class="panel-heading">
    		<h3 class="panel-title">文章归档</h3>
  		</div>
  		<div class="panel-body">
			<li>
				<ul id="record">
					<?php foreach($record_cache as $value): ?>
						<li><a href="<?php echo Url::record($value['date']); ?>"><?php echo $value['record']; ?>(<?php echo $value['lognum']; ?>)</a></li>
					<?php endforeach; ?>
				</ul>
			</li>
		</div>
	</div>
<?php } ?>
<?php
//widget：自定义组件
function widget_custom_text($title, $content){ ?>
	<li>
	<h3><span><?php echo $title; ?></span></h3>
	<ul>
	<?php echo $content; ?>
	</ul>
	</li>
<?php } ?>
<?php
//widget：链接
function widget_link($title){
	global $CACHE; 
	$link_cache = $CACHE->readCache('link');
    //if (!blog_tool_ishome()) return;#只在首页显示友链去掉双斜杠注释即可
	?>
	<div class="panel panel-primary">
  		<div class="panel-heading">
   			<h3 class="panel-title">友情链接</h3>
  		</div>
  		<div class="panel-body">
			<li>
				<ul id="link">
					<?php foreach($link_cache as $value): ?>
						<li><a href="<?php echo $value['url']; ?>" title="<?php echo $value['des']; ?>" target="_blank"><?php echo $value['link']; ?></a></li>
						<?php endforeach; ?>
				</ul>
			</li>
		</div>
	</div>
<?php }?> 
<?php
//blog：导航
function blog_navi(){
	global $CACHE; 
	$navi_cache = $CACHE->readCache('navi');
	?>
	<ul class="nav navbar-nav">
	<?php
	foreach($navi_cache as $value):

        if ($value['pid'] != 0) {
            continue;
        }

		if($value['url'] == ROLE_ADMIN && (ROLE == ROLE_ADMIN || ROLE == ROLE_WRITER)):
			?>
			<li class="item common"><a href="<?php echo BLOG_URL; ?>admin/">管理站点</a></li>
			<li class="item common"><a href="<?php echo BLOG_URL; ?>admin/?action=logout">退出</a></li>
			<?php 
			continue;
		endif;
		$newtab = $value['newtab'] == 'y' ? 'target="_blank"' : '';
        $value['url'] = $value['isdefault'] == 'y' ? BLOG_URL . $value['url'] : trim($value['url'], '/');
        $current_tab = BLOG_URL . trim(Dispatcher::setPath(), '/') == $value['url'] ? 'active' : 'common';
		?>
		<li class="item <?php echo $current_tab;?>">
			<a href="<?php echo $value['url']; ?>" <?php echo $newtab;?>><?php echo $value['naviname']; ?></a>
			<?php if (!empty($value['children'])) :?>
            <ul class="sub-nav">
                <?php foreach ($value['children'] as $row){
                        echo '<li><a href="'.Url::sort($row['sid']).'">'.$row['sortname'].'</a></li>';
                }?>
			</ul>
            <?php endif;?>

            <?php if (!empty($value['childnavi'])) :?>
            <ul class="sub-nav">
                <?php foreach ($value['childnavi'] as $row){
                        $newtab = $row['newtab'] == 'y' ? 'target="_blank"' : '';
                        echo '<li><a href="' . $row['url'] . "\" $newtab >" . $row['naviname'].'</a></li>';
                }?>
			</ul>
            <?php endif;?>

		</li>
	<?php endforeach; ?>
	</ul>
<?php }?>
<?php
//blog：置顶
function topflg($top, $sortop='n', $sortid=null){
    if(blog_tool_ishome()) {
       echo $top == 'y' ? "<img src=\"".TEMPLATE_URL."/images/top.png\" title=\"首页置顶文章\" /> " : '';
    } elseif($sortid){
       echo $sortop == 'y' ? "<img src=\"".TEMPLATE_URL."/images/sortop.png\" title=\"分类置顶文章\" /> " : '';
    }
}
?>
<?php
//blog：编辑
function editflg($logid,$author){
	$editflg = ROLE == ROLE_ADMIN || $author == UID ? '<div class="post-edit"><a class="btn btn-default" href="'.BLOG_URL.'admin/write_log.php?action=edit&gid='.$logid.'" target="_blank"><span class="glyphicon glyphicon-edit"></span> 编辑</a></div>' : '';
	echo $editflg;
}
?>
<?php
//blog：分类
function blog_sort($blogid){
	global $CACHE; 
	$log_cache_sort = $CACHE->readCache('logsort');
	?>
	<?php if(!empty($log_cache_sort[$blogid])): ?>
    <a href="<?php echo Url::sort($log_cache_sort[$blogid]['id']); ?>"><?php echo $log_cache_sort[$blogid]['name']; ?></a>
	<?php endif;?>
<?php }?>
<?php
//blog：文章标签
function blog_tag($blogid){
	global $CACHE;
	$log_cache_tags = $CACHE->readCache('logtags');
	if (!empty($log_cache_tags[$blogid])){
		$tag = '';
		foreach ($log_cache_tags[$blogid] as $value){
			$tag .= "	<li class='tag-hover'><a href=\"".Url::tag($value['tagurl'])."\">".$value['tagname'].'</a></li>';
		}
		echo $tag;
	}
    else {echo '<li class="notag">这篇文章还没有标签</li>';}
}
?>
<?php
//blog：文章作者
function blog_author($uid){
	global $CACHE;
	$user_cache = $CACHE->readCache('user');
	$author = $user_cache[$uid]['name'];
	$mail = $user_cache[$uid]['mail'];
	$des = $user_cache[$uid]['des'];
	$title = !empty($mail) || !empty($des) ? "title=\"$des $mail\"" : '';
	echo ' <a href="'.Url::author($uid)."\" $title>$author</a>";
}
?>
<?php
//blog：相邻文章
function neighbor_log($neighborLog){
	extract($neighborLog);?>
	<?php if($prevLog):?>
	 <li class="previous"><a href="<?php echo Url::log($prevLog['gid']) ?>">&larr; <?php echo $prevLog['title'];#上一篇文章?></a></li>
	<?php endif;?>
	<?php if($nextLog && $prevLog):?>
	<?php endif;?>
	<?php if($nextLog):?>
		 <li class="next"><a href="<?php echo Url::log($nextLog['gid']) ?>"><?php echo $nextLog['title'];#下一篇文章?> &rarr;</a></li>
	<?php endif;?>
<?php }?>

<?php 
//评论列表
function blog_comments($comments,$allow_remark/*加入评论开关参数,后面需要传参*/){
	extract($comments);
    if($commentStacks):#如果评论数量不为0则输出评论列表?>
    <h2 class="alert alert-info">评论列表</h2>
    <?php elseif($allow_remark/*新加的参数传到这里*/ == 'y'): #如果评数量为0,文章可以评论则输出还没有评论?>
    	<h2 class="alert alert-success">还没有评论，你可以来抢沙发哦!</h2>
    <?php endif; ?>
	<?php
	$isGravatar = Option::get('isgravatar');
	foreach($commentStacks as $cid):#开始循环输出
    $comment = $comments[$cid];
	$comment['poster'] = $comment['url'] ? '<a href="'.$comment['url'].'" target="_blank">'.$comment['poster'].'</a>' : $comment['poster'];
	?>

    <a name="<?php echo $comment['cid']; #获取评论ID?>"></a>
	
    <ul class="comment-list media-list">
        <li class="media comment-list-media">
		
        <?php if($isGravatar == 'y'): ?>

            <a class="media-left" href="#"><img class="media-left" src="<?php echo getGravatar($comment['mail']); # 头像?>" /><?php endif; ?></a>
		
            <div class="media-body">

                <h4 class="media-heading">

                    <span class="glyphicon glyphicon-user"></span>
                    <?php echo $comment['poster']; #评论人名字?> 

                    <span class="glyphicon glyphicon-time"></span>
                    <span class="comment-time">发布于：<?php echo $comment['date']; #发布时间?></span>

                    <a class="btn btn-default btn-xs comment-reply" href="#comment-<?php echo $comment['cid']; ?>" onclick="commentReply(<?php echo $comment['cid']; ?>,this)">回复</a>
                
                </h4>
    				
            <div class="comment-content"><?php echo $comment['content']; #评论内容?></div>

            <?php blog_comments_children($comments, $comment['children']); #显示下级评论?>

            </div>

        </li>
    </ul>
	<?php endforeach; #结束循环?>
    <div id="pagenavi">
	    <?php echo $commentPageUrl;?>
    </div>

<?php }?>

<?php
//blog：子评论列表
function blog_comments_children($comments, $children){
	$isGravatar = Option::get('isgravatar');
	foreach($children as $child):#开始循环输出
	$comment = $comments[$child];
	$comment['poster'] = $comment['url'] ? '<a href="'.$comment['url'].'" target="_blank">'.$comment['poster'].'</a>' : $comment['poster'];
	?>

    <a name="<?php echo $comment['cid']; #获取评论ID?>"></a>

	<ul class="comment-lists media-list">
        <li class="media comment-list-media">
				
        <?php if($isGravatar == 'y'): ?>

            <a class="media-left" href="#"><img src="<?php echo getGravatar($comment['mail']); #头像?>" /><?php endif; ?></a>
		
            <div class="media-body">

                <h4 class="media-heading">

                    <span class="glyphicon glyphicon-user"></span>
                    <?php echo $comment['poster']; #发布人?> 

                    <span class="glyphicon glyphicon-time"></span>
                    <span class="comment-time">发布于：<?php echo $comment['date']; #发布时间?></span>

                    <?php if($comment['level'] < 4): ?><a class="btn btn-default btn-xs comment-reply" href="#comment-<?php echo $comment['cid']; ?>" onclick="commentReply(<?php echo $comment['cid']; ?>,this)">回复</a><?php endif; ?>
                
                </h4>
        		
        		<div class="comment-content"><?php echo $comment['content']; #评论内容?></div>
                
                <?php blog_comments_children($comments, $comment['children']); #显示下级评论?>

            </div>

        </li>
    </ul>
	<?php endforeach; #结束循环?>
<?php }?>

<?php
//blog：发表评论表单
function blog_comments_post($logid,$ckname,$ckmail,$ckurl,$verifyCode,$allow_remark){
	if($allow_remark == 'y'){ #如果评论开启,显示下面内容?>
		<div class="comment-post">
            <div id="comment-place">
                <div id="comment-post">
        			<h3 class="alert alert-info">发表评论：

                        <span style="font-size:15px;color:#34A045;">(发表言论请遵守国家相关法律法规,尊重网上道德)</span>

                        <div class="comment-cancel" id="cancel-reply" style="display:none"><a class="btn btn-default" href="javascript:void(0);" onclick="cancelReply()">取消回复</a></div>
                    
                    </h3>
        			<form class="form-inline comment-control" role="form" method="post" name="commentform" action="<?php echo BLOG_URL; ?>index.php?action=addcom" id="commentform">
        					
                        <input type="hidden" name="gid" value="<?php echo $logid; ?>" />
        				<?php if(ROLE == ROLE_VISITOR): #如果是游客，显示下面表单?>

        				<textarea class="form-control comment-edit" name="comment" id="comment" rows="10" tabindex="4"></textarea>

        				<input type="hidden" name="pid" id="comment-pid" value="0" size="22" tabindex="1"/>

        				<ul class="comment-form">

            				<p class="alert alert-warning">以下带 <span style="color:red">*</span> 内容必须填写</p>
            				
            				<div class="form-group">
            					<label for="author" class="control-label"><span style="color:red">*</span>昵称：</label>
            					<input class="form-control" type="text" name="comname" maxlength="49" placeholder="请输入您的名字" value="<?php echo $ckname; ?>" size="12" tabindex="1">
            				</div>

            				<div class="form-group">
            					<label for="email" class="control-label">邮箱：</label>
            					<input class="form-control" type="text" name="commail"  maxlength="128" placeholder="请输入您的邮箱" value="<?php echo $ckmail; ?>" size="12" tabindex="2">
            				</div>
            			
            				<div class="form-group">
            					<label for="url" class="control-label">网站：</label>
            					<input class="form-control" type="text" name="comurl" maxlength="128" placeholder="请输入您的网站" value="<?php echo $ckurl; ?>" size="12" tabindex="3">
            				</div>

                            <?php 
                                if($verifyCode != '')
                                    {   
                                        # 图片验证码在include/controller/log_controller.php文件第93行,搜索//comments
                                        $yzm = '<label for="url" class="control-label"><span style="color:red">*</span>验证码：</label>
                                                <input class="form-control" name="imgcode" type="text" class="input" placeholder="请输入验证码" size="8" tabindex="5" />
                                                <img class="comment-verify" src="'.BLOG_URL.'include/lib/checkcode.php" align="absmiddle" style="cursor:pointer" onClick="this.src=this.src" title="点击刷新"/>';
                                        echo $yzm;
                                    }
                            ?>
            				<?php else: #结束游客内容，如果不是游客，必然是管理员了吧～所以else一下?>
                            <div class="alert alert-success" role="alert">这里可以添加评论表单，一般都是在后台回复的啦...如果你不想看到这段话，请到module.php搜索<span style="color:red;">"管理员评论界面"</span>删除这段div标签</div>
                            <?php endif;#结束判断循环，下面的提交按钮是共用的，如果管理员不想看到按钮，就丢到上面的验证码后面。?>
            				<div class="comment-submit div-inline">
            					<div class="comment-submits"><button class="btn btn-default btn-lg btn-block" type="submit" id="comment_submit" value="发表评论" tabindex="6" />发表评论</button></div>
            					<div class="comment-reset"><button class="btn btn-default btn-lg btn-block" type="reset" id="comment_reset" value="重新填写" tabindex="6" />重新填写</button></div>
            				</div>
        				</ul>
        			</form>
                </div>
            </div>
        </div>
    <?php }else {echo '<h2 class="alert alert-warning">这篇文章禁止评论!</h2>';}# 如果评论关闭,输出禁止评论,表单是被关闭的?>
<?php }?>

<?php 
//判断是否有评论，或者评论关闭，给出相应提示
function comunm_check($value){
	if ($value['allow_remark'] == 'y'){

		if ($value['comnum'] != 0)

			echo $value['comnum']; # 评论数量
						
		else
			echo '还没有评论';
		}

	else {
			echo '</a>禁止评论<a>';
	}
}
?>

<?php
//blog-tool:判断是否是首页
function blog_tool_ishome(){
    if (BLOG_URL . trim(Dispatcher::setPath(), '/') == BLOG_URL){
        return true;
    } else {
        return FALSE;
    }
}
?>
