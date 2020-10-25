<?php
namespace Jykj\Siteconfig\Controller;


/***
 *
 * This file is part of the "系统配置" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 yangshichang <yangshichang@ngoos.org>, 极益科技
 *
 ***/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * ConfigController
 */
class ConfigController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * configRepository
     * 
     * @var \Jykj\Siteconfig\Domain\Repository\ConfigRepository
     * @inject
     */
    protected $configRepository = null;

    /**
     * Default template name
     * @var string
     */
    var $template = 'v2';
    
    /**
     * init
     */
    public function initializeAction(){
        if(GeneralUtility::_GP('certificate')){
            
        }
        $template = '';
        if(isset($_POST['TYPO3_CONF_VARS']['STYLE']['template']) && $_POST['TYPO3_CONF_VARS']['STYLE']['template']){
            $template = $_POST['TYPO3_CONF_VARS']['STYLE']['template'];
        }elseif(isset($GLOBALS['TYPO3_CONF_VARS']['STYLE']['template'])&&$GLOBALS['TYPO3_CONF_VARS']['STYLE']['template']){
            $template = $GLOBALS['TYPO3_CONF_VARS']['STYLE']['template'];
        }
        if($template && is_dir(PATH_site.'fileadmin/templates/'.$this->template)){
            $this->template = $template;
        }
    }
    
    /**
     * action list
     * 
     * @return void
     */
    public function listAction()
    {
        $certificateFile = PATH_site.'typo3conf/ext/siteconfig/Resources/Public/Images/certificate_bg.jpg';
        list($width, $height) = getimagesize($certificateFile);
        $this->view->assign('hash', uniqid(microtime()));
        $this->view->assignMultiple(array(
            'sitetitle' => $this->getValue(array('sitetitle'), 'sys_template', array('uid'=>1)),
            'TYPO3_CONF_VARS' => $GLOBALS['TYPO3_CONF_VARS'],
            'template'        => $this->template,
            'width'           => $width,
            'height'          => $height
        ));
    }

    /**
     * action show
     * 
     * @param \Jykj\Siteconfig\Domain\Model\Config $config
     * @return void
     */
    public function showAction(\Jykj\Siteconfig\Domain\Model\Config $config)
    {
        $this->view->assign('config', $config);
    }

    /**
     * action new
     * 
     * @return void
     */
    public function newAction()
    {
    }

    /**
     * action create
     * 
     * @param \Jykj\Siteconfig\Domain\Model\Config $newConfig
     * @return void
     */
    public function createAction(\Jykj\Siteconfig\Domain\Model\Config $newConfig)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->configRepository->add($newConfig);
        $this->redirect('list');
    }

    /**
     * action edit
     * 
     * @param \Jykj\Siteconfig\Domain\Model\Config $config
     * @ignorevalidation $config
     * @return void
     */
    public function editAction(\Jykj\Siteconfig\Domain\Model\Config $config)
    {
        $this->view->assign('config', $config);
    }

    /**
     * action update
     * 
     * @param \Jykj\Siteconfig\Domain\Model\Config $config
     * @return void
     */
    public function updateAction()
    {
        $errorArray = array();
        
        //logo
        $logoFile = PATH_site.'typo3conf/ext/website/Resources/Public/Images/logo.svg';
        if(!empty($_FILES['logo']['tmp_name'])){
            if(is_writeable($logoFile)){
                GeneralUtility::upload_copy_move($_FILES['logo']['tmp_name'], $logoFile);
                //图片进行了压缩处理，在上传时，删除缓存文件
                exec("pwd",$output);
                $path = $output[0]."/fileadmin/_processed_/*";
                exec("rm -rf ".$path,$out);
            }else{
                $errorArray[] = 'Logo文件无写入权限';
            }
        }
        
        //logoinner
        $logoFile = PATH_site.'typo3conf/ext/website/Resources/Public/Images/logo_inner.svg';
        if(!empty($_FILES['logoinner']['tmp_name'])){
            if(is_writeable($logoFile)){
                GeneralUtility::upload_copy_move($_FILES['logoinner']['tmp_name'], $logoFile);
                //图片进行了压缩处理，在上传时，删除缓存文件
                exec("pwd",$output);
                $path = $output[0]."/fileadmin/_processed_/*";
                exec("rm -rf ".$path,$out);
            }else{
                $errorArray[] = 'Logoinner文件无写入权限';
            }
        }
        
        //banner
        $bannerFile = PATH_site.'typo3conf/ext/website/Resources/Public/Images/banner.jpg';
        if(!empty($_FILES['banner']['tmp_name'])){
            if(is_writeable($bannerFile)){
                GeneralUtility::upload_copy_move($_FILES['banner']['tmp_name'], $bannerFile);
            }else{
                $errorArray[] = 'Banner文件无写入权限';
            }
        }
        
        //certificate file
         if(!empty($_FILES['certificate_file']['tmp_name'])) {
            GeneralUtility::upload_copy_move($_FILES['certificate_file']['tmp_name'], 'typo3conf/ext/siteconfig/Resources/Public/Images/certificate_bg.jpg');
         }
         $GLOBALS['TYPO3_CONF_VARS']['CERTIFICATE'] = $_POST['TYPO3_CONF_VARS']['CERTIFICATE'];
         $this->generateCertificate('张三', '100', 'CERT-DEMO');
        
        //site title
         GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('sys_template')
         ->update(
             'sys_template',
             [ 'sitetitle' => GeneralUtility::_GP('sitetitle') ], // set
             [ 'uid' => 1 ] // where
         );
        
        //switch template begin
        //查询config和constants
         $ts = GeneralUtility::makeInstance(ConnectionPool::class)
         ->getConnectionForTable('sys_template')
         ->select(
             ['config', 'constants'], // fields to select
             'sys_template', // from
             [ 'uid' => 1 ] // where
         )
         ->fetch();
        //循环处理config的数据
        $tsArray = GeneralUtility::trimExplode(chr(10), $ts['config']);
        foreach ($tsArray as $key=>$val) {
            if(preg_match("/FILE\:EXT\:website\/Configuration\/TypoScript\/plugin\.typoscript/is", $val)){
                unset($tsArray[$key]);
            }
            if(preg_match("/config\.baseURL/is", $val)){
                unset($tsArray[$key]);
            }
        }
        array_unshift($tsArray, 'config.baseURL = '.$_POST['TYPO3_CONF_VARS']['FE']['baseURL']);
        array_unshift($tsArray, '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:website/Configuration/TypoScript/plugin.typoscript">');
        //修改config的数据
        GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('sys_template')
        ->update(
            'sys_template',
            [ 'config'=>implode(chr(10), $tsArray)], // set
            [ 'uid' => '1' ] // where
        );

        //switch template end
        
        //TYPO3_CONF_VARS Config
        if(GeneralUtility::_POST('TYPO3_CONF_VARS')){
            $configurationManager = $this->objectManager->get(\TYPO3\CMS\Core\Configuration\ConfigurationManager::class);
            $configurationManager->updateLocalConfiguration($_POST['TYPO3_CONF_VARS']);
        }
        
        if(!empty($errorArray)){
            foreach($errorArray as $error){
                $this->addFlashMessage($error, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
            }
        }else{
            $this->addFlashMessage('配置更新成功', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        }
        
        $CacheManager = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Cache\CacheManager::class);
        $CacheManager->flushCachesInGroup('pages');
        
        $this->redirect('list');
    }

    private function getValue($field, $table, $where){
        $result = GeneralUtility::makeInstance(ConnectionPool::class)
        ->getConnectionForTable($table)
        ->select(
            $field, // array fields to select 
            $table, // from
            $where // array where
        )
        ->fetch();
        return $result[$field[0]];
    }
    
    public function generateCertificate($header_name, $money, $cert_number){
        if(!is_dir(PATH_site.'uploads/certificate/')){
            mkdir(PATH_site.'uploads/certificate/', 0777);
        }
        //generate cert file
        $certificateFile = PATH_site.'typo3conf/ext/siteconfig/Resources/Public/Images/certificate_bg.jpg';
        list($width, $height) = getimagesize($certificateFile);
        $extConf = $GLOBALS['TYPO3_CONF_VARS']['CERTIFICATE'];
        $param = "-resize {$width}x{$height} -font ".PATH_site.\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('siteconfig')."Resources/Public/Fonts/msyh.ttf -fill black -pointsize 30 -draw 'text {$extConf['header']} \"{$header_name}\" text {$extConf['money']} \"￥{$money}元\" text {$extConf['number']} \"{$cert_number}\" text {$extConf['date']} \"".date("Y年m月d日", time())."\"' -colorspace RGB -quality 80";
        $gifCreator = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Imaging\GraphicalFunctions');
        $gifCreator->init();
        $gifCreator->imageMagickExec($certificateFile, PATH_site.'uploads/certificate/'.$cert_number.'.jpg', $param);
    }
    
    /**
     * action delete
     * 
     * @param \Jykj\Siteconfig\Domain\Model\Config $config
     * @return void
     */
    public function deleteAction(\Jykj\Siteconfig\Domain\Model\Config $config)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->configRepository->remove($config);
        $this->redirect('list');
    }
}
