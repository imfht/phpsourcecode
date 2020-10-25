<{include file="public/header.tpl"}>
<body>
<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>用户</h3>
  <div class="form-group">
    <label>
      用户ID:<{$user.id}>
    </label>
  </div>
  <div class="form-group">
    <label for="InputVName">用户姓名：<{$user.name}></label>
  </div>
    <div class="form-group">
    <label for="InputVName">性别：<{if $user.sex==1}>男<{else}>女<{/if}></label>
  </div>
  <div class="form-group">
    <label for="InputVName">爱好：<{if $user.hobby==''}>空<{else}><{$user.hobby}><{/if}></label>
  </div>
  <div class="form-group">
    <label for="InputVName">电话：<{if $user.tel==''}>空<{else}><{$user.tel}><{/if}></label>
  </div>
  <div class="form-group">
    <label for="InputVName">电子邮箱：<{if $user.email==''}>空<{else}><{$user.email}><{/if}></label>
  </div>
  <div class="form-group">
    <label for="InputVName">状态：<{if $user.allow==1}>正常<br><a href="<{$smarty.const.__CONTROLLER__}>/allow/allow/1/id/<{$user.id}>">冻结</a><{else}>冻结<br><a href="<{$smarty.const.__CONTROLLER__}>/allow/allow/0/id/<{$user.id}>">恢复</a><{/if}></label>
  </div>

</form>
</div>
</div>
</body>
<{include file="public/footer.tpl"}>