<?php

namespace App\Services;

use App\Models\User;
use App\Models\Note;

/**
 * 笔记业务逻辑
 *
 * @author edison.an
 *        
 */
class NoteService {
	/**
	 * Get all of the notes for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUser(User $user) {
		return Note::where ( 'user_id', $user->id )->orderBy ( 'created_at', 'desc' )->get ();
	}
	
	/**
	 * Get all of the notes for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forStatus($status) {
		return Note::where ( 'status', $status )->orderBy ( 'created_at', 'desc' )->get ();
	}
	
	/**
	 * Get all of the notes for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUserByStatus(User $user, $status, $needPage = false) {
		$note = Note::with ( [ 
				'noteTagMaps.tag',
				'user' 
		] )->where ( 'status', $status )->orWhere ( 'user_id', $user->id )->orderBy ( 'created_at', 'desc' );
		if ($needPage) {
			return $note->paginate ( 50 );
		} else {
			return $note->get ();
		}
	}
	
	public function getAll($conditions, $pages = array('need_page' => true, 'page_count' => 20)) {
		$query = Note::with ( [
				'noteTagMaps.tag',
				'user'
		] );
		$query->where ( function ($query) use ($conditions) {
			$query->where ( 'status', 2 )->orwhere ( 'user_id', $conditions ['user_id'] );
		} );
			if (isset ( $conditions ['keyword'] )) {
				$query->where ( 'name', 'like', "%" . $conditions ['keyword'] . "%" );
			}
			if (isset ( $conditions ['article_id'] )) {
				$query->where ( 'article_id', $conditions ['article_id'] );
			}
			if (isset ( $conditions ['pomo_id'] )) {
				$query->where ( 'pomo_id', $conditions ['pomo_id'] );
			}
			if (isset ( $conditions ['task_id'] )) {
				$query->where ( 'task_id', $conditions ['task_id'] );
			}
			if (isset ( $conditions ['tag_id'] )) {
				$notes = DB::table ( 'note_tag_maps' )->select ( array (
						'note_tag_maps.note_id'
				) )->where ( 'tag_id', $conditions ['tag_id'] )->get ();
				$noteids = array ();
				foreach ( $notes as $note ) {
					$noteids [] = $note->note_id;
				}
				$query->whereIn ( 'id', $noteids );
			}
			$query->orderBy ( 'created_at', 'desc' );
			if ($pages ['need_page']) {
				return $query->paginate ( $pages ['page_count'] );
			} else {
				return $query->get ();
			}
	}
}
