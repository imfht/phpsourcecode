/**
 * Created by 95 on 3/21/14.
 */
/*从index拿过来的*/

function isLoadMoreVisible() {
    var visibleHeight = $(window.top).height();
    var loadMoreOffset = $('#load_more').offset();
    return visibleHeight + $(window).scrollTop() > loadMoreOffset.top;
}

function loadNextPage() {
    if(loadCount ==3){
        $('#index_weibo_page').show();
    }
    if(currentPage==1 && loadCount < 3){
        loadCount++;
        loadWeiboList(currentPage);
    }

    if(currentPage>1){
        loadWeiboList(currentPage);
    }



}

function reloadWeiboList() {
    loadCount =1;
    loadWeiboList(1, function () {
        clearWeiboList();
        currentPage = 1;
    });
}


function loadWeiboList(page, onBeforePrepend) {
    //默认载入第1页
    if (page == undefined) {
        page = 1;
    }
    //通过服务器载入微博列表
    isLoadingWeibo = true;
    $('#load_more_text').text('正在载入...');
    $.post(url, {page: page,loadCount:loadCount,lastId:lastId}, function (a) {
        if (a.status == 0) {
            noMoreNextPage = true;
            $('#load_more_text').text('没有了');
        }
        if (onBeforePrepend != undefined) {
            onBeforePrepend();
        }
        $('#weibo_list').append(a);
        $('#load_more_text').text('');
        isLoadingWeibo = false;
        bindRepost();
    });
}

function clearWeiboList() {
    currentPage = 0;
    $('#weibo_list').html('');
}
/*从index拿过来的end*/




$(function () {


    $(document).on('click','.weibo-set-post',function(){
        var weiboId=$(this).attr('data-weibo-id');
        $.post(U('weibo/index/settop'),{weibo_id:weiboId},function(msg){
           if(msg.status){
               toast.success(msg.info);
               setTimeout('location.reload()',1500);
           } else{
               toast.error(msg.info);
           }
        });
    });
    /**
     * 点击评论按钮后提交评论
     */
    $(document).on('click', '.weibo-comment-commit', function () {
        var weiboId = $(this).attr('data-weibo-id');
        var weibo = $('#weibo_' + weiboId);
        var content = $('.weibo-comment-content', weibo).val();
        var url = U('Weibo/Index/doComment');
        var commitButton = $('.weibo-comment-commit', weibo);
        var weiboCommentList = $('.weibo-comment-list', weibo);
        var originalButtonText = commitButton.text();
        commitButton.text('正在发表...').attr('class', 'btn btn-primary disabled');
        var weiboToCommentId = $('#weibo-comment-to-comment-id', weibo);
        comment_id = weiboToCommentId.val();
        $.post(url, {weibo_id: weiboId, content: content, comment_id: comment_id}, function (a) {
            handleAjax(a);
            if (a.status) {
                reloadWeiboCommentList(weiboCommentList);
                weiboCommentList.attr('data-weibo-comment-loaded', '1');
                var weiboId = weiboCommentList.attr('data-weibo-id');
                var weibo = $('#weibo_' + weiboId);
                var weiboContainer = $('.weibo-comment-container', weiboCommentList);
                var url = U('Weibo/Index/commentlist');
                $.post(url, {weibo_id: weiboId}, function (a) {

                    $('#show_comment_'+weiboId).html(a);
                    $('#btn_showall').hide()
                    var commentLinkText = $('.operation', weiboContainer).html();
                    $('.operation', weibo).html(commentLinkText);
                    commitButton.text(originalButtonText);
                    commitButton.attr('class', 'btn btn-primary weibo-comment-commit');
                    $('.weibo-comment-content', weibo).val('');
                });



            } else {
                commitButton.text(originalButtonText);
                commitButton.attr('class', 'btn btn-primary weibo-comment-commit');
            }
        });
    });

    /**
     * 点击删除后删除微博
     */
    $(document).on('click', '.weibo-comment-del', function (e) {
        var weibo_id = $(this).attr('data-weibo-id');
        var $this = $(this);
        $.post(U('Weibo/Index/doDelWeibo'), {weibo_id: weibo_id}, function (msg) {
            if (msg.status) {
                $this.parent().parent().parent().parent().parent().next().fadeOut();
                $this.parent().parent().parent().parent().parent().fadeOut();
                toast.success('删除微博成功。', '温馨提示');
            }
        }, 'json');
        //取消默认动作
        e.preventDefault();
        return false;
    });

    /**
     * 点击评论之后载入评论
     */
    $(document).on('click', '.weibo-comment-link', function (e) {
        var weibo_id = $(this).attr('data-weibo-id');
        var weiboCommentList = $('#weibo_' + weibo_id + ' .weibo-comment-list');
        if (weiboCommentList.is(':visible')) {
            hideWeiboCommentList(weiboCommentList);
        } else {
            showWeiboCommentList(weiboCommentList);
        }
        //取消默认动作
        e.preventDefault();
        return false;
    });
    if (typeof(auto_open_detail) != 'undefined') {
        $('.weibo-comment-link').click();

    }
});


