<?php

namespace AppBundle;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait ValidatesEntity
{
    public function validateEntity(ValidatorInterface $validator, $object)
    {
        $violations = $validator->validate($object);
        $errors = [];

        /** @var ConstraintViolationInterface $violation */
        foreach ($violations as $violation) {
            $errors[] = [
                'invalid_value' => $violation->getInvalidValue(),
                'message' => $violation->getMessage(),
                'property_path' => $violation->getPropertyPath(),
            ];
        }

        return $errors;
    }
}
