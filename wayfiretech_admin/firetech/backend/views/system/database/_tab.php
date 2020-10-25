<?php
/**
 * @Author: Wang chunsheng
 * @Date:   2020-04-29 02:32:12
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-06-23 21:46:48
 */
use common\widgets\tab\Tab;

?>
<?= Tab::widget([
    'titles' => ['数据库备份', '数据库还原'],
    'urls'=>['backups','restore']
    ]); ?>


