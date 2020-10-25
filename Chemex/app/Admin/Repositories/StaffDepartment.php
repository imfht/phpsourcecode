<?php

namespace App\Admin\Repositories;

use App\Models\StaffDepartment as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class StaffDepartment extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
