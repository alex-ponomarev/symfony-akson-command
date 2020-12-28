<?php

namespace App\tests\src\DataCrypt;
use App\DataCrypt\Encoder;
use Exception;
use PHPUnit\Framework\TestCase;

class EncoderTest extends TestCase
{
    public function testToJSONNull()
    {
        $validator = new Encoder();
        $result = $validator->toJSON(NULL);
        $this->assertEquals('null', $result);
    }
    public function testToJSONArray()
    {
       $validator = new Encoder();
       $result = $validator->toJSON(['testField0'=>'test','testField1'=> 0]);
       $this->assertEquals('{"testField0":"test","testField1":0}', $result);
    }
    public function testToJSONInteger()
    {
       $validator = new Encoder();
       $result = $validator->toJSON(34412);
       $this->assertEquals('34412', $result);
    }
    public function testToJSONReal()
    {
       $validator = new Encoder();
       $result = $validator->toJSON(344.12);
       $this->assertEquals('344.12', $result);
    }
    public function testToJSONString()
    {
       $validator = new Encoder();
       $result = $validator->toJSON('akson');
       $this->assertEquals('"akson"', $result);
    }

}