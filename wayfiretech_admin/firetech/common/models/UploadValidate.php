<?php
/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-04-09 11:30:29
 * @Last Modified by:   Wang Chunsheng 2192138785@qq.com
 * @Last Modified time: 2020-04-09 11:30:30
 */
 

namespace common\models;

use yii\base\Model;

/**
 * Class UploadValidate 文件上传验证
 * 使用model验证文件上传字段
 * ```
 * $model = new UploadValidate($config_name);
 * ```
 *
 * @package common\models
 * @author  windhoney
 * @package common\models
 */
class UploadValidate extends Model
{
    
    /**
     * @var string 表单字段名
     */
    public $file;
    /**
     * @var array|string 扩展名
     */
    public $extensions;
    /**
     * @var int 文件大小 最大值  单位字节
     */
    public $max_size = 60 * 1024 * 1024;
    /**
     * @var int 文件大小 最小值  单位字节
     */
    public $min_size = 1;
    /**
     * @var array|string  MIME TYPE
     */
    public $mime_type;
    /**
     * @var string 上传失败后返回信息
     */
    public $message = '上传失败';
    
    /**
     * UploadValidate constructor.
     *
     * @param string $config_name `@app/config/params.php` 文件上传验证配置项名称
     */
    public function __construct($config_name)
    {
        parent::__construct();
        $upload_config = \Yii::$app->params[$config_name];
        $this->extensions = $upload_config['extensions']??'';
        $this->mime_type = $upload_config['mime_types']??'';
        $this->max_size = $upload_config['max_size']??'';
        $this->min_size = $upload_config['min_size']??'';
        $this->message = $upload_config['message']??'';
    }
    
    /**
     * @inheritdoc 验证规则
     */
    public function rules()
    {
        $file_rule = [['file'], 'file'];
        if ($this->extensions) {
            $file_rule['extensions'] = $this->extensions;
        }
        if ($this->mime_type) {
            $file_rule['mimeTypes'] = $this->mime_type;
        }
        if ($this->max_size) {
            $file_rule['maxSize'] = $this->max_size;
        }
        if ($this->min_size) {
            $file_rule['minSize'] = $this->min_size;
        }
        if ($this->message) {
            $file_rule['message'] = $this->message;
        }
        $rules = [$file_rule];
        
        return $rules;
    }
}