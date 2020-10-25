<!--{template header}-->
 <link href="{SITE_URL}static/css/widescreen/css/wen.css" rel="stylesheet">
 <div class="QAcon1 part clearfix mb30">
{template index_lunbo}
<div class="th-b fr">
<p class="tips">
<a href="{url seo/index}" target="_blank">最新资讯</a>
</p>
{eval $newtopiclist=$this->fromcache('topiclist');}
{if $newtopiclist}
<p class="pTit">
<a href="{url topic/getone/$newtopiclist[0]['id']}" target="_blank" title="$newtopiclist[0]['title']">{eval echo clearhtml($newtopiclist[0]['title'],15);}</a>
</p>
<p class="des">
{eval echo clearhtml($newtopiclist[0]['description'],60);}
</p>
<ul class="titList">
{loop $newtopiclist $index $topic}
{if $index>=1&&$index<=3}
<li>
<a href="{url topic/getone/$topic['id']}" target="_blank" class="f14">{$topic['title']}</a>
</li>
{/if}
{/loop}
</ul>
{/if}
</div>
</div>
<div class="wdmain QAcon2 mb30">
	<!--  最新一期专家访谈 -->
	
       <div class="left665">
	  <!-- 顶置内容 -->
		
        <div class="lBox part QAview" id="Jslide2">
			
			<div class="lBox-th">
				<h3 class="mark">顶置</h3>
				<span class="submark slide-tab2">
					
				</span>
			</div>
			<div class="lBox-tb PicTxt">
						
			<div class="slideContent">
			<div class="c" style="display: block;">
					{eval $topdatalist=$this->fromcache('topdata');}
					{loop $topdatalist $index $topdata}
					<!-- 最多取3项顶置,$index索引从0开始，<=2表示显示三项 -->
					{if $index<=2}
						<div class="oh">
							<p class="pTitle">
								<a href="{$topdata['url']}" target="_blank" title="{$topdata['title']}">
								{$topdata['title']}</a>
							</p>
							<p class="pTxt">
								<em>简介：</em>{eval echo clearhtml($topdata['description'],80);}
							</p>
							<p class="pMore">
								<a href="{$topdata['url']}" target="_blank" rel="nofollow">查看详情</a>
							</p>
							
						</div>
						{/if}
					      {/loop}
					</div>
					
				</div>
			</div>
			
		</div>
      </div>
    
	<!-- 广告位 -->
	<div class="right305">
		
		<div class="ivy295">
		
		{template index_adv}
		</div>
	</div>
	</div>
	<!-- 第三段 -->
	<div class="wdmain QAcon3">
		<div class="left665">
		<div class="lBox part mb30" id="Jslide">
			<div class="lBox-th">
				<h3 class="mark">最新回复</h3>
				<span class="submark slide-tab">
					<i class="toc current">全部</i>
					<i class="toc ">已采纳</i>					
				</span>
			</div>
			<div class="lBox-tb slide-con QA-New-Con">
				<div class="slideContent">
					
					
					
					
					
				<div class="slideContent">
				<div class="c" style="display: block;">
						<ul class="QA-New lUlList">
							{eval $answerlist=$this->getlistbysql("SELECT * FROM ".$this->db->dbprefix."answer WHERE status!=0  group by qid order by id desc LIMIT 0,25");}
						{loop $answerlist $answer}
							<li>
								<div class="pQ">
									<em class="icons"></em>
									<em class="fr lgray">{echo tdate($answer['time']);}</em>
									<a href="{url question/view/$answer['qid']}" target="_blank" title="{$answer['title']}">{eval echo clearhtml($answer['title'],25);}</a>
								</div>
								<div class="pA">
									<em class="icons"></em>
									<p class="pTxt">{eval echo clearhtml($answer['content'],40);}</p>
								</div>
							</li>
							{/loop}
						
							
						</ul>
					</div>
				<div class="c"  style="display: none;">
						<ul class="QA-New lUlList">
							
										{eval $answerlist=$this->getlistbysql("SELECT * FROM ".$this->db->dbprefix."answer WHERE adopttime>0  group by qid order by id desc LIMIT 0,25");}
						{loop $answerlist $answer}
							<li>
								<div class="pQ">
									<em class="icons"></em>
									<em class="fr lgray">{echo tdate($answer['time']);}</em>
									<a href="{url question/view/$answer['qid']}" target="_blank" title="{$answer['title']}">{eval echo clearhtml($answer['title'],25);}</a>
								</div>
								<div class="pA">
									<em class="icons"></em>
									<p class="pTxt">{eval echo clearhtml($answer['content'],40);}</p>
								</div>
							</li>
							{/loop}
							
							
						</ul>
					</div>	
					
					</div>
					
					</div>
			</div>
		</div>
		
		
		</div>
		<div class="right305">
		{template index_rightadv1}
        <!--专家问答-->
        
			<div class="rBox part QA-professorQa mb30">
			 <div class="rBox-th">
				<h3 class="mark">专家问答</h3>
			</div>
			 <div class="rBox-tb pb20">
				 <ul class="ulList">
					 			{eval $answerlist=$this->getlistbysql("SELECT * FROM ".$this->db->dbprefix."answer WHERE authorid in (select uid from ".$this->db->dbprefix."user where expert=1 )  group by authorid order by adopttime desc LIMIT 0,3");}
						{loop $answerlist $answer}
							
								 <li>
							 <a href="{url question/answer/$answer['qid']/$answer['id']}" target="_blank" title="{$answer['title']}">
							 <p class="pQ">
								{eval echo clearhtml($answer['title'],25);}
							 </p>
							 <p class="pTxt">
								{eval echo clearhtml($answer['content'],40);}
							 </p>
						 	 </a>
						 </li>
							{/loop}
					
					 
				
					 
				 </ul>
				  <a href="{url expert/default}" class="aMore" target="_blank" rel="nofollow">查看更多专家信息</a>
			 </div>
		  </div>
		
		<!-- 专家问答 E -->
		<!-- 专家团  -->
		
        <div class="rBox part QA-professor mb30" id="Jslide3">
		
		<div class="rBox-th">
			<h3 class="mark"><a href="{url expert/default}" target="_blank">专家团</a></h3>
		
		</div>
		<div class="rBox-tb slide-con pb20">
			
			
			
			
		<div class="slideContent"><div class="c" style="display: block;">
				<ul class="ulList ulPicTxt clearfix">
				{eval $expertlist=$this->getlistbysql("select uid,username,expert,regtime,signature from ".$this->db->dbprefix."user where expert=1 order by regtime asc limit 0,6;");}
					{loop $expertlist $expert}
					<li>
						<a href="//kuaiwen.pcbaby.com.cn/48921652/" target="_blank" rel="nofollow">
							<img src="{eval echo get_avatar_dir($expert['uid']);}" width="80" height="80">
							<em>
								<span>{$expert['username']}</span>
								<i title="$expert['signature']">{if $expert['signature']}{echo clearhtml($expert['signature'],8);}{else}暂未设置签名{/if}</i>
							</em>
						</a>
					</li>
				{/loop}
					
				
				</ul>
			</div>
			</div></div>
		
		</div>
		
		<!-- 财富周达人榜-->
		
		 <div class="rBox part QA-week recommend mb30">
			<div class="rBox-th">
				<h3 class="mark">财富周达人榜</h3>
				<span class="submark">财富值</span>
			</div>
			<div class="rBox-tb recommend-tb recommend-headPic">
				<ul>
					 {eval $weekuserlist=$this->fromcache('alluserlist');}
                {loop $weekuserlist $index $alluser}
                {eval $index++;}
                {if $index<=5}
						<li class="reNum reNum-$index first">
							<a rel="nofollow" href="{url user/space/$alluser['uid']}" target="_blank"><em class="rePic">
								<img src="{eval echo get_avatar_dir($alluser['uid']);}" width="50" height="50">
							</em><span class="fr">{$alluser['credit2']}</span>{$alluser['username']}</a>
						</li>
					{/if}
					{/loop}
				</ul>
			</div>
		</div>
		
		<!-- 精彩推荐 S -->
		<div class="rBox part recommend mb30">
			<div class="rBox-th">
				<h3 class="mark">周热门文章</h3>
			</div>
			<div class="rBox-tb recommend-tb">
				<ul class="ulList">
				{eval $weektopiclist = $this->fromcache("weektopiclist");}
				{loop $weektopiclist $nindex $topic}
