<?php
namespace Jykj\Siteconfig\ViewHelpers;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Frans Saris <frans@beech.it>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\ImageService;

/**
 * ViewHelper for image
 */
class GetImgUrlFromContentViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {
    
    /**
     * Go through all given classes which implement the mediainterface
     * and use the proper ones to render the media element
     *
     * @param string $content
     * @param int $nuid
     * @param string $width width of the image. This can be a numeric value representing the fixed width of the image in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width for possible options.
     * @param string $height height of the image. This can be a numeric value representing the fixed height of the image in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width for possible options.
     * @param int $minWidth minimum width of the image
     * @param int $minHeight minimum height of the image
     * @param int $maxWidth maximum width of the image
     * @param int $maxHeight maximum height of the image
     * @param bool $treatIdAsReference given src argument is a sys_file_reference record
     * @param string|bool $crop overrule cropping of image (setting to FALSE disables the cropping set in FileReference)
     * @param bool $absolute Force absolute URL
     * @param int $mobileWidth
     * @param int $mobileHeight
     * @param int $mobileMaxWidth
     * @param int $mobileMaxHeight
     * @return string
     */
    public function initializeArguments()
    {
        $this->registerArgument('content', 'string', 'content', true);
        $this->registerArgument('nuid', 'int', 'nuid', false,0);
        $this->registerArgument('width', 'string', 'width of the image', false);
        $this->registerArgument('height', 'string', 'height of the image', false);
        $this->registerArgument('minWidth', 'int', 'minimum width of the image', false);
        $this->registerArgument('minHeight', 'int', 'minimum height of the image', false);
        $this->registerArgument('maxWidth', 'int', 'maximum width of the image', false);
        $this->registerArgument('maxHeight', 'int', 'maximum height of the image', false);
        $this->registerArgument('crop', 'bool', 'Force absolute URL', false);
        $this->registerArgument('absolute', 'bool', 'content', false,false);
        $this->registerArgument('mobileWidth', 'int', 'mobileWidth', false);
        $this->registerArgument('mobileHeight', 'int', 'mobileHeight', false);
        $this->registerArgument('mobileMaxWidth', 'int', 'mobileMaxWidth', false);
        $this->registerArgument('mobileMaxHeight', 'int', 'mobileMaxHeight', false);
    }
    
