<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model {

	protected $fillable = ['title','body','user_id','state','bodyhtml','summaryhtml'];

}
