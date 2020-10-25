<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\CategoryRepository;

/**
 * 文章分类相关Service
 *
 * @author edison.an
 *        
 */
class CategoryService {
	
	/**
	 *
	 * @var CategoryRepository
	 */
	protected $categories;
	
	/**
	 *
	 * @param CategoryRepository $categories        	
	 */
	public function __construct(CategoryRepository $categories) {
		$this->categories = $categories;
	}
	
	/**
	 *
	 * @param User $user        	
	 * @param boolean $needPage        	
	 * @return
	 *
	 */
	public function getList(User $user, $needPage = true, $needAutoCreate = false) {
		$categories = $this->categories->forUser ( $user, $needPage );
		if ($needAutoCreate && count ( $categories ) == 0) {
			$category = $user->categorys ()->create ( [ 
					'name' => '未分类',
					'category_order' => 0 
			] );
			$categories = array (
					$category 
			);
		}
		return $categories;
	}
	
	/**
	 *
	 * @param User $user        	
	 * @param boolean $needPage        	
	 * @return
	 *
	 */
	public function getByCategoryId(User $user, $categoryId) {
		$category = $this->categories->forCategoryId ( $user, $categoryId );
		return $category;
	}
	
	/**
	 *
	 * @param User $user        	
	 * @param array $categoryIds        	
	 * @return boolean
	 */
	public function setCategorySort($user, $categoryIds) {
		$sort = 0;
		foreach ( $categoryIds as $categoryId ) {
			$category = $this->categories->forCategoryId ( $user, $categoryId );
			if (! empty ( $category )) {
				$category->update ( array (
						'category_order' => $sort ++ 
				) );
			}
		}
		return true;
	}
}
