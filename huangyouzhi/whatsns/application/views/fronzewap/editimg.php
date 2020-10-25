<!--{template header}-->
<section class="ui-container">
<!--{template user_title}-->
<div style="margin:5px;">
    <!--{if isset($imgstr)}-->
                {$imgstr}
                <!--{else}-->
                <p> 说明：</p>
                <ul class="nav">
                <li>
                  1、支持jpg、gif、png、jpeg四种格式图片上传
                </li>
                 <li>
                   2、图片大小不能超过2M;
                </li>
                 <li>
                3、图片长宽大于165*165px时系统将自动压缩
                </li>
                </ul>
        
 <form class="form-horizontal"  action="{url user/editimg/$user['uid']}" method="post"  enctype="multipart/form-data">
  <div class="form-group">
          
          <div class="col-md-24 mar-t-1">
      
           <img class="avatar" alt="{$user['username']}" src="{$user['avatar']}"  style="width:80px;height:80px;border-radius:80px;margin:10px auto;"/>
        
             
          </div>
            <div class="col-md-24 mar-t-1" style="clear:both;text-align:left;">
             <input accept="image/*" style="clear:both;display:block;"  id="file_upload" name="userimage" type="file"/>
                            <button type="submit" name="uploadavatar" id="uploadavatar" style="margin:30px auto;display:block;width:100%;height:40px;" class="ui-btn ui-btn-success " >
                            上传头像 </button>
                           
          </div>
        </div>
 </form>
                 <!--{/if}-->
                 </div>
</section>
 
<!--{template footer}-->

