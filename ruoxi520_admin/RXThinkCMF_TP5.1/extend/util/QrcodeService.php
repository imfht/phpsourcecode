<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace util;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;

/**
 * 二维码-生成类
 *
 * @author 牧羊人
 * @date 2019-04-30
 *
 */
class QrcodeService
{
    // 参数声明
    protected $_qr;
    protected $_encoding = 'UTF-8';              // 编码类型
    protected $_size = 300;                  // 二维码大小
    protected $_logo = false;                // 是否需要带logo的二维码
    protected $_logo_url = '';                   // logo图片路径
    protected $_logo_size = 80;                   // logo大小
    protected $_title = false;                // 是否需要二维码title
    protected $_title_content = '';                   // title内容
    protected $_generate = false;                 // true写入文件 false直接显示
    protected $_file_name = '';                   // 写入文件路径
    const MARGIN = 10;                        // 二维码内容相对于整张图片的外边距
    const WRITE_NAME = 'png';                     // 写入文件的后缀名
    const FOREGROUND_COLOR = ['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0];          // 前景色
    const BACKGROUND_COLOR = ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0];    // 背景色

    /**
     * 构造方法
     *
     * @param unknown $config
     * @author 牧羊人
     * @date 2019-04-30
     */
    public function __construct($config)
    {
        isset($config['generate']) && $this->_generate = $config['generate'];
        isset($config['encoding']) && $this->_encoding = $config['encoding'];
        isset($config['size']) && $this->_size = $config['size'];
        isset($config['logo']) && $this->_logo = $config['logo'];
        isset($config['logo_url']) && $this->_logo_url = $config['logo_url'];
        isset($config['logo_size']) && $this->_logo_size = $config['logo_size'];
        isset($config['title']) && $this->_title = $config['title'];
        isset($config['title_content']) && $this->_title_content = $config['title_content'];
        isset($config['file_name']) && $this->_file_name = $config['file_name'];
    }

    /**
     * 生成二维码
     *
     * @param unknown $content 需要写入的内容
     * @return Ambigous <multitype:, multitype:boolean string multitype:string  , multitype:boolean string NULL >|multitype:boolean string
     * @author 牧羊人
     * @date 2019-04-30
     */
    public function create_qrcode($content)
    {
        $this->_qr = new QrCode($content);
        $this->_qr->setSize($this->_size);
        $this->_qr->setWriterByName(self::WRITE_NAME);
        $this->_qr->setMargin(self::MARGIN);
        $this->_qr->setEncoding($this->_encoding);
        $this->_qr->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH));   // 容错率
        $this->_qr->setForegroundColor(self::FOREGROUND_COLOR);
        $this->_qr->setBackgroundColor(self::BACKGROUND_COLOR);

        // 是否需要title
        if ($this->_title) {
            $this->_qr->setLabel($this->_title_content, 16, null, LabelAlignment::CENTER);
        }

        // 是否需要logo
        if ($this->_logo) {
            $this->_qr->setLogoPath($this->_logo_url);
            $this->_qr->setLogoWidth($this->_logo_size);
//             $this->_qr->setLogoSize(150, 200);
        }

        $this->_qr->setRoundBlockSize(true);
        $this->_qr->setValidateResult(false);
        $this->_qr->setWriterOptions(['exclude_xml_declaration' => true]);

        if ($this->_generate) {
            // 写入文件
            $file_name = $this->_file_name;
            return $this->generate_image($file_name);
        } else {
            // 展示二维码
            header('Content-Type: ' . $this->_qr->getContentType());
            return $this->_qr->writeString();
        }
    }

    /**
     * 生成文件
     *
     * @param unknown $file_name 目录文件 例: /tmp
     * @return multitype:boolean string multitype:string  |multitype:boolean string NULL
     * @author 牧羊人
     * @date 2019-04-30
     */
    public function generate_image($file_name)
    {
        $file_path = $file_name . DIRECTORY_SEPARATOR . uniqid() . '.' . self::WRITE_NAME;
        if (!file_exists($file_name)) {
            mkdir($file_name, 0777, true);
        }
        try {
            $this->_qr->writeFile($file_path);
            $data = [
                'url' => $file_path,
                'ext' => self::WRITE_NAME,
            ];
            return ['success' => true, 'message' => 'write qrimg success', 'data' => $data];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage(), 'data' => ''];
        }
    }

}