<?php
namespace timezone\entities;

class Timezone
{
    private $id;
    private $name;
    private $offset;
    private $clocks = array();

    const TABLE_TIMEZONE = 'fuseau';
    const COL_ID        = 'id_fuseau';
    const COL_NAME      = 'nom_fuseau';
    const COL_OFFSET    = 'decalage_fuseau';

    /**
     * @param int $id
     * @param string $name
     * @param string $offset
     *
     * @return Timezone
     */
    public static function withId($id, $name, $offset)
    {
        $timezone = new Timezone();
        $timezone->setId($id);
        $timezone->setName($name);
        $timezone->setOffset($offset);

        return $timezone;
    }

    /**
     * @param string $name
     * @param string $offset
     *
     * @return Timezone
     */
    public static function withoutId($name, $offset)
    {
        $timezone = new Timezone();
        $timezone->setName($name);
        $timezone->setOffset($offset);

        return $timezone;
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