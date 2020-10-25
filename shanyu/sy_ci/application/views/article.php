<link rel="stylesheet" href="/static/css/prettify/tomorrow-night-eighties.min.css">

<div class="main-nav">
  <ol class="breadcrumb">
    <li><a href="/">首页</a></li>
    <?php if(!empty($nav)){ ?>
    <?php foreach ($nav as $key => $value) { ?>
    <li><a href="<?= $value['url'] ?>"><?= $value['title'] ?></a></li>
    <?php } ?>
    <?php } ?>
  </ol>
</div>

<div class="main-con">
  <div class="art-con">
   <?= $article['content'] ?>
 </div>
</div>