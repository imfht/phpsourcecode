<?php

namespace App\Models;

use App\Models\Task;
use App\Models\Pomo;
use App\Models\Feed;
use App\Models\Category;
use App\Models\Article;
use App\Models\Note;
use App\Models\Third;
use App\Models\Goal;
use App\Models\Setting;
use App\Models\Thing;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 
			'name',
			'email',
			'password' 
	];
	
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [ 
			'password',
			'remember_token' 
	];
	
	/**
	 * Get all of the tasks for the user.
	 */
	public function tasks() {
		return $this->hasMany ( Task::class );
	}
	
	/**
	 * Get all of the tasks for the user.
	 */
	public function pomos() {
		return $this->hasMany ( Pomo::class );
	}
	
	/**
	 * Get all of the tasks for the user.
	 */
	public function notes() {
		return $this->hasMany ( Note::class );
	}
	
	/**
	 * Get all of the thirds for the user.
	 */
	public function thirds() {
		return $this->hasMany ( Third::class );
	}
	public function feeds() {
		return $this->hasMany ( Feed::class );
	}
	public function categorys() {
		return $this->hasMany ( Category::class );
	}
	public function articles() {
		return $this->hasMany ( Article::class );
	}
	
	/**
	 * Get all of the tags for the user.
	 */
	public function goals() {
		return $this->hasMany ( Goal::class );
	}
	public function minds() {
		return $this->hasMany ( Mind::class );
	}
	public function things() {
		return $this->hasMany ( Thing::class );
	}
	public function setting() {
		return $this->hasOne ( Setting::class );
	}
	public function feedSubs() {
		return $this->hasMany ( FeedSub::class );
	}
	public function articleSubs() {
		return $this->hasMany ( ArticleSub::class );
	}
}
