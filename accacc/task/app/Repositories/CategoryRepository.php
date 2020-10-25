<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Category;

class CategoryRepository {
	/**
	 * Get all of the tasks for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUser(User $user, $needPage = false) {
		$category = Category::where ( 'user_id', $user->id )->orderBy ( 'created_at', 'asc' );
		if ($needPage) {
			return $category->paginate ( 50 );
		} else {
			return $category->get ();
		}
	}
	
	/**
	 * Get all of the tasks for a given user.
	 *
	 * @param User $user        	
	 * @return Collection
	 */
	public function forUserByStatus(User $user, string $status) {
		return Category::where ( 'user_id', $user->id )->where ( 'status', $status )->get ();
	}
	
	/**
	 * Get goal for goal id.
	 *
	 * @param User $user        	
	 * @param int $goal_id        	
	 * @return Collection
	 */
	public function forCategoryId(User $user, $category_id) {
		return Category::where ( 'user_id', $user->id )->where ( 'id', $category_id )->first ();
	}
	
	/**
	 * 获取最后一条排序
	 *
	 * @param User $user        	
	 */
	public function forLastCategoryOrder(User $user) {
		$category = Category::where ( 'user_id', $user->id )->orderBy ( 'category_order', 'desc' )->first ();
		if (empty ( $category )) {
			return 0;
		} else {
			return $category->category_order;
		}
	}
	
	/**
	 * 根据categoryName获取分类
	 *
	 * @param User $user        	
	 * @param unknown $category_name        	
	 */
	public function forCategoryName(User $user, $category_name) {
		return Category::where ( 'user_id', $user->id )->where ( 'name', $category_name )->first ();
	}
}
