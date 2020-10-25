<?php

namespace App\Admin\Repositories;

use App\Models\HardwareTrack as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class HardwareTrack extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
