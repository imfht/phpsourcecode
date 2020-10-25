<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-26 00:09:42
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-30 11:20:34
 */
echo "<?php\n";
?>

namespace <?= $generator->getControllerNamespace(); ?>;

use common\components\addons\AddonsModule;

/**
 * diandi_dingzuo module definition class
 */
class frontend extends AddonsModule
{
    /**
     * {@inheritdoc}frontend
     */
    public $controllerNamespace = '<?= $generator->getFrontendPath(); ?>';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        // custom initialization code goes here
    }
}
