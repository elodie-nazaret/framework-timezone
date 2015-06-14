<?php
namespace timezone\entities;

/**
 * Class Clock
 */
class Clock
{
    private $id;
    private $town;
    private $country;
    private $timezone;
    private $views = array();

    const TABLE_CLOCK   = 'horloge';
    const COL_ID        = 'id_horloge';
    const COL_TOWN      = 'ville_horloge';
    const COL_COUNTRY   = 'pays_horloge';
    const COL_TIMEZONE  = 'fuseau_horloge';

    /**
     * @param int $id
     * @param string $town
     */
    public function __construct($id, $town)
    {
        $this->id   = $id;
        $this->town = $town;
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
    public function getTown()
    {
        return $this->town;
    }

    /**
     * @param string $town
     */
    public function setTown($town)
    {
        $this->town = $town;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param Country $country
     */
    public function setCountry(Country $country)
    {
        $this->country = $country;
        $country->addClock($this);
    }

    /**
         * @return Timezone
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param Timezone $timezone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
        $timezone->addClock($this);
    }

    /**
     * @return array
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param array $views
     */
    public function setViews($views)
    {
        $this->views = $views;
    }

    /**
     * @param View $view
     */
    public function addView(View $view)
    {
        $this->views[] = $view;
    }

    /**
     * @param View $view
     */
    public function removeView(View $view) {
        $id = array_search($view, $this->views);
        if ($id !== false) {
            array_splice($this->views, $id, 1);
        }
    }
    
}