<?php
namespace timezone\entities;

/**
 * Class Timezone
 */
class Timezone
{
    private $id;
    private $name;
    private $offset;
    private $clocks = array();

    const TABLE_TIMEZONE = 'fuseau';
    const COL_ID         = 'id_fuseau';
    const COL_NAME       = 'nom_fuseau';
    const COL_OFFSET     = 'decalage_fuseau';

    /**
     * @param int $id
     * @param string $name
     * @param int $offset
     */
    public function __construct($id, $name, $offset)
    {
        $this->id       = $id;
        $this->name     = $name;
        $this->offset   = $offset;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return Clock[}
     */
    public function getClocks()
    {
        return $this->clocks;
    }

    /**
     * @param Clock[} $clocks
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

    /**
     * @return string
     */
    function toOption()
    {
        return "<option value=\"{$this->id}\">{$this->name} {$this->offset}</option>";
    }
}