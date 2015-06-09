<?php
namespace timezone\entities;

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
     * @param $town
     * @param Country $country
     * @param Timezone $timezone
     *
     * @return Clock
     */
    public static function withoutId($town, Country $country, Timezone $timezone)
    {
        $clock = new Clock();
        $clock->setTown($town);
        $clock->setCountry($country);
        $clock->setTimezone($timezone);

        return $clock;
    }

    /**
     * @param $id
     * @param $town
     * @param Country $country
     * @param Timezone $timezone
     *
     * @return Clock
     */
    public static function withId($id, $town, Country $country, Timezone $timezone)
    {
        $clock = new Clock();
        $clock->setId($id);
        $clock->setTown($town);
        $clock->setCountry($country);
        $clock->setTimezone($timezone);

        return $clock;
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