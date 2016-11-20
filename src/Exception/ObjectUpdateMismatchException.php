<?php

namespace Exception;

class ObjectUpdateMismatchException extends \Exception
{
    public function __construct($entityFqcn, $id)
    {
        parent::__construct(sprintf(
            'An error occurred updating object \'%s\' with id \'%s\'.',
            $entityFqcn,
            $id
        ));
    }
}
