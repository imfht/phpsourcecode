<!--{template header}-->
<section class="ui-container">
<!--{template user_title}-->

 <div class="messagepoerate">
 <p>
 <button type="button" class="ui-btn ui-btn-primary mar-l-1 " onclick="javascript:document.location = '{url message/updateunread}'">清空未读消息</button>
 <button type="button" class="ui-btn ui-btn-danger  mar-ly-1" onclick="javascript:document.location = '{url message/sendmessage}'">写消息</button>
 </p>
</div>
<ul class="ui-tab-nav ui-border-b message-tab" >
        <li <!--{if $regular=="message/personal"}--> class="current"<!--{/if}-->>
        <a href="{url message/personal}">私人消息<span class="p-msg-count icon_hot"></span></a>
        </li>
                    <li <!--{if $regular=="message/system"}--> class="current"<!--{/if}-->>
                 
                   <a href="{url message/system}">系统消息<span class="s-msg-count icon_hot"></span></a>
                    </li>
                   
</ul>                    
   <form class="form-horizontal message-form"   name="msgform" action="{url message/remove}" method="POST" onsubmit="javascript:if (!confirm('确定删除所选消息?')) return false;">
                        
                                 <ul class="nav message-items">
                     <!--{loop $messagelist $message}-->
                    <li>
                    <div class="row">
                    
                    <div class="col-sm-2">
                     <!--{if $message['fromuid']}-->
                        <div class="avatar">
                            <a href="{url user/space/$message['fromuid']}" title="supermustang" target="_blank" class="pic"><img width="48px" height="48px" class="img-rounded" alt="{$message['from']}" src="{$message['from_avatar']}"/></a>
                        </div>
                        <!--{/if}-->
                    </div>
                    
                     <div class="col-sm-22">
                     <div class="msgcontent">
                            <p class="font-12" style="font-weight:600">                                
                                <!--{if $message['fromuid']==$user['uid']}-->
                                <input type='checkbox' value="{$message['id']}" name="messageid[outbox][]"/>
                                <a href="{url user/score}">您</a> 对 <a href="{url user/space/$message['fromuid']}">{$message['touser']['username']}</a> 说：
                                <!--{else}-->
                                <input type='checkbox' value="{$message['id']}" name="messageid[inbox][]"/>
                                <a href="{url user/space/$message['fromuid']}">{$message['from']}</a> 对 <a href="{url user/score}">您</a> 说：
                                <!--{/if}-->
                                {$message['subject']}
                            </p>
                            <p class="news_content">{$message['content']}</p>
                            <div class="related">
                                <div class="pv">{$message['format_time']}</div>
                            </div>
                        </div>
                    </div>
                    </div>
                       
                       
                        <div class="clr"></div>
                    </li>
                    <!--{/loop}-->
                </ul>
                
                          <div class="row mar-t-1">   
                          <div class="col-sm-24">
                           <input type="checkbox" value="chkall" id="chkall" onclick="checkall('message');"/> 全选  <button type="submit"  name="submit" class="btn btn-danger" >删除</button>
                  
                          </div>                 
                   
                    <div class="clearfix"></div>
                </div>
                          </form>
                          
                             <!--{if 'personal'==$type}-->
                             
                               <div class="row mar-t-1">
                               <div class="col-sm-24">
                               
                                     <ul class="nav message-items">
                <form class="form-horizontal message-form"   name="commentform" action="{url message/sendmessage}" method="POST" onsubmit="return check_form();">
                    <li>
                    <div class="row">
                    
                     <div class="col-sm-22">
                       
                        <div class="msgcontent">
                           
                  <!--{template editor}-->
                  
                  
                            <div class="row mar-t-1 messagebtnblock">
                            
                              <!--{if $setting['code_message']=='1'}-->
                                  <!--{template code}-->
        
                      <!--{/if}-->
                           
            
                               <button type="submit"  class="ui-btn ui-btn-danger " name="submit">提&nbsp;交</button>
                              
                                <input type="hidden" name="username" value="{$fromuser['username']}" />
                               
                            </div>
                        </div>
                    </div>
                    </div>
                    
                        <div class="clr clear"></div>
                    </li>
                </form>
            </ul>
                               </div>
                               
                               </div>
                               <!--{/if}-->
                          
                            <div class="pages">{$departstr}</div>



<script type="text/javascript">
function check_form() {
    if ($.trim(UE.getEditor('content').getPlainTxt()) == '') {
        alert("消息内容不能为空!");
        return false;
    }
    return true;
}
</script>
</section>
<!--{template footer}-->