<?php
namespace Jykj\PhotoAlbum\Controller;


/***
 *
 * This file is part of the "相册管理" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 yangshichang <yangshichang@ngoos.org>, 极益科技
 *
 ***/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Cache\CacheManager;

/**
 * PhotosController
 */
class PhotosController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * photosRepository
     * 
     * @var \Jykj\PhotoAlbum\Domain\Repository\PhotosRepository
     * @inject
     */
    protected $photosRepository = null;

    /**
     * action list
     * 
     * @return void
     */
    public function listAction()
    {
        if($_GET["tx_photoalbum_photos"]["@widget_0"]["currentPage"]){
            $page=$_GET["tx_photoalbum_photos"]["@widget_0"]["currentPage"];
        }else{
            $page=1;
        }
        $this->view->assign('page', $page);
        
        $keyword=$this->request->hasArgument('keyword') ? $this->request->getArgument('keyword') : '';
        //通过相册id查询相册文件夹
        $albumuid=$this->request->hasArgument('album') ? $this->request->getArgument('album') : GeneralUtility::_GP("album");
        $album = $this->photosRepository->querySingleRow("sys_file_collection",$albumuid);
        $folder=$album['folder'];
        //通过相册文件夹查询所有的照片
        $photos=$this->photosRepository->findAlls($folder,$keyword);
        $this->view->assign('photos', $photos);
        $this->view->assign('keyword', $keyword);
        $this->view->assign('albumuid', $albumuid);
        
//         var_dump(sha1("/albumfolder/7/27b07ab29fdc600471c6e533c37ec15c.jpg"));
//         var_dump("3b5b49864e5e4f2fd66fd0f58149ad74483cf311");
//         var_dump(sha1("/albumfolder/7"));
//         var_dump("09bb8431a83c0a5077c0c4a9d9b20cc291548e07");
    }

    /**
     * action show
     * 
     * @param 
     * @return void
     */
    public function showAction()
    {
        $fuid=$this->request->hasArgument('fuid') ? $this->request->getArgument('fuid') : '';
        $albumuid=$this->request->hasArgument('albumuid') ? $this->request->getArgument('albumuid') : '';//albumuid
        $photos = $this->photosRepository->findInfoByuid($fuid);
        $this->view->assign('photos', $photos);
        $this->view->assign('albumuid', $albumuid);
    }

    /**
     * action new
     * 
     * @return void
     */
    public function newAction()
    {
        $albumuid=$this->request->hasArgument('albumuid') ? $this->request->getArgument('albumuid') : '';//albumuid
        $this->view->assign('albumuid', $albumuid);
    }

    /**
     * action create
     * 
     * @return void
     */
    public function createAction()
    {
        $this->addFlashMessage('保存成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        
        $arrPhoto=$this->request->hasArgument('photo') ? $this->request->getArgument('photo') : '';//photo
        $arrTitle=$this->request->hasArgument('title') ? $this->request->getArgument('title') : '';//title
        $arrDescription=$this->request->hasArgument('description') ? $this->request->getArgument('description') : '';//description
        
        //通过相册id查询相册文件夹
        $albumuid=$this->request->hasArgument('albumuid') ? $this->request->getArgument('albumuid') : '';//albumuid
        $album = $this->photosRepository->querySingleRow("sys_file_collection",$albumuid);
        
        for($i=0;$i<count($arrPhoto);$i++){
            //文档
            if (!empty($arrPhoto[$i]["name"])) {
                $filepath=PATH_site."fileadmin".$album['folder'];
                if (!is_dir($filepath)) {
                    mkdir($filepath, 0755,true);
                }
                $filename =md5($arrPhoto[$i]["name"]).'.'.end(explode('.',$arrPhoto[$i]["name"]));
                if(GeneralUtility::upload_copy_move($arrPhoto[$i]["tmp_name"], $filepath.$filename)){
                    //插入sys_file
                    $arrInsert=array(
                        "tstamp"=>time(),
                        "last_indexed"=>time(),
                        "storage"=>1,
                        "type"=>2,
                        "identifier"=>$album['folder'].$filename,
                        "identifier_hash"=> sha1($album['folder'].$filename),
                        "folder_hash"=>sha1(substr($album['folder'], 0, -1)),
                        "extension"=>end(explode('.', $filename)),
                        "mime_type"=>$arrPhoto[$i]['type'],
                        "name"=>$filename,
                        //"sha1"=>sha1($filename.$arrPhoto[$i]['size']),
                        "size"=>$arrPhoto[$i]['size'],
                        "creation_date"=>time(),
                        "modification_date"=>time()
                    );
                    $uid=$this->photosRepository->insertRow("sys_file",$arrInsert);
                    
                    list($width, $height, $type, $attr) = getimagesize($filepath.$filename); 
                    //插入sys_file_metadata
                    $arrData=array(
                        "tstamp"=>time(),
                        "crdate"=>time(),
                        "file"=>$uid,
                        "title"=>$arrTitle[$i],
                        "width"=>$width,
                        "height"=>$height,
                        "description"=>$arrDescription[$i],
                    );
                    $this->photosRepository->insertRow("sys_file_metadata",$arrData);
                }
            }
        }
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list', 'Photos', 'PhotoAlbum', ['album' =>$albumuid]);
    }

    /**
     * action edit
     * 
     * @return void
     */
    public function editAction()
    {
        $fuid=$this->request->hasArgument('fuid') ? $this->request->getArgument('fuid') : '';
        $albumuid=$this->request->hasArgument('albumuid') ? $this->request->getArgument('albumuid') : '';//albumuid
        $photos = $this->photosRepository->findInfoByuid($fuid);
        $this->view->assign('photos', $photos);
        $this->view->assign('albumuid', $albumuid);
    }

    /**
     * action update
     * 
     * @return void
     */
    public function updateAction()
    {
        $fuid=$this->request->hasArgument('fuid') ? $this->request->getArgument('fuid') : '';//sys_file的uid
        $muid=$this->request->hasArgument('muid') ? $this->request->getArgument('muid') : '';//sys_file_metadata 的uid
        
        //通过相册id查询相册文件夹
        $albumuid=$this->request->hasArgument('albumuid') ? $this->request->getArgument('albumuid') : '';//albumuid
        $album = $this->photosRepository->querySingleRow("sys_file_collection",$albumuid);
        
        if($fuid=="" || $muid==""){
            $this->addFlashMessage('修改失败！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        }else{
            $title=$this->request->hasArgument('title') ? $this->request->getArgument('title') : '';
            $description=$this->request->hasArgument('description') ? $this->request->getArgument('description') : '';
            
            //是否有问题更新
            $fname=$_FILES['tx_photoalbum_photos']['name']['photo'];
            if ($fname!="") {
                $this->addFlashMessage('修改成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
                //存储文件的位置
                $imgpath=PATH_site."fileadmin".$album['folder'];
                //重命名文件名
                $filename = md5(uniqid($fname)).'.'.end(explode('.',$fname));
                //移动文件
                if(GeneralUtility::upload_copy_move($_FILES['tx_photoalbum_photos']['tmp_name']['photo'], $imgpath.$filename)){
                    //删除指定路径文件
                    $photo = $this->photosRepository->querySingleRow("sys_file",$fuid);
                    unlink(PATH_site."fileadmin".$photo['identifier']);
                    
                    //修改sys_file
                    $arrSet=array(
                        "tstamp"=>time(),
                        "identifier"=>$album['folder'].$filename,
                        "identifier_hash"=> sha1($album['folder'].$filename),
                        "folder_hash"=>sha1(substr($album['folder'], 0, -1)),
                        "extension"=>end(explode('.', $filename)),
                        "mime_type"=>$_FILES['tx_photoalbum_photos']['type']['photo'],
                        "name"=>$filename,
                        "size"=>$_FILES['tx_photoalbum_photos']['size']['photo'],
                        "modification_date"=>time(),
                    );
                    $this->photosRepository->updateRows("sys_file",$arrSet,array("uid"=>$fuid));
                    
                    //修改sys_file_metadata
                    list($width, $height, $type, $attr) = getimagesize($imgpath.$filename); 
                    $arrSet=array(
                        "tstamp"=>time(),
                        "title"=>$title,
                        "width"=>$width,
                        "height"=>$height,
                        "description"=>$description
                    );
                    $this->photosRepository->updateRows("sys_file_metadata",$arrSet,array("uid"=>$muid));
                }
            }else{
                //如果文件为空，只修改
                $this->addFlashMessage('修改成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
                //修改sys_file_metadata
                $arrSet=array(
                    "tstamp"=>time(),
                    "title"=>$title,
                    "description"=>$description
                );
                $this->photosRepository->updateRows("sys_file_metadata",$arrSet,array("uid"=>$muid));
            }
        }
        $this->redirect('list', 'Photos', 'PhotoAlbum', ['album' =>$albumuid]);
    }

    /**
     * action delete
     * 
     * @return void
     */
    public function deleteAction()
    {
        $fuid=$this->request->hasArgument('fuid') ? $this->request->getArgument('fuid') : '';//sys_file的uid
        $muid=$this->request->hasArgument('muid') ? $this->request->getArgument('muid') : '';//sys_file_metadata 的uid
        $albumuid=$this->request->hasArgument('albumuid') ? $this->request->getArgument('albumuid') : '';//albumuid
        if($fuid=="" || $muid==""){
            $this->addFlashMessage('删除失败！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        }else{
            $this->addFlashMessage('删除成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
            //查找图片完整路径
            $photo = $this->photosRepository->querySingleRow("sys_file",$fuid);
            //删除指定路径文件
            $imgpath=PATH_site ."fileadmin".$photo['identifier'];
            unlink($imgpath);
            
            //删除sys_file
            $this->photosRepository->deleteByUid("sys_file",$fuid);
            //删除sys_file_metadata
            $this->photosRepository->deleteByUid("sys_file_metadata",$muid);
        }
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list', 'Photos', 'PhotoAlbum', ['album' =>$albumuid]);
    }
}
