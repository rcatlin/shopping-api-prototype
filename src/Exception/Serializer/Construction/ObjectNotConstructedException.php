<?php

namespace Exception\Serializer\Construction;

class ObjectNotConstructedException extends \Exception
{
    /**
     * @var mixed
     */
    private $data;

    /**
     * @var array
     */
    private $path;

    /**
     * @param string $name
     * @param mixed $data
     * @param array $path
     */
    public function __construct($name, $data, array $path)
    {
        parent::__construct(
            sprintf('Could not construct object of type \'%s\' from data.', $name)
        );

        $this->data = $data;
        $this->path = $path;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getPath()
    {
        return $this->path;
    }
}
