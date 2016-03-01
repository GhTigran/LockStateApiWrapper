<?php

namespace Lockstate\Entity;


class Entity
{
    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return array_filter(get_object_vars($this), function($val) {
            return !is_null($val);
        });
    }

    /**
     * @param array $data
     */
    public function exchangeArray($data)
    {
        $properties = get_object_vars($this);

        foreach ($properties as $property => $value) {
            $this->$property = (isset($data[$property]) ? $data[$property] : null);
        }
    }
}
