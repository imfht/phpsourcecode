<?php 
use SCH60\Kernel\App;
use SCH60\Kernel\KernelHelper;
use SCH60\Kernel\StrHelper;

$router = App::$app->getRouter();
?>

<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=StrHelper::O($title. (isset($parentTitle) ? " - ". $parentTitle : ""));?> - <?=StrHelper::O(KernelHelper::config('product_name'));?></title>
    
    <!-- Bootstrap -->
    <link href="<?=StrHelper::urlStatic("static/bootstrap-3.3.5/css/bootstrap.min.css")?>" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="<?=StrHelper::urlStatic("static/html5shiv/html5shiv.min.js");?>"></script>
      <script src="<?=StrHelper::urlStatic("static/respond.js/respond.min.js");?>"></script>
    <![endif]-->
    
    <link href="<?=StrHelper::urlStatic("static/adminapp/css/default.css")?>" rel="stylesheet">
    <link href="<?=StrHelper::urlStatic("static/adminapp/css/sub_". strtolower($router['subapp']). ".css")?>" rel="stylesheet">
    