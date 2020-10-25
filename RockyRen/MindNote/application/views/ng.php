<!doctype html>
<html lang="zh-en" id="mindnote">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MindNote</title>

  <link rel="stylesheet" href="<?=base_url();?>public/packages/bower/bootstrap/dist/css/bootstrap.css"/>
  <link rel="stylesheet" href="<?=base_url();?>public/packages/bower/font-awesome/css/font-awesome.css"/>
  <link rel="stylesheet" href="<?=base_url();?>public/packages/bower/textAngular/dist/textAngular.css"/>

  <link rel="stylesheet" href="<?=base_url();?>public/ngApp/note/css/mindnote-common.css"/>
  <link rel="stylesheet" href="<?=base_url();?>public/ngApp/note/css/catalogue.css"/>
  <link rel="stylesheet" href="<?=base_url();?>public/ngApp/note/css/notes.css"/>
  <link rel="stylesheet" href="<?=base_url();?>public/ngApp/note/css/note-content.css"/>
  <link rel="stylesheet" href="<?=base_url();?>public/ngApp/library/mindmap/css/mindmap.css"/>

</head>
<body>


<div id="note-nav">
  <a class="logo" href="#"><span class="mind-text">Mind</span><span class="note-text">Note</span></a>
    <span class="nav-right">
      <a class="username">你好， <?=$username?></a> |
      <a class="logout" href="<?=site_url('home/logout')?>">注销</a>
    </span>
</div>



<ui-view></ui-view>


<script src="<?=base_url();?>public/packages/bower/textAngular/dist/textAngular-rangy.min.js"></script>
<script data-main="<?=base_url();?>public/ngApp/requireMain.js"
        src="<?=base_url();?>public/packages/bower/requirejs/require.js" ></script>
</body>
</html>