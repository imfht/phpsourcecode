<?php

namespace App\Http\Controllers;

use App\Http\Utils\ErrorCodeUtil;
use App\Models\Note;
use App\Models\NoteTagMap;
use App\Models\Tag;
use App\Services\NoteService;
use Illuminate\Http\Request;
use App\Services\TagService;
use App\Repositories\TagRepository;
use App\Models\Pomo;
use App\Models\Task;
use App\Models\Article;

/**
 * 笔记控制器
 *
 * @author edison.an
 *        
 */
class NoteController extends Controller {
	
	/**
	 * The note repository instance.
	 *
	 * @var NoteRepository
	 */
	protected $notes;
	protected $tags;
	
	/**
	 * Create a new controller instance.
	 *
	 * @param NoteRepository $notes        	
	 * @param TagRepository $tags        	
	 * @return void
	 */
	public function __construct(NoteService $notes, TagService $tags) {
		$this->middleware ( 'auth', [ 
				'except' => [ 
						'welcome' 
				] 
		] );
		
		$this->notes = $notes;
		$this->tags = $tags;
	}
	
	/**
	 * 欢迎页
	 *
	 * @param Request $request        	
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function welcome(Request $request) {
		return view ( 'notes.welcome', [ ] );
	}
	
	/**
	 * 首页.
	 *
	 * @param Request $request        	
	 */
	public function index(Request $request, $add_content = '') {
		$conditions = array (
				'user_id' => $request->user ()->id 
		);
		if ($request->has ( 'tag_id' )) {
			$conditions ['tag_id'] = $request->tag_id;
		} else if ($request->has ( 'keyword' )) {
			$conditions ['keyword'] = $request->keyword;
		} else if ($request->has ( 'pomo_id' )) {
			$conditions ['pomo_id'] = $request->pomo_id;
			$pomo = Pomo::where ( 'id', $request->pomo_id )->where ( 'user_id', $request->user ()->id )->first ();
			if (empty ( $pomo )) {
				abort ( 404, '系统异常，无此番茄!' );
			}
			$recommend_add_content = "#记录番茄#" . $pomo->name . "\n开始时间：" . date ( 'm月d日 H时i分', strtotime ( $pomo->created_at ) ) . "\n持续时长:20分钟\n";
		} else if ($request->has ( 'article_id' )) {
			$conditions ['article_id'] = $request->article_id;
			
			$article = Article::where ( 'id', $request->article_id )->first ();
			if (empty ( $article )) {
				abort ( 404, '系统异常，无此文章!' );
			}
			$recommend_add_content = "#记录文章#" . $article->subject . "\n时间：" . date ( 'm月d日 H时i分' ) . "\n";
		} else if ($request->has ( 'task_id' )) {
			$conditions ['task_id'] = $request->task_id;
			
			$task = Task::where ( 'id', $request->task_id )->where ( 'user_id', $request->user ()->id )->first ();
			if (empty ( $task )) {
				abort ( 404, '系统异常，无此待办!' );
			}
			$parentTaskName = isset ( $task->parentTask->name ) ? "#" . $task->parentTask->name . "#" : "";
			$modeName = $task->mode == 2 ? "#life#" : "#work#";
			$recommend_add_content = "#记录待办#" . $modeName . $parentTaskName . $task->name . "\n开始时间：" . date ( 'm月d日 H时i分', strtotime ( '-20 minute' ) ) . "\n持续时长:20分钟\n";
		}
		
		$notes = $this->notes->getAll ( $conditions );
		
		if ($request->has ( 'add_content' )) {
			if ($request->has ( 'type' ) && $request->type = 'image') {
				$add_image = $request->add_content;
				$img_info = getimagesize ( $add_image );
				if (empty ( $img_info ) && in_array ( $img_info ['mime'], array (
						'image/png',
						'image/gif',
						'image/jpeg' 
				) )) {
					echo '错误的图片类型';
					exit ();
				} else {
					$add_content = '#分享图片#';
				}
			} else {
				$add_content = $request->add_content;
				if (\App\Http\Utils\CommonUtil::isUrl ( $add_content )) {
					$title = \App\Http\Utils\CommonUtil::page_title ( $add_content );
					$shortUrl = \App\Http\Utils\CommonUtil::shortUrl ( $add_content );
					if (! empty ( $shortUrl )) {
						$add_content = $shortUrl;
					}
					$add_content = '#分享链接# ' . $add_content . ' ' . $title;
				}
				if (strpos ( $add_content, '#' ) === false) {
					$add_content = '#分享# ' . $add_content;
				}
			}
		} else if (! empty ( $recommend_add_content )) {
			$add_content = $recommend_add_content;
		}
		
		foreach ( $notes as $key => $note ) {
			$commonUtil = new \App\Http\Utils\CommonUtil ();
			$note->name = $commonUtil->auto_link_text ( $note->name );
			if (! empty ( $note->noteTagMaps )) {
				foreach ( $note->noteTagMaps as $noteTagMap ) {
					$url = "/notes?tag_id=" . $noteTagMap->tag->id;
					$tag_name = '#' . $noteTagMap->tag->name . '#';
					
					$note->name = str_replace ( $tag_name, "<a href='$url'  target='_blank'>" . $tag_name . "</a>", $note->name );
					$notes [$key] = $note;
				}
			}
		}
		
		return view ( 'notes.index', [ 
				'add_content' => $add_content,
				'add_image' => isset ( $add_image ) ? $add_image : '',
				'notes' => $notes,
				'pomo_id' => $request->has ( 'pomo_id' ) ? $request->pomo_id : '',
				'task_id' => $request->has ( 'task_id' ) ? $request->task_id : '',
				'article_id' => $request->has ( 'article_id' ) ? $request->article_id : '' 
		] );
	}
	
