<?php

namespace Exception;

use Symfony\Component\Validator\ConstraintViolationInterface;

class ValidationException extends \Exception
{
    /**
     * @var ConstraintViolationInterface[]
     */
    private $errors;

    public function __construct(array $errors)
    {
        parent::__construct('Validation failed.');

        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
