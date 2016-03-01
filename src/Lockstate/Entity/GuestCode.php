<?php

namespace Lockstate\Entity;


class GuestCode extends Entity
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
    public $starts_at;

    /**
     * @var string
     */
    public $ends_at;

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
    public function getStartsAt()
    {
        return $this->starts_at;
    }

    /**
     * @param string $starts_at
     */
    public function setStartsAt($starts_at)
    {
        $this->starts_at = $starts_at;
    }

    /**
     * @return string
     */
    public function getEndsAt()
    {
        return $this->ends_at;
    }

    /**
     * @param string $ends_at
     */
    public function setEndsAt($ends_at)
    {
        $this->ends_at = $ends_at;
    }
}
