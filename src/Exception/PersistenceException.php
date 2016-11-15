<?php

namespace Exception;

class PersistenceException extends \Exception
{
    public function __construct()
    {
        parent::__construct('An error occurred during persistence.');
    }
}