    public function render() {
        $content = $this->arguments['content'];
        $nuid= $this->arguments['nuid'];
        $width = $this->arguments['width']; 
        $height = $this->arguments['height']; 
        $minWidth = $this->arguments['minWidth']; 
        $minHeight = $this->arguments['minHeight']; 
        $maxWidth = $this->arguments['maxWidth']; 
        $maxHeight = $this->arguments['maxHeight']; 
        $crop = $this->arguments['crop']; 
        $absolute = $this->arguments['absolute'];
        $mobileWidth = $this->arguments['mobileWidth']; 
        $mobileHeight = $this->arguments['mobileHeight']; 
        $mobileMaxWidth = $this->arguments['mobileMaxWidth']; 
        $mobileMaxHeight = $this->arguments['mobileMaxHeight'];
        
        preg_match("/\<img[^\>]*?src\=[\"\'](.*?)[\"\']/is", $content, $matches);
        if(isset($matches[1])){
            if(substr($matches[1], 0, 1)=='/'){
                $file = substr($matches[1], 1);
            }else{
                $file = $matches[1];
            }
            if(preg_match("/^http\:/is", $matches[1])){
                $file = PATH_site.'typo3temp/pics/news_'.md5($matches[1]).'.'.end(explode('.', $matches[1]));
                //create pics folder 20170801 edit ysc
                if(!is_dir(PATH_site.'typo3temp/pics/')){
                    exec('mkdir '.PATH_site.'typo3temp/pics/');
                    exec('chmod -R 777 '.PATH_site.'typo3temp/pics/');
                }
                if(!is_file($file)){
                    //如果是远程图片，直接返回连接
                    if(@fopen($matches[1], 'r' )) {
                        $fpath='typo3temp/pics/news_';
                        return self::saveUrlImage($nuid,$matches[1],$fpath);
                    }else{
                        copy($matches[1], $file);
                    }
                }
            }else if(preg_match("/^https\:/is", $matches[1])){
                //如果是远程图片，直接返回连接
                if(@fopen($matches[1], 'r' )) {
                    $fpath='typo3temp/pics/news_';
                    return self::saveUrlImage($nuid,$matches[1],$fpath);
                }else{
                    copy($matches[1], $file);
                }
            }
            
            if(is_file($file)){
                $arguments = array(
                    'src' => $file,
                    'image' => null,
                    'width' => $width,
                    'height' => $height,
                    'minWidth' => $minWidth,
                    'minHeight' => $minHeight,
                    'maxWidth' => $maxWidth,
                    'maxHeight' => $maxHeight,
                    'treatIdAsReference' => false,
                    'crop' => $crop,
                    'absolute' => $absolute
                );
                if(GeneralUtility::_GET('mobile')){
                    if($arguments['mobileWidth']){
                        $arguments['width'] = $arguments['mobileWidth'];
                    }
                    if($arguments['mobileHeight']){
                        $arguments['height'] = $arguments['mobileHeight'];
                    }
                    if($arguments['mobileMaxWidth']){
                        $arguments['maxWidth'] = $arguments['mobileMaxWidth'];
                    }
                    if($arguments['mobileMaxHeight']){
                        $arguments['maxHeight'] = $arguments['mobileMaxHeight'];
                    }
                }
                
                $src = $arguments['src'];
                $image = $arguments['image'];
                $treatIdAsReference = $arguments['treatIdAsReference'];
                $crop = $arguments['crop'];
                $absolute = $arguments['absolute'];
                
                if (is_null($src) && is_null($image) || !is_null($src) && !is_null($image)) {
                    throw new Exception('You must either specify a string src or a File object.', 1382284105);
                }
                
                $imageService = self::getImageService();
                $image = $imageService->getImage($src, $image, $treatIdAsReference);
                
                if ($crop === null) {
                    $crop = $image instanceof FileReference ? $image->getProperty('crop') : null;
                }
                
                $processingInstructions = array(
                    'width' => $arguments['width'],
                    'height' => $arguments['height'],
                    'minWidth' => $arguments['minWidth'],
                    'minHeight' => $arguments['minHeight'],
                    'maxWidth' => $arguments['maxWidth'],
                    'maxHeight' => $arguments['maxHeight'],
                    'crop' => $crop,
                    'mobile' => GeneralUtility::_GET('mobile')?1:0
                );
                $processedImage = $imageService->applyProcessingInstructions($image, $processingInstructions);
                return $imageService->getImageUri($processedImage, $absolute);
            }
            
            return $file;
            
        }else{
            return '';
        }
    }
    
    /**
     * Return an instance of ImageService using object manager
     *
     * @return ImageService
     */
    protected static function getImageService()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        return $objectManager->get(ImageService::class);
    }
    /**
     *Return image path
     *
     *
     */
    protected static function saveUrlImage($uid,$url,$fpath){
        // mime 和 扩展名 的映射
        $mimes=array(
            'image/bmp'=>'bmp',
            'image/gif'=>'gif',
            'image/jpeg'=>'jpg',
            'image/png'=>'png',
            'image/x-icon'=>'ico'
        );
        
        if(($headers=get_headers($url, 1))!==false){
            // 获取响应的类型
            $type=$headers['Content-Type'];
        }
        
        if(isset($mimes[$type]))
        {
            $extension=$mimes[$type];
            $file_path = PATH_site.$fpath.$uid.".".$extension;
            //检测文件是否存在
            if(!is_file($file_path)){
                // 获取数据并保存
                $contents=file_get_contents($url);
                if(file_put_contents($file_path , $contents))
                {
                    // 这里返回出去的值是直接保存到数据库的路径 + 文件名
                    return $fpath.$uid.".".$extension;
                }
            }else{
                return $fpath.$uid.".".$extension;
            }
        }
        return "";
    }
    
}