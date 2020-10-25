<?php

/*
 * 邮件发送接口
 */

class Mail extends Eloquent {

    public static function send() {
        Mail::send('emails.welcome', array('key' => 'value'), function($message) {
            $message->to('foo@example.com', 'John Smith')->subject('Welcome!');
        });
    }

}
