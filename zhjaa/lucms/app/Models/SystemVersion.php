<?php

namespace App\Models;

use App\Handlers\MarkdownerHandler;

class SystemVersion extends Model
{

    protected $fillable = [
        'version', 'title', 'content', 'download_url'
    ];

    protected function setContentAttribute($value)
    {
        $data = [
            'raw' => $value,
            'html' => (new MarkdownerHandler())->convertMarkdownToHtml($value)
        ];
        $this->attributes['content'] = json_encode($data);
    }

    protected function getContentAttribute($value)
    {
        return json_decode($value,true);
    }
}
