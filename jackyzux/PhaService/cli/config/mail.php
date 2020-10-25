<?php
/**
 * 电子邮件传送 配置
 */

/**
 * SMTP 模式
 */
$smtp = [
    'driver'     => 'smtp',
    'host'       => 'smtp.gmail.com',
    'port'       => 465,
    'encryption' => 'ssl',
    'username'   => 'smices@gmail.com',
    'password'   => 'login_password',
    'from'       => [
        'email' => 'smices@gmail.com',
        'name'  => 'GMAIL:Jacky Ju ',
    ],
];


/**
 * Sendmail 模式
 */
$sendmail = [
    'driver'   => 'sendmail',
    'sendmail' => '/usr/sbin/sendmail -bs',
    'from'     => [
        'email' => 'example@gmail.com',
        'name'  => 'YOUR FROM NAME',
    ],
];


/**
 * PHP Mail 模式
 */
$phpmail = [
    'driver' => 'mail',
    'from'   => [
        'email' => 'example@gmail.com',
        'name'  => 'YOUR FROM NAME',
    ],
];


/**
 * 使用哪种模式,这里就 return 哪种模式
 */
return $smtp;


