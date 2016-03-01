<?php

namespace Lockstate\Entity;


class Schedule extends Entity
{
    public $name;

    public $mon = [];
    public $tue = [];
    public $wed = [];
    public $thu = [];
    public $fri = [];
    public $sat = [];
    public $sun = [];

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function setInterval($day, $start, $end)
    {
        $this->$day = [(object) ['start_time' => $start, 'end_time' => $end]];
        return $this;
    }
}
