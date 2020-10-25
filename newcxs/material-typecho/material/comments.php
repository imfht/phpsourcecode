<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<div id="comments">
    <?php $this->comments()->to($comments); ?>
    <?php if ($comments->have()): ?>
    <div class="alert alert-info">
        <span id="commentCount">
            <?php $this->commentsNum(_t('暂无评论'), _t('仅有一条评论'), _t('已有 %d 条评论')); ?>
        </span>
    </div>

    <?php $comments->listComments(array(
        'replyWord'=>'<button type="button" class="btn btn-danger btn-xs mdi-content-reply reply-button"></button>',
    )); ?>

    <?php $comments->pageNav('&laquo; ', '&raquo;',3,'...',array('wrapClass'=>'pagination','currentClass'=>'active')); ?>

    <?php endif; ?>

    <?php if($this->allow('comment')): ?>
        <div id="<?php $this->respondId(); ?>" class="respond panel panel-default">
            <div class="panel-body">
                <div class="cancel-comment-reply">
                    <a id="cancel-comment-reply-link" href="" rel="nofollow" style="display:none" onclick="return TypechoComment.cancelReply();">
                        <button type="button" class="btn btn-primary btn-xs btn-fab mdi-content-clear pull-right"></button>
                    </a>
                </div>

                <h3 id="response"><?php _e('添加新评论'); ?></h3>
                <form method="post" action="<?php $this->commentUrl() ?>" id="comment-form" role="form" class="form-horizontal">

                <?php if($this->user->hasLogin()): ?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label required"><?php _e('登录身份'); ?></label>
                        <div class="col-sm-9">
                            <a href="<?php $this->options->profileUrl(); ?>"><?php $this->user->screenName(); ?></a>. 
                            <a href="<?php $this->options->logoutUrl(); ?>" title="Logout">
                                <?php _e('退出'); ?> &raquo;
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="form-group">
                        <label for="author" class="col-sm-2 control-label required">昵称</label>
                        <div class="col-sm-9">
                            <input type="text" name="author" id="author"class="form-control text" value="" required />
                        </div>
                    </div>

                    <div class="form-group">
                    <label for="mail"class="col-sm-2 control-label required">Email</label>
                    <div class="col-sm-9">
                    <input type="email" name="mail" id="mail" class="form-control text" value="" required />
                    </div>

                    </div>

                    <div class="form-group">
                    <label for="url" class="col-sm-2 control-label ">网站</label>
                    <div class="col-sm-9">
                    <input type="url" name="url" id="url"class="form-control text" placeholder="http://" value=""  />
                    </div>
                    </div>
                <?php endif; ?>


                    <div class="form-group">
                        <label for="textarea" class="col-sm-2 control-label required">内容</label>
                        <div class="col-sm-9">
                            <textarea rows="9" cols="50" name="text" id="textarea" class="form-control textarea " required ></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-5" >
                            <button type="submit" id="submit" class="btn btn-success btn-raised submit">提交评论</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    <?php else: ?>
        <h3><?php _e('评论已关闭'); ?></h3>
    <?php endif; ?>
</div>
