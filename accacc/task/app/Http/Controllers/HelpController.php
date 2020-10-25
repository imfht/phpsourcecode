<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Http\Utils\ErrorCodeUtil;

/**
 * 帮助控制器
 *
 * @author edison.an
 *        
 */
class HelpController extends Controller {
	
	/**
	 * 构造方法
	 *
	 * @return void
	 */
	public function __construct() {
		$this->middleware ( 'auth' );
	}
	
	/**
	 * 反馈页
	 *
	 * @param Request $request        	
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function feedback(Request $request) {
		return view ( 'help.feedback', [ 
				'from' => $request->has ( 'from' ) ? $request->from : '' 
		] );
	}
	
	/**
	 * 提交反馈
	 *
	 * @param Request $request        	
	 * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Routing\ResponseFactory
	 */
	public function feedbackStore(Request $request) {
		$this->validate ( $request, [ 
				'content' => 'required' 
		] );
		
		$feedback = new Feedback ();
		$feedback->user_id = isset ( $request->user ()->id ) ? $request->user ()->id : null;
		$feedback->from = $request->from;
		$feedback->content = $request->content;
		$feedback->save ();
		
		if ($request->ajax () || $request->wantsJson () || $request->has ( 'json_wants' )) {
			$resp = $this->responseJson ( ErrorCodeUtil::OK_CODE, array () );
			return response ( $resp );
		} else {
			return redirect ( '/help/feedback' )->with ( 'message', '反馈成功' );
		}
	}
}
