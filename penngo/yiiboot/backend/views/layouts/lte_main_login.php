<?php 
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>AdminLTE 2 | Log in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="<?=Url::base()?>/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?=Url::base()?>/libs/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?=Url::base()?>/libs/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?=Url::base()?>/dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?=Url::base()?>/plugins/iCheck/square/blue.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <!-- jQuery 2.2.3 -->
<script src="<?=Url::base()?>/plugins/jQuery/jquery-2.2.3.min.js"></script>
</head>
<body class="hold-transition login-page">
<?= $content ?>

<script src="<?=Url::base()?>/plugins/form/jquery.form.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?=Url::base()?>/bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="<?=Url::base()?>/plugins/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>
</body>
</html>