/*微博应用使用的js*/

/**
 * 评论微博
 * @param obj
 * @param comment_id 评论ID
 */
function weibo_reply(obj, comment_id, comment_object) {
    var weiboId = $(obj).attr('data-weibo-id');

    var weibo = $('#weibo_' + weiboId);
    var textarea = $('.weibo-comment-content', weibo);
    var content = textarea.val();
    var weiboToCommentId = $('#weibo-comment-to-comment-id', weibo);
    weiboToCommentId.val(comment_id);
    textarea.focus();
    textarea.val('回复 @' + comment_object + ' ：');
}
/**
 * 评论微博
 * @param obj
 * @param comment_id 评论ID
 */
function comment_del(obj, comment_id) {
    var url = U('Weibo/Index/doDelComment');
    var $this = $(obj);
    $.post(url, {comment_id: comment_id}, function (msg) {
        if (msg.status) {
            var weiboId = $this.attr('data-weibo-id');
            var weibo = $('#weibo_' + weiboId);
            var weiboCommentList = $('.weibo-comment-list', weibo);
/*      reloadWeiboCommentList(weiboCommentList);*/
            $this.parents('.weibo_comment').prev().fadeOut()
            $this.parents('.weibo_comment').fadeOut()
            toast.success('删除微博成功。', '温馨提示');
        } else {
            toast.error(msg.info, '温馨提示');
        }
    }, 'json');

}


/**
 * 显示微博列表
 * @param weiboCommentList
 */
function showWeiboCommentList(weiboCommentList) {
    //判断是否已经载入
    var weiboContainer = $('.weibo-comment-container', weiboCommentList);
    var loaded = weiboCommentList.attr('data-weibo-comment-loaded');

    //如果已经载入，只要显示即可
    if (loaded) {
        weiboCommentList.show();
        return;
    }
    //显示“正在加载评论”
    weiboContainer.html('<span class="text-muted">正在加载...</span>');
    weiboCommentList.show();

    //通过服务器载入评论列表
    reloadWeiboCommentList(weiboCommentList);
}

function hideWeiboCommentList(weiboCommentList) {
    weiboCommentList.hide();
}

/**
 * 重新读入微博评论列表
 * @param weiboCommentList
 */
function reloadWeiboCommentList(weiboCommentList) {
    //标记为已经载入
    weiboCommentList.attr('data-weibo-comment-loaded', '1');

    var weiboId = weiboCommentList.attr('data-weibo-id');

    var weibo = $('#weibo_' + weiboId);
    var weiboContainer = $('.weibo-comment-container', weiboCommentList);
    var url = U('Weibo/Index/loadComment');
    $.post(url, {weibo_id: weiboId}, function (a) {
        weiboContainer.html(a);
        //更新评论数量
        var commentLinkText = $('.operation', weiboContainer).html();
        $('.operation', weibo).html(commentLinkText);
        bindRepost();
    });
}







$(function () {
    bindRepost();
});
function bindRepost(){
    $('.send_repost').magnificPopup({
        type: 'ajax',
        overflowY: 'scroll',
        modal: true,
        callbacks: {
            ajaxContentAdded: function() {
                // Ajax content is loaded and appended to DOM
                $('#repost_content').focus();
                console.log(this.content);
            }
        }
    });
}