<?php
/* @var $this \yii\web\View */
/* @var $content string */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use frontend\assets\ThemeAsset;
use yeesoft\models\Menu;
use yeesoft\widgets\LanguageSelector;
use yeesoft\widgets\Nav as Navigation;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yeesoft\comment\widgets\RecentComments;

Yii::$app->assetManager->forceCopy = true;
AppAsset::register($this);
ThemeAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <?= $this->renderMetaTags() ?>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->settings->get('general.title', 'Yee Site', Yii::$app->language),
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = Menu::getMenuItems('main-menu');
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => Yii::t('yee/auth', 'Signup'), 'url' => \yii\helpers\Url::to(['/auth/default/signup'])];
        $menuItems[] = ['label' => Yii::t('yee/auth', 'Login'), 'url' => ['/auth/default/login']];
    } else {
        $menuItems[] = [
            'label' => Yii::$app->user->identity->username,
            'url' => ['/auth/default/profile'],
        ];

        $menuItems[] = [
            'label' => Yii::t('yee/auth', 'Logout'),
            'url' => ['/auth/default/logout', 'language' => false],
            'linkOptions' => ['data-method' => 'post']
        ];
    }
    echo Nav::widget([
        'encodeLabels' => false,
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);

    echo LanguageSelector::widget(['display' => 'label', 'view' => 'pills']);

    NavBar::end();
    ?>

    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="hidden-xs">
                    <?php
                        $menuItemsKey = '__mainMenuItems' . Yii::$app->language;
                        if(!$menuItems = Yii::$app->cache->get($menuItemsKey)){
                            $menuItems = Menu::getMenuItems('main-menu');
                            Yii::$app->cache->set($menuItemsKey, $menuItems, 3600);
                        }

                        echo Navigation::widget([
                            'encodeLabels' => false,
                            'items' => $menuItems,
                            'options' => [
                                ['class' => 'nav nav-pills nav-stacked'],
                                ['class' => 'nav nav-second-level'],
                                ['class' => 'nav nav-third-level']
                            ],
                        ]);
                    ?>
                </div>

                <div>
                    <?= RecentComments::widget() ?>
                </div>
            </div>
            <div class="col-md-9">
                <?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []]) ?>
                <?= Alert::widget() ?>
                <?= $content ?>
            </div>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="container">

        <p class="center-block text-center">&copy; <?= Html::encode(Yii::$app->settings->get('general.title', 'Yee Site', Yii::$app->language)) ?> <?= date('Y') ?></p>

        <!--
        <p class="pull-right"><//?= Yii::powered() ?>, <//?= yeesoft\Yee::powered() ?></p>
        -->
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
