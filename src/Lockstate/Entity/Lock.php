<?php

namespace Lockstate\Entity;


class Lock extends Entity
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $serial_number;

    /**
     * @var string
     */
    public $location_id;

    /**
     * @var int
     */
    public $programming_code;

    /**
     * @var int
     */
    public $heartbeat_interval;

    /**
     * @var int
     */
    public $wake_wifi;

    /**
     * @var int
     */
    public $muted;

    /**
     * @var int
     */
    public $autolock;

    /**
     * @var int
     */
    public $autolock_timeout;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSerialNumber()
    {
        return $this->serial_number;
    }

    /**
     * @param string $serial_number
     */
    public function setSerialNumber($serial_number)
    {
        $this->serial_number = $serial_number;
    }

    /**
     * @return int
     */
    public function getLocationId()
    {
        return $this->location_id;
    }

    /**
     * @param string $location_id
     */
    public function setLocationId($location_id)
    {
        $this->location_id = $location_id;
    }

    /**
     * @return int
     */
    public function getProgrammingCode()
    {
        return $this->programming_code;
    }

    /**
     * @param int $programming_code
     */
    public function setProgrammingCode($programming_code)
    {
        $this->programming_code = $programming_code;
    }

    /**
     * @return int
     */
    public function getHeartbeatInterval()
    {
        return $this->heartbeat_interval;
    }

    /**
     * @param int $heartbeat_interval
     */
    public function setHeartbeatInterval($heartbeat_interval)
    {
        $this->heartbeat_interval = $heartbeat_interval;
    }

    /**
     * @return int
     */
    public function getWakeWifi()
    {
        return $this->wake_wifi;
    }

    /**
     * @param int $wake_wifi
     */
    public function setWakeWifi($wake_wifi)
    {
        $this->wake_wifi = $wake_wifi;
    }

    /**
     * @return int
     */
    public function getMuted()
    {
        return $this->muted;
    }

    /**
     * @param int $muted
     */
    public function setMuted($muted)
    {
        $this->muted = $muted;
    }

    /**
     * @return int
     */
    public function getAutolock()
    {
        return $this->autolock;
    }

    /**
     * @param int $autolock
     */
    public function setAutolock($autolock)
    {
        $this->autolock = $autolock;
    }

    /**
     * @return int
     */
    public function getAutolockTimeout()
    {
        return $this->autolock_timeout;
    }

    /**
     * @param int $autolock_timeout
     */
    public function setAutolockTimeout($autolock_timeout)
    {
        $this->autolock_timeout = $autolock_timeout;
    }
}
