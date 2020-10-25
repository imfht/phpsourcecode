<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-26 00:09:42
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-11 23:22:22
 */
echo "<?php\n";
?>

namespace <?= $generator->getControllerNamespace(); ?>;

use Yii;
use yii\db\Migration;
use common\helpers\MigrateHelper;
use common\interfaces\AddonWidget;

/**
 * 安装
 *
 * Class Install
 * @package addons\Merchants
 */
class Install extends Migration implements AddonWidget
{
  public function run($addon)
  {
    MigrateHelper::upByPath([
      '@common/addons/<?= $generator->moduleID; ?>/migrations/'
    ]);
  }
}
