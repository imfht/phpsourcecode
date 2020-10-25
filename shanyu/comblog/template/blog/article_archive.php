<?php include 'public_head.php'; ?>
<div class="article-category">
    <div class="title">
        日期归档
    </div>
    <div class="list">
        <ul>
            <?php foreach ($years as $year){ ?>
            <li>
                <i><?= $year ?></i>
                <ul>
                    <?php while (!empty($list)){
                        $v=array_shift($list);
                        if($v['create_year']!=$year){
                            array_unshift($list, $v);
                            break;
                        }
                    ?>
                    <li>
                        <i><?= $v['create_day'] ?></i>
                        <a href="<?= $v['url'] ?>"><?= $v['title'] ?></a>
                    </li>
                    <?php } ?>
                </ul>
            </li>
            <?php } ?>
        </ul>  
    </div>
</div>
<?php include 'public_foot.php'; ?>