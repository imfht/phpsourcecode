<div class="panel panel-default">
    <div class="panel-heading">
        <b>上传头像</b>
    </div>

    <div class="panel-body">
        <div class="well">上传头像文件的最大尺寸为100KB。后缀名为JPG、PNG、JPEG、BMP</div>
        <input type="file" class="file-loading" name="imageUpload" id="avatarUpload">
    </div>
</div>

<link rel="stylesheet" type="text/css" media="screen" href="<?= THEMEPATH ?>/css/fileinput.min.css"/>
<script src="<?= THEMEPATH ?>/js/fileinput.min.js"></script>
<script src="<?= THEMEPATH ?>/js/fileinput_locale_zh.js"></script>

<script type="text/javascript">
    $(function () {
        //上传头像
        $('#avatarUpload').fileinput({
            'language': 'zh', //设置语言
            'uploadUrl': "<?= site_url('/user/avatar') ?>",
            maxFileSize: 100,
            showCaption: false,
            uploadClass: 'btn btn-success',
            elErrorContainer: '#kv-avatar-errors',
            defaultPreviewContent: '<img src="<?= THEMEPATH?>images/avatar/<?=isset($user['avatar'])&& $user['avatar']!='' ?  $user['avatar'] : 'default.jpg' ?>" alt="头像" style="width:150px">',
            allowedFileExtensions: ["jpg", "png", "bmp", 'jpeg']
        });

        $('#avatarUpload').on('fileuploaded', function (event, data) {
            var file = data.response;
            var image_url = ' <?=THEMEPATH?>images/avatar/' + file.profile['file_name'];
            $('#userAvatar').attr('src', image_url);
            show_error('头像保存成功。');
        });
    });
</script>
