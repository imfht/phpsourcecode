<?php
namespace Modules\File\Models;

use Phalcon\Mvc\Model;

class FileAuthorSize extends Model
{
    public $id;
    public $author;
    public $type;
    public $size;
}