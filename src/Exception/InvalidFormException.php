<?php

namespace Exception;

class InvalidFormException extends \Exception
{
    /**
     * @var array
     */
    private $errors;

    public function __construct(array $errors)
    {
        parent::__construct('');

        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
