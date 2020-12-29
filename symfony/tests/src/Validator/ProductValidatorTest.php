<?php


namespace App\tests\src\Validator;
use App\Validator\ProductValidator;
use Exception;
use PHPUnit\Framework\TestCase;

class ProductValidatorTest extends TestCase
{


    public function testFieldsValidationPriceNuN()
    {
        $validator = new ProductValidator();
        try {
            $result = $validator->fieldsValidation(['price' => 'FFFF']);
        } catch (Exception $e) {
            $this->assertEquals('Поле price не содержит интерпретируемое число', $e->getMessage());
        }

    }
    public function testFieldsValidationPriceMinusNumber()
    {
        $validator = new ProductValidator();
        try {
            $result = $validator->fieldsValidation(['price' => '-3']);
        } catch (Exception $e) {
            $this->assertEquals('Поле price не содержит нуль или явно положительное число', $e->getMessage());
        }

    }
   public function testFieldsValidationCategoryNaN()
    {
        $validator = new ProductValidator();
        try {
            $result = $validator->fieldsValidation(['category' => 'test']);
        } catch (Exception $e) {
            $this->assertEquals('Поле category не содержит интерпретируемое целое число', $e->getMessage());
        }

    }
    public function testFieldsValidationCategoryMinusNumber()
    {
        $validator = new ProductValidator();
        try {
            $result = $validator->fieldsValidation(['category' => -4]);
        } catch (Exception $e) {
            $this->assertEquals('Поле category не содержит нуль или явно положительное число', $e->getMessage());
        }

    }
    public function testFieldsValidationSkuNoFormat()
    {
        $validator = new ProductValidator();
        try {
            $result = $validator->fieldsValidation(['sku' => 'FFFF']);
        } catch (Exception $e) {
            $this->assertEquals('Поле sku не соответствует формату.', $e->getMessage());
        }

    }
    public function testIdValidationNaN()
    {
        $validator = new ProductValidator();
        try {
            $result = $validator->idValidation('FFFF');
        } catch (Exception $e) {
            $this->assertEquals('ID не является интерпретируемым числом', $e->getMessage());
        }

    }
    public function testIdValidationNotAnInt()
    {
        $validator = new ProductValidator();
        try {
            $result = $validator->idValidation(4.3);
        } catch (Exception $e) {
            $this->assertEquals('ID не является целым числом', $e->getMessage());
        }

    }
    public function testIdValidationNotAnPlus()
    {
        $validator = new ProductValidator();
        try {
            $result = $validator->idValidation(-3);
        } catch (Exception $e) {
            $this->assertEquals('ID не является нулем или явно положительным числом', $e->getMessage());
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