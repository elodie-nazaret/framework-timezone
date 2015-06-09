<?php
namespace timezone\entities;

class Timezone {
    private $id;
    private $name;
    private $offset;
    private $clocks;

    /**
     * @param int $id
     * @param string $name
     * @param string $offset
     */
    function __construct($id, $name, $offset)
    {
        $this->id = $id;
        $this->name = $name;
        $this->offset = $offset;
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
     * @return string
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param string $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
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