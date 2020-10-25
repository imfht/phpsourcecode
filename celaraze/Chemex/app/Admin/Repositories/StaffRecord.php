<?php

namespace App\Admin\Repositories;

use App\Models\StaffRecord as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class StaffRecord extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
