<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<div id="main" class="main layui-clear">
    <?php require_once VIEWPATH . "$theme_id/account/inc/account_nav.inc.php";?>
    <div class="layui-form layui-form-pane">
        <form method="post" onsubmit="return perfect();">
            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">昵称</label>
                <div class="layui-input-inline">
                    <input type="text" id="nickname" name="nickname" class="layui-input" value="<?=$open_user['nickname']?>">
                </div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn">立即完善</button>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
$(function(){
    $('#nickname').val($('#nickname').val()).focus();
});

//完善资料
function perfect(){
    var $nickname = $('#nickname');
    var nickname = $nickname.val();

    if(!simple_validate.required(nickname)){
        layer.msg('请填写昵称');
        $nickname.focus();
        return false;
    }

    if(!simple_validate.nickname(nickname)){
        layer.msg('昵称只能为中文、字母、数字或横线-');
        $nickname.focus();
        return false;
    }

    if(!simple_validate.mix_range(nickname, 1, 16)){
        layer.msg('昵称长度为1-16位，1个中文算2个字符长度');
        $nickname.focus();
        return false;
    }

    layer.load();
    $.post(
        '/baseapi/account/perfect',
        {
            'nickname': nickname
        },
        function(json){
            if(json.error_code == 'ok'){
                document.location = '/';
            }
            else{
                show_error(json.error_code);
                layer.closeAll('loading');
            }
        },
        'json'
    );
    return false;
}
</script>
<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>