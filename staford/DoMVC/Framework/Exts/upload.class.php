<?php

/**
 * @name Upload
 * @abstract 文件上传处理类
 * @author 暮雨秋晨
 * @copyright 2014
 */

class Upload
{
    private static $Instance = null; //当前类实例化对象存储静态变量
    private $saveFileName; //保存后的文件名
    private $saveFilePrefix; //上传文件的后缀名
    private $maxSize = 10485760; //文件大小限制，默认最大10M
    private $uploadRootPath = 'upload'; //文件存放根目录，默认为upload文件夹
    private $uploadPath; //文件存放路径
    private $filePrefix = array(
        'png',
        'jpg',
        'gif',
        'jpeg'); //允许上传的文件名后缀，默认PNG、JPG、GIF、JPEG
    private $error = array(); //存储错误信息

    /**
     * 取得实例化对象
     * @param string $formName 表单名称
     */
    public static function getInstance()
    {
        if (!self::$Instance) {
            self::$Instance = new Upload();
        }
        return self::$Instance;
    }

    /**
     * 设置文件大小限制
     */
    public function setMaxSize($size)
    {
        $size = $size + 0;
        if ($size > 0) {
            $this->maxSize = $size;
            return true;
        }
    }

    /**
     * 设置文件存放根目录
     */
    public function setRootPath($path)
    {
        if (!empty($path)) {
            $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
            $this->uploadRootPath = $path;
            return true;
        }
    }

    /**
     * 设置文件存放目录
     */
    public function setPath($path)
    {
        if (!empty($path)) {
            $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
            $this->uploadPath = $path;
            return true;
        }
    }

    /**
     * 设置文件后缀名信息，可选覆盖或是增加
     */
    public function setPrefix($prefix, $isAdd = true)
    {
        if (!empty($prefix)) {
            if ($isAdd) {
                if (is_array($prefix)) {
                    foreach ($prefix as $pre) {
                        $this->filePrefix[] = $pre;
                    }
                } else {
                    $this->filePrefix[] = $prefix;
                }
                return true;
            } else {
                $this->filePrefix = array($prefix);
                return true;
            }
        }
    }

    /**
     * 执行入口
     */
    public function run($formName)
    {
        if (!is_dir($this->uploadRootPath)) {
            mkdir($this->uploadRootPath, 0777, true);
        }
        if (!is_dir($this->uploadRootPath . DIRECTORY_SEPARATOR . $this->uploadPath)) {
            mkdir($this->uploadRootPath . DIRECTORY_SEPARATOR . $this->uploadPath, 0777, true);
        }
        if (!empty($formName)) {
            if ($this->checkError($formName) && $this->checkPrefix($formName) && $this->
                checkSize($formName)) {
                $fileName = $this->makeOnlyFileName() . '.' . $this->saveFilePrefix;
                $fullFilePath = $this->uploadRootPath . DIRECTORY_SEPARATOR . $this->uploadPath .
                    DIRECTORY_SEPARATOR . $fileName;
                if ($this->saveFile($formName, $fullFilePath)) {
                    return array('name' => $fileName, 'path' => str_replace(DIRECTORY_SEPARATOR, '/',
                            $fullFilePath));
                } else {
                    return false;
                }
            }
        }
    }

    private function checkPrefix($formName)
    {
        $name = $_FILES[$formName]['name'];
        if (strpos($name, '.')) {
            $name = explode('.', $name);
            $name = $name[count($name) - 1];
            if (in_array($name, $this->filePrefix)) {
                $this->saveFilePrefix = $name; //将后缀名存储到属性中备用
                return true;
            } else {
                $this->error[] = '文件格式非法';
                return false;
            }
        } else {
            $this->error[] = '文件格式非法';
            return false;
        }
    }

    private function checkSize($formName)
    {
        $size = $_FILES[$formName]['size'];
        if ($size > $this->maxSize || $size < 1) {
            $this->error[] = '文件大小超过系统限制或不是一个正常的文件';
            return false;
        } else {
            return true;
        }
    }

    private function checkError($formName)
    {
        $error = $_FILES[$formName]['error'];
        switch ($error) {
            case 0:
                return true;
                break;
            case 1:
                $this->error[] = '文件大小超出php.ini限制';
                return false;
                break;
            case 2:
                $this->error[] = '文件大小超出HTML表单限制';
                return false;
                break;
            case 3:
                $this->error[] = '文件未完全上传';
                return false;
                break;
            case 4:
                $this->error[] = '没有上传文件';
                return false;
                break;
        }
    }

    private function makeOnlyFileName()
    {
        return uniqid('upload_');
    }

    private function saveFile($formName, $fullPath)
    {
        if (isset($_FILES[$formName]) && !empty($fullPath)) {
            if (is_file($fullPath)) {
                $this->error[] = '文件已经存在';
                return false;
            } else {
                $file_tmp = $_FILES[$formName]['tmp_name'];
                if (move_uploaded_file($file_tmp, $fullPath)) {
                    $this->delete($file_tmp);
                    return true;
                } else {
                    $this->error[] = '无法保存文件';
                    return false;
                }
            }
        }
    }

    /**
     * @abstract 删除指定文件
     */
    private function delete($path)
    {
        if (@unlink($path)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @abstract 获取错误信息
     */
    public function getError()
    {
        if (!empty($this->error)) {
            return $this->error[0];
        } else {
            return false;
        }
    }

}
?>