<li class="first ">
<a href="{url topic/getone/$topic['id']}" target="_blank"  class="aPointLink" title="{$topic['title']}">{eval echo clearhtml($topic['title'],18);}</a>
</li>
{/loop}
</ul>
			</div>
		</div>
		<div class="rBox part recommend QA-intf" id="Jslide4">
		<div class="rBox-tab slide-tab">
			<a href="javascript:;" target="_blank" class="toc current">最新问题<em></em></a>
			<a href="javascript:;" target="_blank" class="toc">推荐文章<em></em></a>
		</div>
		<div class="rBox-tb recommend-tb slide-con">
			

				
				
		<div class="slideContent"><div class="c" style="display: block;">
<ul class="ulList">
{eval $newnwentilist=$this->getlistbysql("select * from ".$this->db->dbprefix."question where status!=0 order by time desc limit 0,10");}
{loop $newnwentilist $question}
<li>
<a href="{url question/view/$question['id']}" class="aPointLink" target="_blank" title="{$question['title']}">{eval echo clearhtml($question['title'],15);}</a>
</li>					
{/loop}			
</ul>
</div><div class="c" style="display: none;">
					<ul>
						{eval $tuijianwenzhanglist=$this->getlistbysql("select * from ".$this->db->dbprefix."topic where ispc=1 order by viewtime desc limit 0,10");}
{loop $tuijianwenzhanglist $article}
<li class="first">
							<a href="{url topic/getone/$article['id']}" target="_blank" title="{$article['title']}">{eval echo clearhtml($article['title'],15);}</a>
						</li>
										
