<?php

namespace bTokman\Validation;

use Symfony\Component\Validator\{
    Constraint,
    ConstraintViolation,
    ConstraintViolationListInterface,
    Exception\LogicException,
    Exception\UnexpectedTypeException
};

/**
 * Trait ValidationTrait
 * @package bTokman\Validation
 */
trait ValidationTrait
{
    /**
     * List of rules (Symfony validation)
     *
     * [
     *  'propertyName' => [NotBlank::class],
     *  'propertyName' => [Assert\Length::class, ["min" => 3]],
     *  'propertyName' => [NotBlank::class],
     * ]
     *
     * @var array
     */
    public $validationRules = [];

    /**
     * List of errors
     * [
     *   'propertyName' => 'Error message'
     * ]
     *
     * @var array
     */
    public $validationErrors = [];

    /**
     * Validate any model by Symfony validation
     *
     * @link https://symfony.com/doc/current/components/validator.html
     *
     * @return array|null
     * @throws \ReflectionException | UnexpectedTypeException
     */
    public function validate(): ?array
    {
        foreach ($this->validationRules as $field => $rules) {
            if (!is_array($rules)) {
                throw new UnexpectedTypeException($field, 'array');
            }

            $this->processRules($rules, $field);
        }

        return count($this->validationErrors) ? $this->validationErrors : null;
    }

    /**
     * Process list of rules and validate class properties
     *
     * @param array $rules
     * @param string $field
     * @throws \ReflectionException
     */
    private function processRules(array $rules, string $field)
    {
        /** Create new Symfony validator instance */
        $validator = $this->getValidator();

        /** Build an array of Symfony validation constraints */
        $validators = $this->buildValidation($rules);

        /** Get property value  */
        $property = $this->getProperty($field);

        /** Validate property value
         * @link https://symfony.com/doc/master/validation/raw_values.html
         */
        $errors = $validator->validate($property, $validators);

        if (count($errors)) {
            $this->processErrors($errors, $field);
        }
    }

    /**
     * Get validator instance
     *
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected function getValidator()
    {
        return \Symfony\Component\Validator\Validation::createValidator();
    }

    /**
     * Build array of validation constraint from rules
     *
     * @param $rules
     * @return array
     */
    private function buildValidation($rules): array
    {
        $validators = [];

        foreach ($rules as $rule) {
            if (is_array($rule)) {
                $class = array_shift($rule);
                $object = new $class(...$rule);
            } else {
                $object = new $rule();
            }

            if (!$object instanceof Constraint) {
                throw new LogicException('Rule not allowed - ' . $rules[0]);
            }

            $validators[] = $object;
        }

        return $validators;
    }

    /**
     * Get child class property
     *
     * @param string $name
     * @return mixed
     * @throws \ReflectionException
     */
    private function getProperty(string $name)
    {
        $property = new \ReflectionProperty(static::class, $name);
        $property->setAccessible(true);

        return $property->getValue($this) ?? false;
    }

    /**
     * Generate output format.Example : [
     *  'name' => [
     *              "This value should not be blank.",
     *              "This value is too short. It should have 2 characters or more."
     *            ]
     * ]
     *
     * @param ConstraintViolationListInterface $errors
     * @param $field
     */
    private function processErrors(ConstraintViolationListInterface $errors, $field): void
    {
        foreach ($errors as $error) {
            /** @var ConstraintViolation $error */
            $this->validationErrors[$field][] = $error->getMessage();
        }
    }
}