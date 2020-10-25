<?php
/* @var $this yii\web\View */

use yeesoft\comments\widgets\Comments;
use yeesoft\post\models\Post;

/* @var $post yeesoft\post\models\Post */

$this->title = $post->title;
$this->params['breadcrumbs'][] = $post->title;
?>

<?= $this->render('/items/post.php', ['post' => $post]) ?>

<?php if ($post->comment_status == Post::COMMENT_STATUS_OPEN): ?>
    <?php echo Comments::widget(['model' => Post::className(), 'model_id' => $post->id]); ?>
<?php endif; ?>