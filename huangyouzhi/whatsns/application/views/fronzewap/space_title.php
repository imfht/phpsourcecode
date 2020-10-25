<style>
<!--
.user-header-info{
height: 85px;
}
.mysignnature{
margin-top:5px;
color: #eee;
}
-->
</style>
<div class="user-header-info">
 <!--{if $is_followed}-->
          <button class="ui-btn ui-btn-danger btn-edit button_followed" onclick="attentto_user($member['uid'])" id="attenttouser_{$member['uid']}">
               
                 取消关注
            </button>
          <!--{else}-->
           <button class="ui-btn ui-btn-danger btn-edit button_attention" onclick="attentto_user($member['uid'])" id="attenttouser_{$member['uid']}">
               +关注
            </button>
               <!--{/if}-->
    <ul class="ui-row">
        <li class="ui-col ui-col-25">
            <div class="ui-avatar-lg" onclick="window.location.href='{url user/space/$member['uid']}'">
                <span style="background-image:url({$member['avatar']})"></span>
            </div>
        </li>
        <li class="ui-col ui-col-75">
              <p>
                  <span class="ui-txt-highlight user-name"  onclick="window.location.href='{url user/space/$member['uid']}'">
                  {$member['username']}
                   {if $member['author_has_vertify']!=false}<i class="fa fa-vimeo {if $member['author_has_vertify'][0]=='0'}v_person {else}v_company {/if}  " data-toggle="tooltip" data-placement="right" title="" {if $member['author_has_vertify'][0]=='0'}data-original-title="个人认证" {else}data-original-title="企业认证" {/if} ></i>{/if}
                  </span>
                 
              </p>
 <div class="mysignnature">{if $member['signature']}$member['signature']{else}这个人很懒，暂无签名信息{/if}</div>
 
                <ul class="ui-user-tiled">
                    <li  onclick="window.location.href='{url user/space_answer/$member['uid']}'"><div>{$member['answers']}</div><i style="border-bottom:solid 1px #ccc;">回答</i></li>
                    <li onclick="window.location.href='{url user/space_ask/$member['uid']}'"><div>{$member['questions']}</div><i style="border-bottom:solid 1px #ccc;">提问</i></li>
                    <li  onclick="window.location.href='{url topic/userxinzhi/$member['uid']}'"><div>{$member['articles']}</div><i style="border-bottom:solid 1px #ccc;">文章</i></li>
                     
                    
                    <li><div>{$member['followers']}</div><i>粉丝</i></li>
            
                </ul>

        </li>
       
        </ul>
</div>
<div class="user-basic-info" style="padding:10px;">

        {if $member['vertify']['status']==1}
         <div >认证信息:</div>
  <div class="description">
   
    <div class="js-intro" style="color:#908d08">
    {$member['vertify']['jieshao']}
    </div>
 

  </div>
     
   {/if}   
   
</div>