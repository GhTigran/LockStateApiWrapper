<?php

namespace Lockstate\Entity;


class UserCode extends Entity
{
    /**
     * @var string
     */
    public $first_name;

    /**
     * @var string
     */
    public $last_name;

    /**
     * @var int
     */
    public $value;

    /**
     * @var string
     */
    public $access_schedule_id;

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param string $first_name
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param string $last_name
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getAccessScheduleId()
    {
        return $this->access_schedule_id;
    }

    /**
     * @param string $access_schedule_id
     */
    public function setAccessScheduleId($access_schedule_id)
    {
        $this->access_schedule_id = $access_schedule_id;
    }
}
