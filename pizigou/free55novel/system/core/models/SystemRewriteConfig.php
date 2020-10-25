<?php

/**
 * Class SystemRewriteConfig
 */
class SystemRewriteConfig extends CFormModel
{
    private $idPatternRule = '/(\{id\}){1}/i';

    private $shortTitlePatternRule = '/(\{shorttitle\}){1}/i';

	public $UrlSuffix;

//    public $RewriteRules;

    public $CategoryRule;

    public $BookDetailRule;

    public $ChapterDetailRule;

    public $NewsListRule;

    public $NewsDetailRule;


	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
            array('UrlSuffix', 'in', 'range' => array_keys(Yii::app()->params['urlSuffix'])),
            array('CategoryRule', 'match', pattern => $this->shortTitlePatternRule),
            array('BookDetailRule', 'match', pattern => $this->idPatternRule),
            array('ChapterDetailRule', 'match', pattern => $this->idPatternRule),
            array('NewsListRule', 'match', pattern => $this->idPatternRule),
            array('NewsDetailRule', 'match', pattern => $this->idPatternRule),
//			array('UrlSuffix,RewriteRules', 'required'),
//			array('SiteAdminEmail', 'email'),
//			array('UrlSuffix', ''),
//            array('RewriteRule', 'length', 'max'=> 255),
//            array('SiteKeywords,SiteIntro,SiteCopyright,SiteAttachmentPath', 'length', 'max'=> 255),
//			array('verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements()),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'UrlSuffix' => '网址后缀',
			'CategoryRule' => '分类网址规则',
			'BookDetailRule' => '小说网址规则',
			'ChapterDetailRule' => '章节网址规则',
			'NewsListRule' => '新闻列表网址规则',
			'NewsDetailRule' => '新闻内容网址规则',
//			'SiteIntro' => '站点简介',
//			'SiteAdminEmail' => '管理员邮箱',
//			'SiteCopyright' => '站点版权信息',
//			'SiteAttachmentPath' => '站点附件地址',
		);
	}

    public function getIdPatternRule()
    {
        return $this->idPatternRule;
    }

    public function getShortTitleRule()
    {
        return $this->shortTitlePatternRule;
    }
}