	/**
	 * 创建.
	 *
	 * @param Request $request        	
	 */
	public function store(Request $request) {
		$this->validate ( $request, [ 
				'name' => 'required' 
		] );
		
		if (empty ( $request->fname )) {
			$record_path = '';
		} else {
			$record_name = $request->user ()->id . $request->fname . '.mp3';
			$temp_path = config ( "app.storage_path" ) . 'recorders/temp/' . $record_name;
			$real_path = config ( "app.storage_path" ) . 'recorders/' . $record_name;
			
			if (! file_exists ( $temp_path )) {
				$record_path = '';
			} else {
				rename ( $temp_path, $real_path );
				$record_path = 'recorders/' . $record_name;
			}
		}
		
		if ($request->has ( 'add_image' )) {
			$add_image = $request->add_image;
			$img_info = getimagesize ( $add_image );
			if (empty ( $img_info ) && in_array ( $img_info ['mime'], array (
					'image/png',
					'image/gif',
					'image/jpeg' 
			) )) {
				echo '错误的图片类型';
				exit ();
			}
		} else {
			$add_image = '';
		}
		
		$name = htmlspecialchars ( $request->name );
		$name = str_replace ( '&lt;code&gt;', '<code>', $name );
		$name = str_replace ( '&lt;/code&gt;', '</code>', $name );
		$name = nl2br ( $name );
		$note = $request->user ()->notes ()->create ( [ 
				'name' => $name,
				'article_id' => $request->article_id,
				'task_id' => $request->task_id,
				'pomo_id' => $request->pomo_id,
				'record_path' => $record_path,
				'image_path' => $add_image,
				'status' => $request->status 
		] );
		
		preg_match_all ( '/#(.*?)#/i', $request->name, $match );
		foreach ( $match [0] as $item ) {
			$tag_name = trim ( $item, '#' );
			if (empty ( $tag_name )) {
				continue;
			}
			
			$tag = $this->tags->forTagName ( $tag_name );
			if (empty ( $tag )) {
				$tag = Tag::create ( array (
						'name' => $tag_name 
				) );
			}
			
			$tagNote = new NoteTagMap ();
			$tagNote->create ( array (
					'tag_id' => $tag->id,
					'note_id' => $note->id 
			) );
		}
		
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( self::OK_CODE );
			return response ( $resp );
		} else {
			return redirect ( '/notes' )->with ( 'message', '操作成功!' );
		}
	}
	
	/**
	 * 删除.
	 *
	 * @param Request $request        	
	 * @param Note $note        	
	 */
	public function destroy(Request $request, Note $note) {
		$this->authorize ( 'destroy', $note );
		
		$note->delete ();
		
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( self::OK_CODE );
			return response ( $resp );
		} else {
			return redirect ( '/notes' )->with ( 'message', '操作成功!' );
		}
	}
	
	/**
	 * 上传音频
	 *
	 * @param Request $request        	
	 * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Routing\ResponseFactory
	 */
	public function upload(Request $request) {
		if ($_FILES ["file"] ["type"] == 'audio/mp3') {
			$record_name = $request->user ()->id . $request->fname . '.mp3';
			move_uploaded_file ( $_FILES ["file"] ["tmp_name"], config ( "app.storage_path" ) . 'recorders/temp/' . $record_name );
		}
		
		if ($request->ajax () || $request->wantsJson ()) {
			$resp = $this->responseJson ( self::OK_CODE );
			return response ( $resp );
		} else {
			return redirect ( '/notes' )->with ( 'message', '操作成功!' );
		}
	}
	
	/**
	 * 获取音频
	 *
	 * @param Request $request        	
	 * @param Note $note        	
	 */
	public function getRecord(Request $request, Note $note) {
		if ($note->user_id == $request->user ()->id || $note->status == 2) {
			header ( 'Content-type: audio/mp3' );
			readfile ( config ( "app.storage_path" ) . $note->record_path );
		} else {
			echo 'error' . $request->user ()->user_id;
			exit ();
		}
	}
}
