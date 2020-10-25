<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-30 10:45:20
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-20 10:40:53
 */

namespace addonstpl;

use yii\gii\CodeFile;
use yii\helpers\Html;
use Yii;

class Generator extends \yii\gii\Generator
{
    public $moduleClass;

    public $moduleID;

    public $title;

    public $identifie;

    public $version;

    public $type;

    public $ability;

    public $description;

    public $author;

    public $url;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return '扩展模块生成';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return '生成自己的扩展模块';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['moduleID', 'title', 'version', 'type', 'ability', 'description', 'author', 'url'], 'filter', 'filter' => 'trim'],
            [['moduleID', 'title'], 'required'],
            [['moduleID'], 'match', 'pattern' => '/^[\w\\-]+$/', 'message' => 'Only word characters and dashes are allowed.'],
            [['moduleClass'], 'match', 'pattern' => '/^[\w\\\\]*$/', 'message' => 'Only word characters and backslashes are allowed.'],
            [['moduleClass'], 'validateModuleClass'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'moduleID' => '模块英文名称',
            'moduleClass' => '模块后台入口',
            'title' => '模块中文名称',
            'version' => '模块版本',
            'type' => '模块类型',
            'ability' => '模块一句话介绍',
            'description' => '模块详细说明',
            'author' => '模块作者',
            'url' => '店滴粉丝社区模块介绍地址',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function hints()
    {
        return [
            'moduleID' => 'This refers to the ID of the module, e.g., <code>admin</code>.',
            'moduleClass' => 'This is the fully qualified class name of the module, e.g., <code>app\modules\admin\site</code>.',
            'identifie' => '作者英文名称在前，然后用下划线隔开，例如：diandi_shop',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function successMessage()
    {
        if (Yii::$app->hasModule($this->moduleID)) {
            $link = Html::a('try it now', Yii::$app->getUrlManager()->createUrl($this->moduleID), ['target' => '_blank']);

            return "The module has been generated successfully. You may $link.";
        }

        $output = <<<EOD
<p>The module has been generated successfully.</p>
<p>To access the module, you need to add this to your application configuration:</p>
EOD;
        $code = <<<EOD
<?php
    ......
    'modules' => [
        '{$this->moduleID}' => [
            'class' => '{$this->moduleClass}',
        ],
    ],
    ......
EOD;

        return $output.'<pre>'.highlight_string($code, true).'</pre>';
    }

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates()
    {
        return ['module.php', 'controller.php', 'view.php', 'api.php', 'AutocompleteAsset.php', 'frontend.php', 'manifest.xml'];
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $files = [];
        $modulePath = $this->getModulePath();
        $files[] = new CodeFile(
            $modulePath.'/site.php',
            $this->render('module.php')
        );

        $files[] = new CodeFile(
            $modulePath.'/backend/DefaultController.php',
            $this->render('controller.php')
        );
        $files[] = new CodeFile(
            $modulePath.'/views/default/index.php',
            $this->render('view.php')
        );
        $files[] = new CodeFile(
            $modulePath.'/api.php',
            $this->render('api.php')
        );
        $files[] = new CodeFile(
            $modulePath.'/frontend.php',
            $this->render('frontend.php')
        );

        $files[] = new CodeFile(
            $modulePath.'/frontend/DocController.php',
            $this->render('DocController.php')
        );

        $files[] = new CodeFile(
            $modulePath.'/api/ApiController.php',
            $this->render('ApiController.php')
        );

        $files[] = new CodeFile(
            $modulePath.'/install.php',
            $this->render('install.php')
        );

        $files[] = new CodeFile(
            $modulePath.'/uninstall.php',
            $this->render('uninstall.php')
        );

        $files[] = new CodeFile(
            $modulePath.'/AutocompleteAsset.php',
            $this->render('AutocompleteAsset.php')
        );

        $files[] = new CodeFile(
            $modulePath.'/upgrade.php',
            $this->render('upgrade.php')
        );

        $files[] = new CodeFile(
            $modulePath.'/python/__init__.py',
            $this->render('__init__.py')
        );

        $files[] = new CodeFile(
            $modulePath.'/manifest.xml',
            $this->render('manifest.xml')
        );

        return $files;
    }

    /**
     * Validates [[moduleClass]] to make sure it is a fully qualified class name.
     */
    public function validateModuleClass()
    {
        if (strpos($this->moduleClass, '\\') === false || Yii::getAlias('@'.str_replace('\\', '/', $this->moduleClass), false) === false) {
            $this->addError('moduleClass', 'Module class must be properly namespaced.');
        }
        if (empty($this->moduleClass) || substr_compare($this->moduleClass, '\\', -1, 1) === 0) {
            $this->addError('moduleClass', 'Module class name must not be empty. Please enter a fully qualified class name. e.g. "app\\modules\\admin\\Module".');
        }
    }

    /**
     * @return bool the directory that contains the module class
     */
    public function getModulePath()
    {
        $this->moduleClass = 'common\\addons\\'.$this->moduleID.'\\site';

        return Yii::getAlias('@'.str_replace('\\', '/', substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\'))));
    }

    /**
     * @return string the controller namespace of the module
     */
    public function getControllerNamespace()
    {
        return substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\'));
    }

    public function getAssetsPath()
    {
        return substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\')).'\assets';
    }

    public function getFrontendPath()
    {
        return substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\')).'\frontend';
    }

    public function getApiCachekey()
    {
        $key = explode('\\', $this->moduleClass);

        return $key[2].'-api';
    }

    public function getItems($k)
    {
        return $this->$k;
    }
}
