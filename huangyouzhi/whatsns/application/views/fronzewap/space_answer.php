
<!--{template header}-->
{if $member['expert']!=1}
<section class="ui-container">
<!--{template space_title}-->

   
    <section class="user-content-list">
            <div class="titlemiaosu">
            Ta的回答
            </div>
           <ul class="" style="padding:10px;">
   <!--{if $answerlist}-->
   
       <div class="stream-list question-stream ">
      <!--{loop $answerlist $question}-->
     
  
      <section class="stream-list__item">
        <div class="qa-rank"><div class="answers answered solved ml10 mr10">
                {$question['comments']}<small>评论</small></div></div> 
                   <div class="summary">
            <ul class="author list-inline">
                                           
                                                <li class="authorinfo">
                                          {if $question['hidden']==1}
                                            匿名用户
                      
                       {else} 
                              <a href="{url user/space/$question['authorid']}">
                          {$question['author']}{if $question['author_has_vertify']!=false}<i class="fa fa-vimeo {if $question['author_has_vertify'][0]=='0'}v_person {else}v_company {/if}  " ></i>{/if}
                          </a>
                      
                         {/if} 
                       
                        <span class="split"></span>
                        <a {if  !$question['hidden']} href="{url question/answer/$question['qid']/$question['id']}" {/if}>{$question['time']}</a>
                                    </li>
            </ul>
            <h2 class="title"><a {if  !$question['hidden']} href="{url question/answer/$question['qid']/$question['id']}" {/if}>{$question['title']}</a></h2>
 
                
              
                                   
                           
                                            </div>
    </section>

  
      
     
    <!--{/loop}-->
     </div>
      <!--{else}-->
       
            <div class="text">
            真不巧，作者还没回答任何问题~
          </div>
          <!--{/if}-->
                    
     
   


   
   

   


   
   

</ul>
  <div class="pages" >{$departstr}</div>    
    </section>
</section>
{else}
<section class="ui-container">
<header class="profile menuitem">
		<div class="avatar member">
			<span class=" head-v"> <img src="{$member['avatar']}" width="75" height="75" alt="奋斗">
			</span> 
			<br>{$member['username']} 
					<br><i>{$member['signature']}</i>
							<br><p>{$member['introduction']}</p>
				</div>
				 <!--{if $is_followed}-->

             	<span class="add-focus  button_attention btn-default following" onclick="attentto_user($member['uid'])" id="attenttouser_{$member['uid']}">
             	<i class="fa fa-check"></i><span>已关注</span>
             	</span>
             
          <!--{else}-->
     
            	<span class="add-focus button_attention btn-success follow" onclick="attentto_user($member['uid'])" id="attenttouser_{$member['uid']}">+关注</span>
             
               <!--{/if}-->
               
				
			</header>

</section>
<section class="my-favorite back-f">
		<a href="javascript:;" class="myasnwer">回答<span>{$member['answers']}</span></a>
		<a href="javascript:;" class="caina">采纳率<span>{eval echo $this->user_model->adoptpercent ( $member );}%</span></a>
		<a href="javascript:;" class="zan">获赞<span>{$member['supports']}</span></a>
	</section>
	
	<section class="questions back-f">
		<textarea placeholder="请输入问题" id="quicktitle" maxlength="80"></textarea>
		{if $member['mypay']>0}
	<a {if $user['uid']}href="javascript:freeask();"{else}href="{url user/login}"{/if}>$member['mypay']元咨询</a>
		
		{else}
		
				<a {if $user['uid']}href="javascript:freeask();"{else}href="{url user/login}"{/if}>免费咨询</a>
		
		{/if}
	</section>
	
		<section class="answerlists back-f">
	<h4 class="pl">
			<span>回答<font color="#ff7900">{$member['answers']}</font>个问题
			</span>{if $member['jine']}&nbsp;&nbsp;&nbsp;<span>共收入<font color="#ff7900">{eval echo $member['jine']/100;}</font>元 {/if}
			</span>&nbsp;&nbsp;&nbsp;<span>获得<font color="#ff7900">$member['credit2']</font>财富值
				</span>
		</h4>
		    <div class="whatsns_list" style="background:#F6F6F6">
		    
                       <!--{loop $answerlist $question}-->
                       
                        <div class="whatsns_listitem">
         <div class="l_title"><h2><a {if  !$question['hidden']} href="{url question/answer/$question['qid']/$question['id']}" {/if}>
      {$question['title']}{if $question['price']}<label class="tit-money">奖励$question['price']财富值</label>{/if}{if $question['shangjin']}
              
                  <span  class="icon_hot"><i class="fa fa-hongbao mar-r-03"></i>悬赏$question['shangjin']元</span>
               
                {/if}</a></h2></div>

       <div class="whatsns_content" style="margin-top: 10px;">
  
 

 
 
        {if  !$question['hidden']} 
 <div class="whatsns_des" >
 {if !$question['reward']}
 <span class="mtext" >{$question['content']}</span>
  <div class="whatsns_readmore" onclick="window.location='{url question/answer/$question['qid']/$question['id']}'" >查看更多<i class="fa fa-angle-down"></i></div>
 
 {else}
  <span class="mtext">
  <a class="thiefbox font-12" target="_self"><i class="fa fa-lock font-12"></i> &nbsp;{$question['reward']}元查看回答</a>
  </span>
  {/if}

 </div>
         {/if}
     <div class="ask-bottom">

          <a    {if  !$question['hidden']}  href="{url question/answer/$question['qid']/$question['id']}"    {/if} class="" ><i class="fa fa-commentingicon"></i>{$question['comments']} 评论</a>
          <a    {if  !$question['hidden']}  href="{url question/answer/$question['qid']/$question['id']}"    {/if}  class=" ">{$question['supports']}个赞</a>
     </div>
       </div>

              </div>
                          <!--{/loop}-->   
		    </div>
		      <div class="pages" >{$departstr}</div>    
	</section>
	
{/if}
{if $user['uid']}
<script type="text/javascript">
var subing=false;
function freeask(){
	if(subing){
return false;
	}
	var _quicktitle=$.trim($("#quicktitle").val());
	var _quickcid="{$member['category'][0]['cid']}";
	if(_quicktitle==''){
		alert("咨询内容不能为空");
		return false;
	}
	if(_quicktitle.length<5){
		alert("咨询内容最少5个字");
		return false;
	}
	var data={
			quicktitle:_quicktitle,
			quickcid:_quickcid,
			askfromuid:"{$member['uid']}"
			}
	var _url="{url question/ajaxquickadd}";
	function success(result){
		subing=false;
		if(result.message=="ok"){
        window.location.href=result.url;
		}else{
alert(result.message);
		}
  
	}
	subing=true;
	
	ajaxpost(_url,data,success);
}
</script>
{/if}
<!--{template footer}-->