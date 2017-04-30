<?php

namespace Snscripts\MyCal\Traits;

trait Accessible
{
    protected $data = [];

    /**
     * Magic Getter method for retrieving object data
     *
     * @param string $var Variable to get
     * @return mixed|null
     */
    public function __get($var)
    {
        $varMethod = $this->transformVar($var);
        $method = 'get' . $varMethod . 'Attr';

        if (method_exists($this, $method)) {
            return $this->$method();
        } elseif (array_key_exists($var, $this->data)) {
            return $this->data[$var];
        }

        return null;
    }

    /**
     * Magic Setter method for setting data
     *
     * @param string $var Variable to set
     * @param mixed $value The value to set
     * @return void
     */
    public function __set($var, $value)
    {
        $varMethod = $this->transformVar($var);
        $method = 'set' . $varMethod . 'Attr';

        if (method_exists($this, $method)) {
            $this->$method($value);
        } else {
            $this->data[$var] = $value;
        }
    }

    /**
     * Magic methods for check items isset / empty
     *
     * @param string $var Variable to set
     * @return bool
     */
    public function __isset($var)
    {
        return isset($this->data[$var]);
    }

    /**
     * Magic method for treating object as a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * convert the object to an array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * convert the object to a json string
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode(
            $this->toArray()
        );
    }

    /**
     * set array of data
     *
     * @param array $data
     * @return object $this
     */
    public function setAllData($data)
    {
        array_walk(
            $data,
            function ($value, $key) {
                $this->{$key} = $value;
            }
        );

        return $this;
    }

    /**
     * transform var name into method name
     *
     * @param string $var
     * @return string
     */
    public function transformVar($var)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($var))));
    }
}
