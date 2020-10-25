

            {if $member['vertify']['status']==1}
            <div class="recommend bb">
   <div class="title">
     <i class="fa fa-renzheng"></i>
   <span  class="title_text">认证信息</span>

   </div>

  <div class="description">

    <div class="js-intro"  style="color:#908d08">
    {$member['vertify']['jieshao']}
    </div>


  </div>
  </div>
   {/if}

          <div class="recommend bb">
   <div class="title">
     <i class="fa fa-jieshao"></i>
   <span  class="title_text">个人介绍</span>

   </div>

  <div class="description">
    <div class="js-intro">
{if $member['introduction']}{$member['introduction']}{else}暂无介绍{/if}
    </div>


  </div>
  </div>

   </div>

