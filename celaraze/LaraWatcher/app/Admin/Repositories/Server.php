<?php

namespace App\Admin\Repositories;

use App\Models\Server as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Server extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
