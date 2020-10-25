<?php

namespace CigoAdminLib\Lib\Uploader\FileUploader;

use CigoAdminLib\Lib\Uploader\FileUploader;
use CigoAdminLib\Lib\Uploader\Uploader;

use CigoAdminLib\Lib\ErrorCode;
use Think\Image;

/**
 * 图片上传接口
 */
class Img extends FileUploader
{
    const CTRL_TYPES_THUMB = 'thumb';
    const CTRL_TYPES_CROP = 'crop';
    const CTRL_TYPES_WATER = 'water';
    const CTRL_TYPES_TXT = 'text';

    protected function getConfigFileLimit($configs)
    {
        return $configs['fileLimit']['img'];
    }

    protected function getFileType()
    {
        return Uploader::FILE_TYPE_IMG;
    }

    protected function ctrlUploadFile($args, $file, $configs)
    {
        // 实例化图片操作类
        $image = new Image();
        $image->open($file['saved_path_name']);

        // 检查操作参数
        if ($this->checkCtrlFileArgs($image, $args, $configs)) {
            // 组织保存图片信息
            $fileCtrl = array();
            $fileCtrl['ext'] = $file['ext'];
            $fileCtrl['type'] = $file['type'];
            // 保存图片至磁盘
            if (!$this->saveCtrlFileToDisk($image, $fileCtrl, $configs)) {
                return false;
            }
            // 保存图片信息至数据库
            if (!$this->saveCtrlFileToDb($fileCtrl)) {
                return false;
            }
            // 返回操作后的图片信息
            $this->makeResponse(true, $this->getResponseFileInfo($fileCtrl), '上传成功！');
        } else {
            //创建默认缩略图
            $fileThumb = array();
            $fileThumb['saved_path_name_for_thumb_middle'] = $file['saved_path'] . '/' . $file['saved_name'] . '_thumb_middle.' . $file['ext'];
            $fileThumb['saved_path_name_for_thumb_small'] = $file['saved_path'] . '/' . $file['saved_name'] . '_thumb_small.' . $file['ext'];
            $image->thumb(768, 768)->save($fileThumb['saved_path_name_for_thumb_middle']);
            $image->thumb(300, 300)->save($fileThumb['saved_path_name_for_thumb_small']);
            //更新数据库
            $data = array(
                'id' => $file['id'],
                'thumb_small' => $fileThumb['saved_path_name_for_thumb_small'],
                'thumb_middle' => $fileThumb['saved_path_name_for_thumb_middle']
            );
            $model = D('Files');
            $data = $model->create($data);
            $model->save($data);//TODO 检查错误

            $this->makeResponse(true, array_merge(
                $this->getResponseFileInfo($file),
                array(
                    'path_thumb_small' => $fileThumb['saved_path_name_for_thumb_small'],
                    'path_thumb_middle' => $fileThumb['saved_path_name_for_thumb_middle']
                )), '上传成功！');
        }
        return true;
    }

