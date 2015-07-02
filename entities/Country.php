<?php
namespace Timezone\Entities;

use Framework\Entities\Entity;

/**
 * Class Country
 */
class Country extends Entity
{
    private $id;
    private $name;
    private $clocks = array();

    const TABLE_COUNTRY = 'pays';
    const COL_ID        = 'id_pays';
    const COL_NAME      = 'nom_pays';

    /**
     * @param int $id
     * @param string $name
     */
    public function __construct($id, $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
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
     * @return Clock[]
     */
    public function getClocks()
    {
        return $this->clocks;
    }

    /**
     * @param Clock[] $clocks
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
    public function toOption()
    {
        return "<option value=\"{$this->id}\">{$this->name}</option>";
    }
}