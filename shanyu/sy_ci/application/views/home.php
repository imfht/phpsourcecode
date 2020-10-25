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
  <div class="art-list">
    <ul class="list-unstyled">
      <?php foreach ($article_list as $key => $value): ?>
        <li>
          <span class="time"><?= $value['time'] ?></span>
          <a href="<?= 'article/'.$category_list[$value['cid']]['name'].'/'.$value['id'].'.html' ?>"><?= $value['title'] ?></a>
        </li>
      <?php endforeach ?>
    </ul>
  </div>

  <div class="art-page">
   <ul class="pagination pagination-sm">
    <?= $article_page ?>
  </ul>        
</div>
</div>