<?php
namespace Snscripts\MyCal;

class BaseObject
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
        // get the data
        if (array_key_exists($var, $this->data)) {
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
        $this->data[$var] = $value;
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
}
