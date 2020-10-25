<?php foreach ($stories as $s): ?>
    <div class="box">

        <div class="text-center">
            <a href="<?= site_url('/story/'.$s['id']) ?>">
                <img src="<?= site_url('/') ?><?= $s['image'] ? $s['image'] : 'books/default.jpg' ?>" width="160px" title="<?= $s['desc'] ?>"/>
            </a><br/>
            <span class="">
            <a href="<?= site_url('/story/'.$s['id']) ?>">
                <b><?= $s['title'] ?></b> - <?= $s['author'] ?>
            </a>
            </span>
        </div>


    </div>
<?php endforeach; ?>