{/loop}	
						
						
					</ul>
				</div></div></div>
		</div>
		{template index_rightadv2}
		</div>
	</div>
{if $setting['weixin_logo']}
<div class="side-weixin-box">
  <div class="weixin-box-c"> 
    <div class="close" onclick="this.parentNode.parentNode.style.display='none';"></div>
    <div class="content"> 
      <p>关注问答微信公众号</p>
      <img src="{$setting['weixin_logo']}" alt="扫一扫，关注我们" width="111" height="111"> 
      
    </div> 
  </div>
</div>
{/if}
<script src="//js.3conline.com/min/temp/v1/dpl-jquery.slide.js"></script>
<script src="//js.3conline.com/common/lazy-min.js"></script>
	<script>
		/*首屏tab*/
		window.slide01 = new Slide({
			target: $('#slide01 .c'),
			control: $('#slide01 .control a'),
			autoPlay: true,
			stay:4000,
			onchange:function(){
				var _target = this.target[this.curPage],
				imgs = _target.getElementsByTagName("img");
				if(!imgs) return;
					for(var i=0,len=imgs.length;i<len;i++){
					var img = imgs[i],src;
					(src = img.getAttribute('#src1')) && (img.src=src,img.removeAttribute("#src1"));
				} 
			}
		});
		/*按需执行js*/
		var jsList_ = [
			{id:"Jslide",js:function(){
				new Slide({
					target: $( '#Jslide .c' ),
					control: $( '#Jslide .toc')
				});
			}},
		
			{id:"Jslide3",js:function(){
				window.slide03 = new Slide({
					target: $('#Jslide3 .c'),
					control: false,
					onchange: function(){
						$("#Jslide3-cur").html(this.curPage + 1)
					}
				});
				$("#Jslide3 .slide-prev").click(function(e){
					slide03.prev();
					e.preventDefault();
				});
				$("#Jslide3 .slide-next").click(function(e){
					slide03.next();
					e.preventDefault();
				});
			}},
			{id:"Jslide4",js:function(){
				new Slide({
					target: $('#Jslide4 .c'),
					control:  $('#Jslide4 .toc')
				}); 
			}},
			{id:"Jbaike",js:function(){
				new Slide({
					target: $('#Jbaike .c'),
					control:  $('#Jbaike .toc')
				}); 
			}},
			{id:"Jseo",js:function(){
				new Slide({
					target: $('#Jseo .c'),
					control:  $('#Jseo .toc')
				}); 
			}}
		]
		
		var xx = Lazy.create({
		lazyId:"Jbody",
		jsList:jsList_,
		trueSrc:'#src'
		});
		Lazy.init(xx);
		
    </script>
    <!--{template footer}-->