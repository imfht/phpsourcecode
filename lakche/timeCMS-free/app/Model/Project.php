<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Model\Person;

class Project extends Model
{
  use SoftDeletes;

  protected $hidden = ['deleted_at', 'created_at'];

  protected $fillable = ['title', 'category_id', 'sort', 'views', 'tag', 'is_recommend', 'is_show', 'cover', 'thumb', 'cost', 'period', 'person_id', 'info', 'url', 'keywords', 'description', 'text', 'speed', 'hash'];

  public function setIsRecommendAttribute($value)
  {
    $this->attributes['is_recommend'] = intval($value);
  }

  public function setIsShowAttribute($value)
  {
    $this->attributes['is_show'] = intval($value);
  }

  public function setTagAttribute($value)
  {
    $this->attributes['tag'] = json_encode(explode(',', strip_tags($value)));
  }

  public function setPersonIdAttribute($value)
  {
    $this->attributes['person_id'] = json_encode(explode(',', strip_tags($value)));
  }

  public function setDescriptionAttribute($value)
  {
    $this->attributes['description'] = $value ? $value : '';
  }

  public function setTextAttribute($value)
  {
    $this->attributes['text'] = $value ? $value : '';
  }

  public function scopeSortByDesc($query,$key)
  {
    if($key != 'id') return $query->orderBy($key,'desc')->orderBy('id','desc');
    return $query->orderBy($key,'desc');
  }

  public function scopeSortBy($query,$key)
  {
    return $query->orderBy($key);
  }

  public function category()
  {
    return $this->belongsTo('App\Model\Category');
  }

  public function getCover()
  {
    if($this->thumb != ''){
      return $this->thumb;
    } else {
      $this->cover;
    }
  }

  public function getPersonName()
  {
    $person_id = $this->person_id;
    $person_name = [];
    foreach(json_decode($person_id) as $id){
      $person = Person::where('id',$id)->first();
      if($person){
        $person_name[] = $person->name;
      }
    }
    return implode(',',$person_name);
  }

  public function persons()
  {
    $person_id = $this->person_id;

    $person_id = json_decode($person_id);
    $persons = Person::wherein('id',$person_id)->get();

    return $persons;
  }


}
