<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;
use App\Models\KindleLog;
use Develpr\Phindle\Phindle;
use Develpr\Phindle\Content;
use App\Http\Utils\ErrorCodeUtil;
use App\Services\SettingService;

/**
 * Kindle相关控制器
 *
 * @author edison.an
 *        
 */
class KindleController extends Controller {
	
	/**
	 * SettingService 实例.
	 *
	 * @var SettingService
	 */
	protected $settings;
	
	/**
	 * 构造方法
	 *
	 * @param SettingService $settings        	
	 * @return void
	 */
	public function __construct(SettingService $settings) {
		$this->middleware ( 'auth', [ 
				'except' => [ 
						'welcome' 
				] 
		] );
		
		$this->settings = $settings;
	}
	
	/**
	 * 首页
	 *
	 * @param Request $request        	
	 */
	public function index(Request $request) {
		$page_params = array ();
		
		$setting = $request->user()->setting;
		
		if (empty ( $setting )) {
			$setting = new Setting ();
		}
		
		return view ( 'kindles.index', [ 
				'setting' => $setting 
		] );
	}
	
	/**
	 * 测试kindle邮箱
	 *
	 * @param Request $request        	
	 */
	public function test(Request $request) {
		$user = $request->user ();
		$setting = $user->setting;
		
		if (! isset ( $setting->kindle_email ) || empty ( $setting->kindle_email )) {
			echo 'empty kindle_email';
			exit ();
		}
		
		$kindleLog = new KindleLog ();
		$kindleLog->user_id = $request->user ()->id;
		$kindleLog->type = 1;
		$kindleLog->status = 1;
		$kindleLog->save ();
		
		$phindle = new Phindle ( array (
				'title' => "Montage GTD - 测试文件",
				'publisher' => "Montage GTD",
				'creator' => $user->name,
				'language' => 'zh-CN',
				'subject' => 'Test', // @see https://www.bisg.org/complete-bisac-subject-headings-2013-edition
				'description' => '这是一个测试文件!',
				'path' => config ( "app.storage_path" ) . '/ebooks', // The path that temp files will be stored, as well as the location of the final ebook mobi file
				'isbn' => '666666666666666',
				'staticResourcePath' => config ( "app.storage_path" ) . '/ebooks/static', // The absolute path to your static resources referenced in html (images, css, etc)
				'cover' => 'cover.jpg', // The relative path of your cover image
				'kindlegenPath' => '/usr/local/bin/kindlegen', // The path to the kindlegen utility
				'downloadImages' => true 
		) ); // Should images be downloaded from the web if found in your html?
		
		$title = '测试文件';
		$html = '<meta http-equiv="Content-Type" content="text/html;charset=utf-8"><h3>' . $title . '</h3>当你收到此测试文件，说明你的配置正确，快来Montage GTD订阅你喜欢的文档吧!Montage GTD是个综合性的网站，在这里你还可以更高效的完成每一件事，快来体验吧。';
		
		$content = new Content ();
		$content->setHtml ( $html );
		$content->setTitle ( $title );
		$phindle->addContent ( $content );
		
		$phindle->process ();
		// $path = config("app.storage_path") . '/ebooks/' . $phindle->getAttribute('uniqueId') . '.mobi';
		$path = $phindle->getMobiPath ();
		
		$kindleLog->path = $path;
		$kindleLog->status = 2;
		$kindleLog->save ();
		
		Log::info ( 'send to kindle test:' . $user->id . '|' . $path );
		
		Mail::send ( 'emails.kindle', [ 
				'user' => $user,
				'setting' => $setting,
				'path' => $path 
		], function ($m) use ($user, $setting, $path) {
			$m->to ( $setting->kindle_email, $user->name )->subject ( 'Send To Kindle' );
			$m->attach ( $path );
		} );
		
		$kindleLog->status = 3;
		$kindleLog->save ();
		echo 'success!';
		exit ();
	}
}
