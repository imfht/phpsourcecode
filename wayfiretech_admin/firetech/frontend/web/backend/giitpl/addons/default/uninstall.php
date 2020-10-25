<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-26 00:09:42
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-11 23:21:45
 */
echo "<?php\n";
?>

namespace <?= $generator->getControllerNamespace(); ?>;

use yii\db\Migration;
use common\enums\AppEnum;
use common\models\rbac\AuthItemChild;
use common\models\rbac\AuthRole;
use common\models\common\ConfigValue;
use common\helpers\MigrateHelper;
use common\interfaces\AddonWidget;

/**
 * 卸载
 *
 * Class UnInstall
 * @package addons\Merchants
 */
class UnInstall extends Migration implements AddonWidget
{
    
    public function run($addon)
    {

        MigrateHelper::downByPath([
            '@common/addons/<?= $generator->moduleID; ?>/migrations/'
        ]);
    }
}
