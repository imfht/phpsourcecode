<?php

namespace App\Admin\Repositories;

use App\Models\HardwareRecord as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class HardwareRecord extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
