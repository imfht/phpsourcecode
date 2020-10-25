<?php
namespace Jykj\Filemanage\Controller;


/***
 *
 * This file is part of the "文件管理系统" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 yangshichang <yangshichang@ngoos.org>, 极益科技
 *
 ***/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Cache\CacheManager;

/**
 * FilemanageController
 */
class FilemanageController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * filemanageRepository
     * 
     * @var \Jykj\Filemanage\Domain\Repository\FilemanageRepository
     * @inject
     */
    protected $filemanageRepository = null;

    /**
     * filetypesRepository
     * 
     * @var \Jykj\Filemanage\Domain\Repository\FiletypesRepository
     * @inject
     */
    protected $filetypesRepository = null;


    /**
     * action list
     * 
     * @return void
     */
    public function listAction()
    {
        //业务后台分页显示序号
        if($_GET["tx_filemanage_filemanage"]["@widget_0"]["currentPage"]){
            $page=$_GET["tx_filemanage_filemanage"]["@widget_0"]["currentPage"];
        }else{
            $page=1;
        }
        $this->view->assign('page', $page);
        
        //获得输入框的值
        $keyword = $this->request->hasArgument('keyword') ? $this->request->getArgument('keyword') : '';
        //查询
        $filetypeid = 0;//$this->settings['listType'];
        $filemanages = $this->filemanageRepository->findFileList($filetypeid,$keyword);
        $this->view->assign('keyword', $keyword);
        $this->view->assign('filemanages', $filemanages);
    }

    /**
     * action show
     * 
     * @param \Jykj\Filemanage\Domain\Model\Filemanage $filemanage
     * @return void
     */
    public function showAction(\Jykj\Filemanage\Domain\Model\Filemanage $filemanage)
    {
        $this->view->assign('filemanage', $filemanage);
    }

    /**
     * action new
     * 
     * @return void
     */
    public function newAction()
    {
        $this->view->assign('typearr', $this->filetypesRepository->findTypes($this->settings['categories']));
    }

    /**
     * action create
     * 
     * @param \Jykj\Filemanage\Domain\Model\Filemanage $filemanage
     * @return void
     */
    public function createAction(\Jykj\Filemanage\Domain\Model\Filemanage $filemanage)
    {
        $this->addFlashMessage('保存成功', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        if (!empty($_FILES['tx_filemanage_filemanage']['name']['filepath'])) {
            
            $imgpath=PATH_site . 'uploads/tx_filemanage/';
            if (!is_dir($imgpath)) {
                mkdir($imgpath, 0755,true);
            }
            
            $filename = md5(uniqid($_FILES['tx_filemanage_filemanage']['name']['filepath'])).'.'.end(explode('.', $_FILES['tx_filemanage_filemanage']['name']['filepath']));
            
            if(GeneralUtility::upload_copy_move($_FILES['tx_filemanage_filemanage']['tmp_name']['filepath'], $imgpath.$filename)){
                $filemanage->setFilepath($filename);
            }
        }

        //上传文件封面图
        if (!empty($_FILES['tx_filemanage_filemanage']['name']['fileimg'])) {
            $fileimgpath=PATH_site . 'uploads/tx_filemanage/pics/';

            if (!is_dir($fileimgpath)) {
                mkdir($fileimgpath, 0755,true);
            }

            $fileimgname = md5(uniqid($_FILES['tx_filemanage_filemanage']['name']['fileimg'])).'.'.end(explode('.', $_FILES['tx_filemanage_filemanage']['name']['fileimg']));

            if(GeneralUtility::upload_copy_move($_FILES['tx_filemanage_filemanage']['tmp_name']['fileimg'], $fileimgpath.$fileimgname)){
                $filemanage->setFileimg($fileimgname);
            }
        }

        //$filemanage->setFiletypeid($this->settings['listType']);
        $filemanage->setFiletypeid(0);
        $this->filemanageRepository->add($filemanage);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }

    /**
     * action edit
     * 
     * @param \Jykj\Filemanage\Domain\Model\Filemanage $filemanage
     * @ignorevalidation $filemanage
     * @return void
     */
    public function editAction(\Jykj\Filemanage\Domain\Model\Filemanage $filemanage)
    {
        $this->view->assign('filemanage', $filemanage);
        $this->view->assign('typearr', $this->filetypesRepository->findTypes($this->settings['categories']));
    }

    /**
     * action update
     * 
     * @param \Jykj\Filemanage\Domain\Model\Filemanage $filemanage
     * @return void
     */
    public function updateAction(\Jykj\Filemanage\Domain\Model\Filemanage $filemanage)
    {
        $this->addFlashMessage('修改成功', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        if (!empty($_FILES['tx_filemanage_filemanage']['name']['filepath'])) {
            
            $imgpath=PATH_site . 'uploads/tx_filemanage/';
            if (!is_dir($imgpath)) {
                mkdir($imgpath, 0755,true);
            }
            
            $filename = md5(uniqid($_FILES['tx_filemanage_filemanage']['name']['filepath'])).'.'.end(explode('.', $_FILES['tx_filemanage_filemanage']['name']['filepath']));
            
            if(GeneralUtility::upload_copy_move($_FILES['tx_filemanage_filemanage']['tmp_name']['filepath'], $imgpath.$filename)){
                //删除原有文件
                $ipath = $imgpath.$filemanage->getFilepath();
                unlink($ipath);
                $filemanage->setFilepath($filename);
            }
        }
        
        //上传文件封面图
        if (!empty($_FILES['tx_filemanage_filemanage']['name']['fileimg'])) {
            $fileimgpath=PATH_site . 'uploads/tx_filemanage/pics/';

            if (!is_dir($fileimgpath)) {
                mkdir($fileimgpath, 0755,true);
            }

            $fileimgname = md5(uniqid($_FILES['tx_filemanage_filemanage']['name']['fileimg'])).'.'.end(explode('.', $_FILES['tx_filemanage_filemanage']['name']['fileimg']));

            if(GeneralUtility::upload_copy_move($_FILES['tx_filemanage_filemanage']['tmp_name']['fileimg'], $fileimgpath.$fileimgname)){
                //删除原有文件
                if($filemanage->getFileimg()!=""){
                    $ipath1 = $fileimgpath.$filemanage->getFileimg();
                    unlink($ipath1);
                }
                $filemanage->setFileimg($fileimgname);
            }
        }
        $this->filemanageRepository->update($filemanage);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }

    /**
     * action delete
     * 
     * @param \Jykj\Filemanage\Domain\Model\Filemanage $filemanage
     * @return void
     */
    public function deleteAction(\Jykj\Filemanage\Domain\Model\Filemanage $filemanage)
    {
        $this->addFlashMessage('删除成功', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->filemanageRepository->remove($filemanage);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }

    /**
     * action qtlist
     * 
     * @return void
     */
    public function qtlistAction()
    {
        //获得输入框的值
        $filetypeid = 0;//$this->settings['listType'];//文件类型
        $types = $this->settings['categories'];//文件所属分类
        $filemanages = $this->filemanageRepository->findFileList($filetypeid,"",$types);
        $this->view->assign('filemanages', $filemanages);
    }

    /**
     * action sylist
     * 
     * @return void
     */
    public function sylistAction()
    {
        //获得输入框的值
        $filetypeid = 0;//$this->settings['listType'];//文件类型
        $types = $this->settings['categories'];//文件所属分类
        $num=6;//显示最大记录数
        $filemanages = $this->filemanageRepository->findFileSyList($filetypeid,$types,$num);
        $this->view->assign('filemanages', $filemanages);
    }

    /**
     * action download
     * 
     * @param \Jykj\Filemanage\Domain\Model\Filemanage $filemanage
     * @return void
     */
    public function downloadAction(\Jykj\Filemanage\Domain\Model\Filemanage $filemanage)
    {
        $file=PATH_site.'uploads/tx_filemanage/'.$filemanage->getFilepath();
        ob_end_clean();
        $filename=$filemanage->getFilepath();
        header("Content-type: text/plain");
        Header("Content-Transfer-Encoding: binary");
        header("Accept-Ranges: bytes");
        Header("Content-Length: ".filesize($file));
        header("Content-Disposition: attachment; filename=".$filename);
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0" );
        header("Pragma: no-cache" );
        header("Expires: 0" );
        $str = file_get_contents($file);
        exit($str);
    }
}