    /**
     * @param Image $image
     * @param $args
     * @param $configs
     * @return bool
     */
    private function checkCtrlFileArgs($image, $args, $configs)
    {
        $doCtrlFlag = false;
        if (// 检查缩略图
            isset($args[Img::CTRL_TYPES_THUMB]) && !empty($args[Img::CTRL_TYPES_THUMB]) &&
            isset($args[Img::CTRL_TYPES_THUMB]['width']) && !empty($args[Img::CTRL_TYPES_THUMB]['width']) &&
            isset($args[Img::CTRL_TYPES_THUMB]['height']) && !empty($args[Img::CTRL_TYPES_THUMB]['height'])
        ) {
            $image->thumb(
                $args[Img::CTRL_TYPES_THUMB]['width'],
                $args[Img::CTRL_TYPES_THUMB]['height'],
                ((isset($args[Img::CTRL_TYPES_THUMB]['type']) && !empty($args[Img::CTRL_TYPES_THUMB]['type']))
                    ? $args[Img::CTRL_TYPES_THUMB]['type']
                    : Image::IMAGE_THUMB_SCALE
                )
            );
            $doCtrlFlag = true;
        } else if (// 检查截图操作
            isset($args[Img::CTRL_TYPES_CROP]) && !empty($args[Img::CTRL_TYPES_CROP]) &&
            isset($args[Img::CTRL_TYPES_CROP]['w']) && !empty($args[Img::CTRL_TYPES_CROP]['w']) &&
            isset($args[Img::CTRL_TYPES_CROP]['h']) && !empty($args[Img::CTRL_TYPES_CROP]['h']) &&
            isset($args[Img::CTRL_TYPES_CROP]['x']) && !empty($args[Img::CTRL_TYPES_CROP]['x']) &&
            isset($args[Img::CTRL_TYPES_CROP]['y']) && !empty($args[Img::CTRL_TYPES_CROP]['y'])
        ) {
            $image->crop(
                $args[Img::CTRL_TYPES_CROP]['w'],
                $args[Img::CTRL_TYPES_CROP]['h'],
                $args[Img::CTRL_TYPES_CROP]['x'],
                $args[Img::CTRL_TYPES_CROP]['y'],
                ((isset($args[Img::CTRL_TYPES_CROP]['width']) && !empty($args[Img::CTRL_TYPES_CROP]['width']))
                    ? $args[Img::CTRL_TYPES_CROP]['width']
                    : null
                ),
                ((isset($args[Img::CTRL_TYPES_CROP]['height']) && !empty($args[Img::CTRL_TYPES_CROP]['height']))
                    ? $args[Img::CTRL_TYPES_CROP]['height']
                    : null
                )
            );
            $doCtrlFlag = true;
        }
        // 检查水印图片
        if (isset($args[Img::CTRL_TYPES_WATER]) && !empty($args[Img::CTRL_TYPES_WATER])) {
            $image->water(
                $configs['waterImg'],
                ((isset($args[Img::CTRL_TYPES_WATER]['locate']) && !empty($args[Img::CTRL_TYPES_WATER]['locate']))
                    ? $args[Img::CTRL_TYPES_WATER]['locate']
                    : Image::IMAGE_WATER_SOUTHEAST
                ),
                ((isset($args[Img::CTRL_TYPES_WATER]['alpha']) && !empty($args[Img::CTRL_TYPES_WATER]['alpha']))
                    ? $args[Img::CTRL_TYPES_WATER]['alpha']
                    : 80
                )
            );
            $doCtrlFlag = true;
        }
        // 检查文字水印操作
        if (isset($args[Img::CTRL_TYPES_TXT]) && !empty($args[Img::CTRL_TYPES_TXT])) {
            $image->text(
                (isset($args[Img::CTRL_TYPES_TXT]['text']) && !empty($args[Img::CTRL_TYPES_TXT]['text']))
                    ? $args[Img::CTRL_TYPES_TXT]['text']
                    : ((isset($configs['waterText']) && !empty($configs['waterText']))
                    ? $configs['waterText']
                    : (!is_null(C('AppName'))
                        ? C('AppName')
                        : ''
                    )
                ),
                $configs['waterTextFont'],
                ((isset($args[Img::CTRL_TYPES_TXT]['size']) && !empty($args[Img::CTRL_TYPES_TXT]['size']))
                    ? $args[Img::CTRL_TYPES_TXT]['size']
                    : 11
                ),
                ((isset($args[Img::CTRL_TYPES_TXT]['color']) && !empty($args[Img::CTRL_TYPES_TXT]['color']))
                    ? $args[Img::CTRL_TYPES_TXT]['color']
                    : '#00000000'
                ),
                ((isset($args[Img::CTRL_TYPES_TXT]['locate']) && !empty($args[Img::CTRL_TYPES_TXT]['locate']))
                    ? $args[Img::CTRL_TYPES_TXT]['locate']
                    : Image::IMAGE_WATER_SOUTHEAST
                ),
                ((isset($args[Img::CTRL_TYPES_TXT]['offset']) && !empty($args[Img::CTRL_TYPES_TXT]['offset']))
                    ? $args[Img::CTRL_TYPES_TXT]['offset']
                    : 0
                ),
                ((isset($args[Img::CTRL_TYPES_TXT]['angle']) && !empty($args[Img::CTRL_TYPES_TXT]['angle']))
                    ? $args[Img::CTRL_TYPES_TXT]['angle']
                    : 0
                )
            );

            $doCtrlFlag = true;
        }
        return $doCtrlFlag;
    }

    /**
     * @param Image $image
     * @param $fileCtrl
     * @param $configs
     * @return bool
     */
    private function saveCtrlFileToDisk($image, &$fileCtrl, $configs)
    {
        $fileCtrl['root_path'] = $configs['rootPath'];
        $fileCtrl['sub_path'] = $this->getSubPath();
        $fileCtrl['saved_path'] = $fileCtrl['root_path'] . '/' . $fileCtrl['sub_path'];//TODO 注意路径'./',避免linux造成'./'和'/'歧义
        $fileCtrl['saved_name'] = $this->getSaveFileName();
        $fileCtrl['saved_path_name'] = $fileCtrl['saved_path'] . '/' . $fileCtrl['saved_name'] . '.' . $fileCtrl['ext'];

        //检查是否允许覆盖
        if (!$configs['replace'] && is_file($fileCtrl['saved_path_name'])) {
            $this->makeResponse(false, null, '保存文件重名，请重新尝试！', ErrorCode::ERROR_CODE_UPLOAD_FILE_SAVE_ERROR);
            return false;
        }
        //检查保存目录是否存在
        if (!$this->mkPath($fileCtrl['saved_path'])) {
            return false;
        }
        //检查保存目录是否可写
        if (!$this->checkPathWritable($fileCtrl['saved_path'])) {
            return false;
        }
        //保存图片并获取文件信息
        $image->save($fileCtrl['saved_path_name']);

        $fileCtrl['md5'] = md5_file($fileCtrl['saved_path_name']);
        $fileCtrl['sha1'] = sha1_file($fileCtrl['saved_path_name']);

        return true;
    }

    private function saveCtrlFileToDb(&$fileCtrll)
    {
        $model = D('Files');
        $data = array(
            'type' => $this->getFileType(),
            'name' => $fileCtrll['saved_name'],
            'ext' => $fileCtrll['ext'],
            'mime' => $fileCtrll['type'],
            'path' => $fileCtrll['saved_path_name'],
            'md5' => $fileCtrll['md5'],
            'sha1' => $fileCtrll['sha1']
        );
        $data = $model->create($data);
        $res = $model->add($data);
        if ($res === false) {
            $this->makeResponse(false, null, '保存数据库失败！', ErrorCode::ERROR_CODE_UPLOAD_DB_SAVE_ERROR);
            return false;
        }
        $fileCtrll['id'] = $res;
        return true;
    }
}

