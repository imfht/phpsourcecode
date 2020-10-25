<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Article extends Model
{
  use SoftDeletes;

  protected $hidden = ['deleted_at', 'created_at'];

  protected $fillable = ['user_id', 'notepad', 'is_private', 'key', 'tag', 'code', 'hash'];

  public function setTagAttribute($value)
  {
    if (!is_array($value)) {
      $data = [];
      $data[] = $value;
    }
    $data = self::arrayRecursive($value, 'urlencode', true);
    $json = json_encode($data);
    $this->attributes['tag'] = intval($json);
  }

  public function scopeSortByDesc($query, $key)
  {
    if ($key != 'id') return $query->orderBy($key, 'desc')->orderBy('id', 'desc');
    return $query->orderBy($key, 'desc');
  }

  public function scopeSortBy($query, $key)
  {
    return $query->orderBy($key);
  }

  public function maketime()
  {
    $date = $this->created_at;
    $offDays = Carbon::now('Asia/Shanghai')->diffInDays(Carbon::parse($date));
    if ($offDays > 7) {
      return Carbon::parse($date)->toDateString();
    }

    return Carbon::parse($date)->diffForHumans();
  }

  public function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
  {
    static $recursive_counter = 0;
    if (++$recursive_counter > 1000) {
      die('possible deep recursion attack');
    }
    foreach ($array as $key => $value) {
      if (is_array($value)) {
        $this->arrayRecursive($array[$key], $function, $apply_to_keys_also);
      } else {
        $array[$key] = $function($value);
      }

      if ($apply_to_keys_also && is_string($key)) {
        $new_key = $function($key);
        if ($new_key != $key) {
          $array[$new_key] = $array[$key];
          unset($array[$key]);
        }
      }
    }
    $recursive_counter--;
  }
}
