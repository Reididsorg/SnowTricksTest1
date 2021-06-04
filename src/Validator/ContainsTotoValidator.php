<?php


namespace App\Validator;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContainsTotoValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
//        if(preg_match('/(toto)/', $value, $matches)){
//           $this->context->buildViolation($constraint->message)
//               ->atPath('mainImage')
//               ->addViolation();
//        }
    }
}