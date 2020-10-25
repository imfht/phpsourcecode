<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * @var array
     */
    protected $fillable = ["tag"];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bijis(){
        return $this->belongsToMany('App\Biji');
    }

    /**
     * @param array $tags
     */
    public static function addNeededTags(array $tags){
        if(count($tags) == 0){
            return;
        }
        $found = static::whereIn('tag',$tags)->lists('tag')->all();
        foreach (array_diff($tags, $found) as $tag) {
            static::create([
                'tag' => $tag,
            ]);
        }
    }
}
