<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-10-21
 * Time: 下午2:49
 */

use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * 该类用于封装图片上传操作，在进一步了解Symfony File类之前，采用如下折中实现.
 *
 * Class UploadedImage
 */
class UploadedImage
{
    /**
     * 设置上传的规则.
     *
     * @param $rules 关于规则的关联数组.
     * 		allow: 数组形式， 接受的扩展名， 默认包括 jpg、jpeg、png、gif
     * 		maxSize: 可接受的最大文件大小，单位为Byte， 默认3MB
     * 		target: 上传的目标目录，basePath从 /public 文件夹算起
     */
    public function setRules($rules)
    {
        $this->setProperty('allow', $rules, function($allowExts){
            return explode('|', $allowExts);
        });

        $this->setProperty('name', $rules);

        $this->setProperty('maxSize', $rules);

        $this->setProperty('target', $rules);
    }

    /**
     * 实现上传操作.
     *
     * @return bool 成功上传返回true，否则返回false
     */
    public function doUpload()
    {
        $this->file = Input::file($this->name);

        if ($this->file && $this->checkRules()) {
            try {
                $this->lastName = $this->getNewNameByTime();
                $this->file->move($this->target, $this->lastName);
            } catch (FileException $err) {
                echo $err;
                return false;
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * 得到上传后的文件名.
     *
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * 类内使用，设置各项属性.
     *
     * @param $propertyName
     * @param $rulesArray
     * @param null $callbackFun
     */
    protected function setProperty($propertyName, $rulesArray, $callbackFun = null)
    {
        if (is_array($rulesArray) && isset($rulesArray[$propertyName])) {

            if (is_callable($callbackFun)) {
                $this->$propertyName = call_user_func_array($callbackFun, [$rulesArray[$propertyName]]);
            } else {
                $this->$propertyName = $rulesArray[$propertyName];
            }
        }
    }

    /**
     * 检查是否符合规定.
     *
     * @return bool
     */
    protected function checkRules()
    {
        if (!$this->file->isValid()) {
            return false;
        }

        if (!in_array($this->file->getClientOriginalExtension(), $this->allow)) {
            return false;
        }

        if ($this->file->getClientSize() >= $this->maxSize) {
            return false;
        }

        if (stripos($this->file->getClientMimeType(), 'image') === false) {
            return false;
        }

        return true;
    }

    /**
     * 得到根据时间生成的新的图片文件名
     *
     * @return string
     */
    protected function getNewNameByTime()
    {
        return (string)time().'.'.$this->file->getClientOriginalExtension();
    }

    protected $allow = ['jpg', 'jpeg', 'png', 'gif'];	//可接受的文件扩展名

    protected $name = 'image';	//图片文件的名称

    protected $maxSize = 3145728;	//可接受文件的最大的体积

    protected $target = 'uploaded';	//上传的目标文件夹

    protected $errorMessages = [];

    protected $file;	//引用 Symfony\Component\HttpFoundation\File 对象的实例

    protected $lastName;	//上传成功后的文件名

}
