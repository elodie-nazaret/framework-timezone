<?php
namespace timezone\entities;

class Country {
    private $id;
    private $name;
    private $clocks;

    /**
     * @param int $id
     * @param string $name
     */
    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
        $this->clocks = array();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

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
     * @return array
     */
    public function getClocks()
    {
        return $this->clocks;
    }

    /**
     * @param array $clocks
     */
    public function setClocks($clocks)
    {
        $this->clocks = $clocks;
    }

    /**
     * @param Clock $clock
     */
    public function addClock(Clock $clock)
    {
        $this->clocks[] = $clock;
    }

    /**
     * @param Clock $clock
     */
    public function removeClock(Clock $clock) {
        $id = array_search($clock, $this->clocks);
        if ($id !== false) {
            array_splice($this->clocks, $id, 1);
        }
    }
}