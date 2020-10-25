<?php


namespace App\Validator\Constraints;


use App\Library\Helper\GeneralHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContainsTagsValidator extends ConstraintValidator
{
    public function validate($protocol, Constraint $constraint)
    {
        if (!$constraint instanceof ContainsTags) {
            throw new UnexpectedTypeException($constraint, ContainsTags::class);
        }

        // 验证当前tag在当前info中是否已经存在
        if (GeneralHelper::getOneInstance()->hasTag($protocol->getId(), $protocol->getName())) {
            $this->context->buildViolation($constraint->message_name)
                ->atPath('name')
                ->addViolation();
        }
    }
}