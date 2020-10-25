<?php

namespace App\Admin\Repositories;

use App\Models\SoftwareRecord as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class SoftwareRecord extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
