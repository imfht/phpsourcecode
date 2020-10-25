<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-05 08:20:10
 * @Last Modified by:   Wang Chunsheng 2192138785@qq.com
 * @Last Modified time: 2020-03-05 08:20:10
 */

/**
 * This is the template for generating a controller class within a module.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\module\Generator */

echo "<?php\n";
?>

namespace <?= $generator->getControllerNamespace() ?>;


use backend\controllers\BaseController;

/**
* Default controller for the `<?= $generator->moduleID ?>` module
*/
class DefaultController extends BaseController
{
/**
* Renders the index view for the module
* @return string
*/
public function actionIndex()
{
return $this->render('index');
}
}