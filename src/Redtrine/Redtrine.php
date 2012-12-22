<?php

namespace Redtrine;

class Redtrine
{
    protected $client;

    public function __construct($client = null)
    {
        if (null !== $client) {
            $this->client = $client;
        }
    }

    public function setClient($client)
    {
        $this->client = $client;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function create($structure, $name)
    {
        if ('List' == $structure) {
            $structure = 'Rlist';
        }

        $class = 'Redtrine\\Structure\\' . $structure;

        if (is_array($name)) {
            $name = implode(':', $name);
        }

        if (class_exists($class)) {
            $obj = new $class($name);
            $obj->setClient($this->client);

            return $obj;

        } else {
            throw new \InvalidArgumentException(sprintf('Impossible to create structure %s, class %s not found.', $structure, $class));
        }
    }
}
