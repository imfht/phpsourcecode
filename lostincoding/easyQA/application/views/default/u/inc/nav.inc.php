<div class="layui-tab layui-tab-brief mb30 mt0">
    <ul class="layui-tab-title">
        <li<?=$active_nav == 'q' ? ' class="layui-this"' : ''?>><a class="pjax" href="/u">文章 <cite><?=$article_counts?></cite></a></li>
        <li<?=$active_nav == 'comment' ? ' class="layui-this"' : ''?>><a class="pjax" href="/u/comment">评论 <cite><?=$comment_counts?></cite></a></li>
        <li<?=$active_nav == 'favorite' ? ' class="layui-this"' : ''?>><a class="pjax" href="/u/favorite">收藏</cite></a></li>
        <li<?=$active_nav == 'msg' ? ' class="layui-this"' : ''?>><a class="pjax" href="/u/msg">消息 <cite><?=$msg_to_me_counts?></cite></a></li>
        <li<?=$active_nav == 'avatar' ? ' class="layui-this"' : ''?>><a class="pjax" href="/u/avatar">头像</a></li>
        <li<?=$active_nav == 'reset_pwd' ? ' class="layui-this"' : ''?>><a class="pjax" href="/u/reset_pwd">密码</a></li>
        <li<?=$active_nav == 'verify' ? ' class="layui-this"' : ''?>><a class="pjax" href="/u/verify">认证</a></li>
        <li<?=$active_nav == 'bind' ? ' class="layui-this"' : ''?>><a class="pjax" href="/u/bind">账号绑定</a></li>
        <li<?=$active_nav == 'profile' ? ' class="layui-this"' : ''?>><a class="pjax" href="/u/profile">个人资料</a></li>
        <li><a class="pjax" href="/u/home/<?=$user['id']?>">我的主页</a></li>
    </ul>
</div>