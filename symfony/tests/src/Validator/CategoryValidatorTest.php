<?php


namespace App\tests\src\Validator;
use App\Validator\CategoryValidator;
use Exception;
use PHPUnit\Framework\TestCase;

class CategoryValidatorTest extends TestCase
{
    public function testFieldsValidationNameNull()
    {
        $validator = new CategoryValidator();
        try {
        $result = $validator->fieldsValidation([]);
        } catch (Exception $e) {
            $this->assertEquals('JSON не содержит поле name', $e->getMessage());
        }

    }
    public function testFieldsValidationCountNull()
    {
        $validator = new CategoryValidator();
        try {
            $result = $validator->fieldsValidation(['name' => 'test']);
        } catch (Exception $e) {
            $this->assertEquals('JSON не содержит поле productCount', $e->getMessage());
        }

    }
    public function testFieldsValidationCategoryNull()
    {
        $validator = new CategoryValidator();
        try {
            $result = $validator->fieldsValidation(['name' => 'test', 'productCount' => 0]);
        } catch (Exception $e) {
            $this->assertEquals('JSON не содержит поле category', $e->getMessage());
        }

    }
    public function testFieldsValidationNamePatternCombinationAlphabet()
    {
        $validator = new CategoryValidator();
        try {
            $result = $validator->fieldsValidation(['name' => 'фоlse']);
        } catch (Exception $e) {
            $this->assertEquals('Поле name не соответствует формату: допустимы только буквы, недопустимо сочетание разных алфавитов', $e->getMessage());
        }

    }
    public function testFieldsValidationNamePatternNumbers()
    {
        $validator = new CategoryValidator();
        try {
            $result = $validator->fieldsValidation(['name' => '233']);
        } catch (Exception $e) {
            $this->assertEquals('Поле name не соответствует формату: допустимы только буквы, недопустимо сочетание разных алфавитов', $e->getMessage());
        }

    }
    public function testFieldsValidationNamePatternCombinationAlpNumb()
    {
        $validator = new CategoryValidator();
        try {
            $result = $validator->fieldsValidation(['name' => '233ff']);
        } catch (Exception $e) {
            $this->assertEquals('Поле name не соответствует формату: допустимы только буквы, недопустимо сочетание разных алфавитов', $e->getMessage());
        }

    }
    public function testFieldsValidationNamePatternAnotherSymbols()
    {
        $validator = new CategoryValidator();
        try {
            $result = $validator->fieldsValidation(['name' => '#@ $ #$&(*']);
        } catch (Exception $e) {
            $this->assertEquals('Поле name не соответствует формату: допустимы только буквы, недопустимо сочетание разных алфавитов', $e->getMessage());
        }

    }
    public function testFieldsValidationCountNuN()
    {
        $validator = new CategoryValidator();
        try {
            $result = $validator->fieldsValidation(['name' => 'test', 'productCount' => 'test']);
        } catch (Exception $e) {
            $this->assertEquals('Поле productCount не содержит интерпретируемое целое число', $e->getMessage());
        }

    }
    public function testFieldsValidationCountMinusNumber()
    {
        $validator = new CategoryValidator();
        try {
            $result = $validator->fieldsValidation(['name' => 'test', 'productCount' => -4]);
        } catch (Exception $e) {
            $this->assertEquals('Поле productCount не содержит нуль или явно положительное число', $e->getMessage());
        }

    }
   public function testFieldsValidationCategoryNuN()
    {
        $validator = new CategoryValidator();
        try {
            $result = $validator->fieldsValidation(['name' => 'test', 'productCount' => 0, 'category' => 'test']);
        } catch (Exception $e) {
            $this->assertEquals('Поле category не содержит интерпретируемое целое число', $e->getMessage());
        }

    }
    public function testFieldsValidationCategoryMinusNumber()
    {
        $validator = new CategoryValidator();
        try {
            $result = $validator->fieldsValidation(['name' => 'test', 'productCount' => 0, 'category' => -4]);
        } catch (Exception $e) {
            $this->assertEquals('Поле category не содержит нуль или явно положительное число', $e->getMessage());
        }

    }

   /*
    function testIdValidation()
    {
        $validator = new CategoryValidator();
        $result = $validator->idValidation('ff4');
        // убедитесь, что ваш калькулятор добавил цифры правильно!
        $this->assertEquals('ID не является интерпретируемым числом', $result->getMessage());
    }
    */

}