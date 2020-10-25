# Aliyuns

- **阿里云媒体处理，转码，搜索管道，提交作业等**

## Requirements

- **PHP 5.6+**

## Example

#### **使用说明**

*   **下载扩展** ：
>   **git clone git@gitee.com:china_wangyu/aliyuns.git**
*   **引入命名空间** ：
>    **use aliyun\Amts;**
*   **使用代码**：
```php
         /**
          * 实例化转码类Amts
          * @param oss_cn_location     阿里云所属区域                     
          * @param access_key_id       阿里云授权 access_key_id
          * @param access_key_secret   阿里云授权 access_key_secret
          * @param bucket              阿里云OSS bucket名称
          * @param template_id         阿里云媒体处理：转码模板
         */
         Amts::instance(阿里云 oss_cn_location,阿里云授权 access_key_id,阿里云授权 access_key_secret,阿里云OSS bucket, 阿里云转码模板ID);
         # oss输入对象
         $oss_input_object = 'video/live.mp4'; # 阿里云视频源地址
         # oss输出对象
         $oss_output_object = [
              'oss_save_path' => 'video/'.time().'.mp4', # 阿里云保存地址
              'Container' => array('Format' => 'mp4'),   # 阿里云转码后缀格式
              'Video' => array('Codec' => 'H.264',       # 阿里云视频转码格式 
                               'Bitrate' => 480,         # 阿里云清晰度比特率  例：480P
                               'Width' => 640,           # 阿里云视频宽度640
                               'Fps' => 25),             # 阿里云视频FPS值
              'Audio' => array('Codec' => 'AAC',         # 阿里云音频格式
                               'Bitrate' => 128,         # 阿里云音频清晰比特率
                               'Channels' => 2,          # 阿里云音频渠道
                               'Samplerate' => 44100),   # 阿里云音频采样率
          ];
         # 实例化转码类Amts 执行视频转码方法 runVideoTranscoding()
         Amts::runVideoTranscoding($oss_input_object,$oss_output_object);
```

## Contact The Author

>   **Author : wene**

>   **QQ : 354007048**

>   **Emali : china_wangyu@aliyun.com**
