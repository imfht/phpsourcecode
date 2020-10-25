<?php
namespace Modules\Queue\Models;

use Phalcon\Mvc\Model;
use Phalcon\Paginator\Adapter\Model as Paginator;

class Queue extends Model
{
    public $id;
    public $type;
    public $data;
    public $runtime;
    public $weight;
    public $starttime;
    public $stoptime;
    public $cycle;
    public $changed;
    public $state;
}
