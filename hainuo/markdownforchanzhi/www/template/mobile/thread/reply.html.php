<?php if(!defined("RUN_MODE")) die();?>
<div class='panel panel-section' id='repliesListWrapper'>
  <div id='repliesList' class='panel-body cards cards-list'>
    <?php $i = 2 + ($pager->pageID - 1) * $pager->recPerPage;?>
    <?php foreach($replies as $reply):?>
    <div class='card thread reply' id='<?php echo $reply->id;?>'>
      <div class='card-heading'>
        <div class='pull-right'>
          <?php if($i > 3):?>
          <strong class='level-number'>#<?php echo $i; ?></strong>
          <?php elseif($i === 2): ?>
          <strong class='level-number'><?php echo $lang->reply->sofa;?></strong>
          <?php elseif($i === 3): ?>
          <strong class='level-number'><?php echo $lang->reply->stool;?></strong>
          <?php endif; ?>
        </div>
        <div><span class='reply-time'><i class='icon-comment-alt'></i> <?php echo $reply->addedDate;?></span> &nbsp;&nbsp; <span class='reply-user<?php if($this->app->user->account == $reply->author) echo ' text-danger'; ?>'><i class='icon-user'></i> <?php echo isset($speakers[$reply->author]) ? $speakers[$reply->author]->realname : $reply->author ?></span></div>
      </div>
      <section class='card-content article-content'><?php echo $reply->content;?></section>
      <?php if(!empty($reply->files)):?>
      <div class='card-content'><?php $this->reply->printFiles($reply, $this->thread->canManage($board->id, $reply->author));?></div>
      <?php endif;?>
      <div class='card-footer'>
        <?php if($reply->editor): ?>
        <small class='hide last-edit'><i class="icon-pencil"></i> <?php printf($lang->thread->lblEdited, $reply->editorRealname, $reply->editedDate); ?></small>
        <?php endif; ?>
        <div class='actions text-right'>
          <?php if($this->app->user->account != 'guest'): ?>
            <?php if($this->thread->canManage($board->id)) echo html::a($this->createLink('reply', 'delete', "replyID=$reply->id"), '<i class="icon-trash"></i> ' . $lang->delete, "class='deleter text-muted'") . ' &nbsp; ';?>
            <?php if($this->thread->canManage($board->id, $reply->author)) echo html::a($this->createLink('reply', 'edit',   "replyID=$reply->id"), '<i class="icon-pencil"></i> ' . $lang->edit, "data-toggle='modal' class='text-muted'") . ' &nbsp; '; ?>
            <?php if(!$thread->readonly):?>
            <a href='#replyDialog' data-toggle='modal' class='text-muted thread-reply-btn'><i class='icon-reply'></i> <?php echo $lang->reply->common;?></a>
            <?php endif; ?>
          <?php else: ?>
            <?php if(!$thread->readonly):?>
            <a href="<?php echo $this->createLink('user', 'login', 'referer=' . helper::safe64Encode($this->app->getURI(true))); ?>#reply" class="thread-reply-btn text-muted"><i class="icon-reply"></i> <?php echo $lang->reply->common;?></a>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php $i++;?>
    <?php endforeach;?>
    <?php $pager->show('justify');?>
    <hr class='space' id='bottomSpace'>
  </div>
</div>

<?php if(!$thread->readonly):?>
<div class='modal fade' id='replyDialog'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header'>
        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>×</span></button>
        <h5 class='modal-title'><i class='icon-reply'></i> <?php echo $lang->reply->common; ?></h5>
      </div>
      <div class='modal-body'>
        <form method='post' enctype='multipart/form-data' id='replyForm' action='<?php echo $this->createLink('reply', 'post', "thread=$thread->id");?>'>
          <div class='form-group' id='reply'>
            <?php echo html::textarea('content', '', "rows='6' class='form-control' placeholder='{$lang->reply->content}'"); ?>
          </div>
          <div class="form-group clearfix captcha-box">
          <?php if(zget($this->config->site, 'captcha', 'auto') == 'open'):?>
               <div class='form-group clearfix' id='captchaBox'><?php echo $this->loadModel('guarder')->create4reply();?></div>
            <?php else:?>
               <div class='form-group clearfix' id='captchaBox' style='display:none;'></div>
            <?php endif;?>
          </div>
          <div class='form-group'><?php echo html::submitButton('', 'btn primary block');?></div>
          <?php
          echo html::hidden('recTotal',   $pager->recTotal);
          echo html::hidden('recPerPage', $pager->recPerPage);
          echo html::hidden('pageID',     $pager->pageTotal);
          ?>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
$(function()
{
    $.refreshRepliesList = function()
    {
        $('#repliesListWrapper').load(window.location.href + ' #repliesList', function()
        {
            $(window).scrollTop($('#bottomSpace').offset().top);
        });
    };

    var $replyForm = $('#replyForm');
    $replyForm.ajaxform({onResultSuccess: function(response)
    {
        response.locate = false;
        $('#replyDialog').modal('hide');
        $.refreshRepliesList();
        $replyForm.find('#content').val('');
        if(response.reason == 'needChecking')
        {
            $replyForm.find('.captcha-box').html(Base64.decode(response.captcha)).removeClass('hide');
        }
    }});
});
</script>
<?php endif;?>
