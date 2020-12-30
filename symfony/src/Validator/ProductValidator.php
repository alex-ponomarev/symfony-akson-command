<?php

namespace App\Validator;

use Exception;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ProductValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\Product */

        if (null === $value || '' === $value) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }

    function fieldsValidation($fields): bool
    {
        if ((array_key_exists('price', $fields))) {
            if (!(is_numeric($fields['price']))) {
                throw new Exception('Поле price не содержит интерпретируемое число');
            } else if (!($fields['price'] >= 0)) {
                throw new Exception('Поле price не содержит нуль или явно положительное число');
            }
        }

        if ((array_key_exists('category', $fields))) {
            if (!(is_int($fields['category']))) {
                throw new Exception('Поле category не содержит интерпретируемое целое число');
            } else if (!($fields['category'] >= 0)) {
                throw new Exception('Поле category не содержит нуль или явно положительное число');
            }
        }

        return true;
    }

    function idValidation($id): bool
    {
        if (!(is_numeric($id))) {
            throw new Exception('ID не является интерпретируемым числом');
        } else if (($this->isDecimal($id))) {
            throw new Exception('ID не является целым числом');
        } else if (!($id >= 0)) {
            throw new Exception ('ID не является нулем или явно положительным числом');
        }
        return true;
    }

    function isDecimal($id)
    {
        $n = abs($id);
        $whole = floor($n);
        $fraction = $n - $whole;
        return $fraction > 0;
    }
}
