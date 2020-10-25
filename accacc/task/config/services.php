<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

	'fanfou' => [
			'client_id' => env('FANFOU_CLIENT_ID'),
			'client_secret' => env('FANFOU_CLIENT_SECRET'),
			'redirect' => env('FANFOU_REDIRECT')
	],
		
	'github' => [
			'client_id' => env('GITHUB_CLIENT_ID'),         // Your GitHub Client ID
			'client_secret' => env('GITHUB_CLIENT_SECRET'), // Your GitHub Client Secret
			'redirect' => env('GITHUB_REDIRECT'),
	],
		
	'twitter' => [
			'client_id' => env('TWITTER_CLIENT_ID'),         // Your GitHub Client ID
			'client_secret' => env('TWITTER_CLIENT_SECRET'), // Your GitHub Client Secret
			'redirect' => env('TWITTER_REDIRECT'),
	],
		
	'weibo' => [
			'client_id' => env('WEIBO_CLIENT_ID'),         // Your WeiBo Client ID
			'client_secret' => env('WEIBO_CLIENT_SECRET'), // Your WeiBo Client Secret
			'redirect' => env('WEIBO_REDIRECT'),
	],
		
	'wechat' => [
			'client_id' => env('WECHAT_CLIENT_ID'),         // Your Weixin Client ID
			'client_secret' => env('WECHAT_CLIENT_SECRET'), // Your Weixin Client Secret
			'redirect' => env('WECHAT_REDIRECT'),
	],
		
	'wechatmini' => [
			'client_id' => env('WECHATMINI_CLIENT_ID'),         // Your Weixin Client ID
			'client_secret' => env('WECHATMINI_CLIENT_SECRET'), // Your Weixin Client Secret
			'redirect' => env('WECHATMINI_REDIRECT'),
	],

];
