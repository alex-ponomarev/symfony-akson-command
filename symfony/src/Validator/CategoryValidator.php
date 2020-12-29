<?php

namespace App\Validator;

use Exception;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CategoryValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\Category */

        if (null === $value || '' === $value) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
    function fieldsValidation($fields): bool
    {
        if (!(array_key_exists('name', $fields))) {
            throw new Exception('JSON не содержит поле name');
        } else if (!(preg_match('/^([А-ЯЁ\s][а-яё\s]+|[A-Z\s][a-z\s]+)$/iu', $fields['name']))) {
            throw new Exception('Поле name не соответствует формату: допустимы только буквы, недопустимо сочетание разных алфавитов');
        }

        if (!(array_key_exists('productCount', $fields))) {
            throw new Exception('JSON не содержит поле productCount');
        } else if (!(is_int($fields['productCount']))) {
            throw new Exception('Поле productCount не содержит интерпретируемое целое число');
        } else if (!($fields['productCount'] >= 0)) {
            throw new Exception('Поле productCount не содержит нуль или явно положительное число');
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
        if(!(is_numeric($id))) {
            throw new Exception('ID не является интерпретируемым числом');
        }else if(!(is_int((int)$id))){
            throw new Exception('ID не является целым числом');
        }else if(!($id>=0)){
            throw new Exception ('ID не является нулем или явно положительным числом');
        }
        return true;
    }
}
