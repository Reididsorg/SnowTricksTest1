<?php


namespace App\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsToto extends Constraint
{
    public $message = 'Le nom de fichier de l\'image principale contient le mot \'Toto\'';
}