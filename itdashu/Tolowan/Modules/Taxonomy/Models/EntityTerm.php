<?php
namespace Modules\Taxonomy\Models;

use Phalcon\Mvc\Model;

class EntityTerm extends Model
{
    public function getSource()
    {
        return 'entity_term';
    }
}
