<?php

namespace bTokman\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\{
    LogicException, UnexpectedTypeException
};

class ValidationTraitTest extends TestCase
{
    /** @var ValidationObject */
    protected $objectToValidation;

    public function setUp()
    {
        $this->objectToValidation = new ValidationObject();
        parent::setUp();
    }

    public function testFailureValidationKeys(): void
    {
        $result = $this->objectToValidation->validate();

        foreach ($this->objectToValidation->validationRules as $field => $rules) {
            $this->assertArrayHasKey($field, $result);
        }
    }

    public function testFailureValidationMessageCount(): void
    {
        $result = $this->objectToValidation->validate();

        foreach ($this->objectToValidation->validationRules as $field => $rules) {
            $this->assertCount(\count($result[$field]), $rules);
        }
    }

    public function testSuccessValidation(): void
    {
        $this->objectToValidation->password = 'password';
        $this->objectToValidation->email = 'test@test.com';

        $result = $this->objectToValidation->validate();

        $this->assertEquals(null, $result);
    }

    public function testUnexpectedTypeException(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->objectToValidation->validationRules['password'] = 'Wrong format';
        $this->objectToValidation->validate();
    }

    public function testLogicException(): void
    {
        $this->expectException(LogicException::class);
        $this->objectToValidation->validationRules['password'] = [\stdClass::class];
        $this->objectToValidation->validate();
    }
}