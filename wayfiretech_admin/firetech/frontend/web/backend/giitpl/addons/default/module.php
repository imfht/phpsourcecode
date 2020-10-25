<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-30 18:22:18
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-06-24 20:33:19
 */

/**
 * This is the template for generating a module class file.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\module\Generator */

$className = $generator->moduleClass;
$pos = strrpos($className, '\\');
$ns = ltrim(substr($className, 0, $pos), '\\');
$className = substr($className, $pos + 1);

echo "<?php\n";
?>

namespace <?= $ns; ?>;
use common\components\addons\AddonsModule;

/**
 * <?= $generator->moduleID; ?> module definition class
 */
class <?= $className; ?> extends AddonsModule
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = '<?= $generator->getControllerNamespace().'\\backend'; ?>';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
