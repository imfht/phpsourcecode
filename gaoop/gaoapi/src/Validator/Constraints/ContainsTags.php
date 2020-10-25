<?php


namespace App\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class ContainsTagTitleUnique
 * @package App\Validator\Constraints
 * @Annotation
 */
class ContainsTags extends Constraint
{
    public $message_name = '该值已经存在';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}