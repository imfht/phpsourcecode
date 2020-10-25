<?php
/**
 * Class FWFrontController
 */
class FWFrontController extends CController
{
	public $optionInfo;
	public $viewSeo;

    public $pageTitle = "";

    public $pageKeywords = "";

    public $pageDescription = "";

    /**
     * @var SystemBaseConfig
     */
    protected $siteConfig = null;

	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/main';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

	public function init(){
        if (!H::checkIsInstall()) return;
        $m = Yii::app()->settings->get("SystemBaseConfig");
        if ($m) {
            $this->siteConfig = $m;
        }
	}
}