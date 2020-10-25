<!--{template header}-->
<section class="ui-container">
<!--{template user_title}-->
 <div class="messagepoerate">
                         <p>
                          
                          <button type="button" class="ui-btn ui-btn-primary mar-l-1 " onclick="javascript:document.location = '{url message/updateunread}'">清空未读消息</button>
                         <button type="button" class="ui-btn ui-btn-danger  mar-ly-1" onclick="javascript:document.location = '{url message/sendmessage}'">写消息</button>
                         </p>
         
                       
                        
                     </div>


     <ul class="tab-head">
                                         
                  
                      <li class="tab-head-item <!--{if $regular=="message/personal"}--> current<!--{/if}-->"><a href="{url message/personal}" title="私人消息">私人消息<span class="p-msg-count msgspan"></span></a></li>
                                                                               
                  
                      <li class="tab-head-item <!--{if $regular=="message/system"}--> current <!--{/if}-->"><a href="{url message/system}" title="系统消息">系统消息<span class="s-msg-count msgspan"></span></a></li>
                                                                               
                              

   
</ul>

  <form  class="form-horizontal message-form"  name="msgform" {if $type=='system'}action="{url message/remove}"{else}action="{url message/removedialog}"{/if} method="POST" onsubmit="javascript:if (!confirm('确定删除所选消息全部内容?')) return false;">
                                 <ul class="nav message-items">
                    <!--{loop $messagelist $message}-->
                    <li id="msg{$message['id']}" {if $message['new']}class="new"{/if}>
                      
                        <div class="row">
                          <div class="col-sm-1">
                            <!--{if $message['fromuid']}-->
                           <div class="avatar">
                            <a href="{url user/space/$message['fromuid']}" title="supermustang" target="_blank" class="pic"><img alt="{$message['from']}" src="{$message['from_avatar']}" /></a>
                        </div>
                          <!--{/if}-->
                          </div>
                          
                          <div class="col-sm-24">
                          
                           <div class="msgcontent hand">
                            <p class="font-12" style="font-weight:600;">
                                <!--{if $message['fromuid']==0}-->
                                <input type='checkbox' value="{$message['id']}" name="messageid[inbox][]"/>
                                {$message['subject']}
                                <!--{else}-->
                                <input type='checkbox' value="{$message['fromuid']}" name="message_author[]"/>
                                <a href="{url user/space/$message['fromuid']}">{$message['from']}</a> 对 <a href="{url user/score}">您</a> 说：
                                {$message['subject']}
                                <!--{/if}-->
                                
                                  <!--{if $message['new']==1}-->
                                       <span class="icon_hot">新</span>
                                                <!--{/if}-->
                                
                            </p>
                            <!--{if $type!='system'}-->
                            <div class="detail" style="cursor:pointer" onclick="javascript:document.location = '{url message/view/$type/$message[fromuid]/$message['id']}';">{eval echo cutstr(strip_tags($message['content']),300,'')}</div>
                            <!--{else}-->
                            <div class="detail" style="cursor:pointer">{$message['content']}</div>
                            <!--{/if}-->
                            <div class="related">
                                <div class="pv">{$message['format_time']}</div>
                            </div>
                        </div>
                          </div>
                        </div>
                       
                   
                      
                    </li>
                    <!--{/loop}-->
                </ul>
                {if $messagelist!=null}
                          <div class="row mar-t-1">   
                          <div class="col-sm-12">
                           <input type="checkbox" value="chkall" id="chkall" onclick="checkall('message');"/> 全选  <button type="submit"  name="submit" class="ui-btn ui-btn-danger mar-ly-1" >删除</button>
                  
                          </div>                 
                   
                    <div class="clearfix"></div>
                </div>
                {/if}
                          </form>
                            <div class="pages">{$departstr}</div>
</section>



<!--{template footer}-->