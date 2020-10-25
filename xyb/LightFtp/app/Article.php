<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model {

	public function author()
    {
        return $this->hasOne('App\Author');
    }

}
