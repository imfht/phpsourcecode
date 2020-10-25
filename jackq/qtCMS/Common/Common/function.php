<?php
/**
 * 转换Mb单位为Byte大小
 * @return int
 */
function convMb2B($mb) {
    return $mb * 1048576;
}
/**
 * 文件上传
 * @param  string $save_path 保存路径
 * @return array
 */
function upload($save_path, $size = -1, $rule = 'uniqid') {
    $upload = new \Org\Util\UploadFile();

    // 文件大小
    $upload->maxSize = $size;
    //设置附件上传目录
    $upload->savePath = WEB_ROOT . $save_path;
    // 上传文件名唯一
    $upload->saveRule = $rule;

    if (!$upload->upload()) {
        //捕获上传异常
        return array('status' => false, 'info' => $upload->getErrorMsg());
    }

    // 得到上传的文件路径
    $info = $upload->getUploadFileInfo();
    foreach ($info as $key => $item) {
        $info[$key]['path'] = $save_path . $item['savename'];
    }

    return array('status' => true, 'info' => $info);
}



/**
 * 发送邮件
 * @param  string $to         收件人邮箱
 * @param  string $name       收件人名称
 * @param  string $subject    邮件主题
 * @param  string $body       邮件正文
 * @return mixed
 */
function smtp_mail($to, $name, $subject = '', $body = '', array $config) {
    Vendor('PHPMailer.PHPMailerAutoload');

    $mail = new PHPMailer();
    // 设置字符集
    $mail->CharSet = $config['SMTP_CHARSET'];
    // 设定使用SMTP服务
    $mail->IsSMTP();
    // html格式内容
    $mail->IsHTML(true);
    // 启用 SMTP 验证功能
    $mail->SMTPAuth = $config['SMTP_AUTH'];
    // SMTP 安全协议
    $mail->SMTPSecure = 'ssl';
    // SMTP 服务器
    $mail->Host = $config['SMTP_HOST'];
    // SMTP服务器的端口号
    $mail->Port = $config['SMTP_PORT'];
    // SMTP服务器用户名
    $mail->Username = $config['SMTP_USER_NAME'];
    // SMTP服务器密码
    $mail->Password = $config['SMTP_PASSWORD'];
    // 设置发送者信息
    $mail->SetFrom($config['MAIL_FROM'], $config['SENDER_NAME']);
    // 设置邮件回复者信息
    $mail->AddReplyTo($config['MAIL_REPLY'], $config['REPLYER_NAME']);
    // 设置邮件主题
    $mail->Subject = $subject;
    // 设置邮件内容
    $mail->MsgHTML($body);
    // 兼容不支持html的邮件
    $mail->AltBody = 'This is the body in plain text';
    //
    $mail->AddAddress($to, $name);

    return $mail->Send() ? true : $mail->ErrorInfo;
}

/**
 * 把文件打包成为zip
 * @param  array $files       需要打包在同一个zip中的文件的路径
 * @param  string $out_dir    zip的文件的输出目录
 * @param  [type] $des_name   zip文件的名称m
 * @return boolean            打包是否成功
 */
function zip($files, $file_path, $out_dir, $des_name) {
    $zip = new ZipArchive;

    // 创建文件夹
    mkdir($out_dir);
    // 打包操作
    $result = $zip->open($out_dir . '/' . $des_name, ZipArchive::CREATE);
    if (true !== $result) {
        return false;
    }

    foreach ($files as $file) {
        // 添加文件到zip包中
        $zip->addFile($file_path . '/' . $file,
            str_replace('/', '', $file));
    }
    $zip->close();

    return true;
}

/**
 * 解压zip文件
 * @param  string $zip_file 需要解压的zip文件
 * @param  string $out_dir  解压文件的输出目录
 * @return boolean          解压是否成功
 */
function unzip($zip_file, $out_dir) {
    $zip = new ZipArchive();
    if (true !== $zip->open($zip_file)) {
        return false;
    }

    $zip->extractTo($out_dir);
    $zip->close();

    return true;
}

function simple_substr($str,$length,$allow_length=54,$strip_tag=false,$start=FALSE){

    if($strip_tag){
        $str = strip_tags($str);
    }
    $strlen = strlen($str);
    $content = '';
    if($strlen < $allow_length) {
        return $str;
    }
    $count = 0;
    $sing = 0;
    while(($length-3) != $count)
    {
        if(ord($str[$sing]) > 0xa0) {
            if(!$start || $start <= $count) {
                $content .= $str[$sing].$str[$sing+1].$str[$sing+2];
            }
            $sing += 3;
            $count++;
        }else{
            if(!$start || $start <= $count) {
                $content .= $str[$sing];
            }
            $sing++;
            $count++;
        }
    }
    return $content.'...';
}


