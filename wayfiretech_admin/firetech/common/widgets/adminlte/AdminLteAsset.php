<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-03 07:10:17
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-08 22:40:49
 */

namespace common\widgets\adminlte;

use yii\base\Exception;
use yii\web\AssetBundle as BaseAdminLteAsset;

/**
 * AdminLte AssetBundle.
 *
 * @since 0.1
 */
class AdminLteAsset extends BaseAdminLteAsset
{
    public $sourcePath = '@common/widgets/adminlte/asset/';
    public $css = [
        // 'node_modules/bootstrap/css/bootstrap.min.css',
        'dist/css/font-awesome.min.css',
        'dist/css/ionicons.min.css',
        'dist/css/AdminLTE.min.css',
        'dist/css/skins/all-skins.min.css',
    ];
    public $js = [
        // 'plugins/jQuery/jquery-2.2.3.min.js',
        // 'plugins/jQuery/jquery-migrate.js',
        // 'dist/main.js',
        'dist/js/app.js',
        'dist/js/demo.js',
        'dist/js/app_iframe.js',
    ];

    public $jsOptions = [
        // 'type'=>'module'
    ];

    public $depends = [
        'common\widgets\adminlte\VuemainAsset',
        // 'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    /**
     * @var string|bool Choose skin color, eg. `'skin-blue'` or set `false` to disable skin loading
     *
     * @see https://almsaeedstudio.com/themes/AdminLTE/documentation/index.html#layout
     */
    public $skin = 'all-skins';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // Append skin color file if specified
        if ($this->skin) {
            if (('all-skins' !== $this->skin) && (strpos($this->skin, 'skin-') !== 0)) {
                throw new Exception('Invalid skin specified');
            }
            $this->css[] = sprintf('dist/css/skins/%s.min.css', trim($this->skin));
        }

        parent::init();
    }
}
