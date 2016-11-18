<?php

namespace Exception\Serializer\Construction;

class ObjectNotConstructedException extends \Exception
{
    /**
     * @var mixed
     */
    private $data;

    /**
     * @param string $name
     * @param mixed $data
     */
    public function __construct($name, $data)
    {
        parent::__construct(
            sprintf('Could not construct object of type \'%s\' from data.', $name)
        );

        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
